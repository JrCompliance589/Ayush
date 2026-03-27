<?php

namespace Modules\IntelliReply\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI;

class RagService
{
    private string $qdrantHost;
    private ?string $qdrantApiKey;
    private string $embeddingModel = 'text-embedding-3-small';
    private int $embeddingDimension = 1536;

    // Chunking config
    private int $chunkSize = 600;     // ~600 tokens per chunk
    private int $chunkOverlap = 100;  // ~100 tokens overlap

    public function __construct()
    {
        $this->qdrantHost = rtrim(config('services.qdrant.host', 'http://localhost:6333'), '/');
        $this->qdrantApiKey = config('services.qdrant.api_key') ?: null;
    }

    /**
     * Get the collection name scoped to an organization.
     */
    private function collectionName(int $organizationId): string
    {
        return "org_{$organizationId}_documents";
    }

    /**
     * Build HTTP client for Qdrant API calls.
     */
    private function qdrantClient(): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::baseUrl($this->qdrantHost)
            ->timeout(30)
            ->acceptJson();

        if ($this->qdrantApiKey) {
            $client = $client->withHeaders(['api-key' => $this->qdrantApiKey]);
        }

        return $client;
    }

    /**
     * Ensure the Qdrant collection exists for this organization.
     */
    public function ensureCollection(int $organizationId): void
    {
        $name = $this->collectionName($organizationId);

        Log::channel('rag')->info("[QDRANT REQUEST] GET /collections/{$name} — Check if collection exists");
        $check = $this->qdrantClient()->get("/collections/{$name}");
        Log::channel('rag')->info("[QDRANT RESPONSE] GET /collections/{$name}", [
            'status' => $check->status(),
            'exists' => $check->successful(),
        ]);

        if ($check->successful()) {
            return;
        }

        $payload = ['vectors' => ['size' => $this->embeddingDimension, 'distance' => 'Cosine']];
        Log::channel('rag')->info("[QDRANT REQUEST] PUT /collections/{$name} — Create collection", $payload);
        $response = $this->qdrantClient()->put("/collections/{$name}", $payload);
        Log::channel('rag')->info("[QDRANT RESPONSE] PUT /collections/{$name}", [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if (!$response->successful()) {
            Log::error("RagService: Failed to create Qdrant collection {$name}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException("Failed to create Qdrant collection: {$response->body()}");
        }

        Log::channel('rag')->info("[QDRANT] Collection created: {$name}");
    }

    /**
     * Preprocess raw text: clean whitespace, normalize.
     */
    public function preprocessText(string $text): string
    {
        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Remove excessive whitespace/blank lines
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        // Trim leading/trailing whitespace per line
        $lines = array_map('trim', explode("\n", $text));
        $text = implode("\n", $lines);

        return trim($text);
    }

    /**
     * Split text into overlapping chunks based on approximate token count.
     * Uses word-based splitting (~0.75 words per token approximation).
     */
    public function chunkText(string $text): array
    {
        $words = preg_split('/\s+/', $text);
        $totalWords = count($words);

        if ($totalWords === 0) {
            return [];
        }

        // Approximate: 1 token ≈ 0.75 words, so multiply token counts by 0.75
        $chunkWords = (int) round($this->chunkSize * 0.75);
        $overlapWords = (int) round($this->chunkOverlap * 0.75);
        $stepWords = max(1, $chunkWords - $overlapWords);

        $chunks = [];
        $offset = 0;

        while ($offset < $totalWords) {
            $slice = array_slice($words, $offset, $chunkWords);
            $chunkText = implode(' ', $slice);

            if (strlen(trim($chunkText)) > 20) { // Skip tiny chunks
                $chunks[] = trim($chunkText);
            }

            $offset += $stepWords;
        }

        return $chunks;
    }

    /**
     * Generate embeddings for an array of text chunks using OpenAI.
     */
    public function generateEmbeddings(string $apiKey, array $chunks): array
    {
        $client = OpenAI::client($apiKey);
        $embeddings = [];

        // Batch up to 20 chunks at a time for efficiency
        $batches = array_chunk($chunks, 20);

        foreach ($batches as $batch) {
            $response = $client->embeddings()->create([
                'input' => $batch,
                'model' => $this->embeddingModel,
            ]);

            foreach ($response->embeddings as $embedding) {
                $embeddings[] = $embedding->embedding;
            }
        }

        return $embeddings;
    }

    /**
     * Generate a single embedding for a query string.
     */
    public function generateQueryEmbedding(string $apiKey, string $query): array
    {
        $client = OpenAI::client($apiKey);

        $response = $client->embeddings()->create([
            'input' => $query,
            'model' => $this->embeddingModel,
        ]);

        return $response->embeddings[0]->embedding;
    }

    /**
     * Index document chunks into Qdrant with metadata.
     */
    public function indexDocument(int $organizationId, int $documentId, array $chunks, array $embeddings): void
    {
        $this->ensureCollection($organizationId);
        $collectionName = $this->collectionName($organizationId);

        // First, delete any existing points for this document
        $this->deleteDocumentPoints($organizationId, $documentId);

        // Prepare points for upsert
        $points = [];
        foreach ($chunks as $index => $chunkText) {
            if (!isset($embeddings[$index])) {
                continue;
            }

            $points[] = [
                'id' => $this->pointId($documentId, $index),
                'vector' => $embeddings[$index],
                'payload' => [
                    'text' => $chunkText,
                    'document_id' => $documentId,
                    'chunk_index' => $index,
                    'organization_id' => $organizationId,
                ],
            ];
        }

        Log::channel('rag')->info("[QDRANT] Upserting " . count($points) . " points for doc {$documentId}", [
            'collection' => $collectionName,
            'chunks_preview' => collect($points)->map(fn($p) => [
                'id' => $p['id'],
                'chunk_index' => $p['payload']['chunk_index'],
                'text_preview' => mb_substr($p['payload']['text'], 0, 120) . '...',
            ])->toArray(),
        ]);

        // Batch upsert in groups of 100
        $batches = array_chunk($points, 100);
        foreach ($batches as $batchIndex => $batch) {
            Log::channel('rag')->info("[QDRANT REQUEST] PUT /collections/{$collectionName}/points — Batch " . ($batchIndex + 1) . ", " . count($batch) . " points");
            $response = $this->qdrantClient()->put("/collections/{$collectionName}/points", [
                'points' => $batch,
            ]);
            Log::channel('rag')->info("[QDRANT RESPONSE] PUT /collections/{$collectionName}/points", [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if (!$response->successful()) {
                Log::error("RagService: Failed to upsert points to Qdrant", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \RuntimeException("Failed to index document in Qdrant: {$response->body()}");
            }
        }

        Log::channel('rag')->info("[QDRANT] Indexing complete: doc {$documentId}, " . count($points) . " chunks, org {$organizationId}");
    }

    /**
     * Generate a deterministic point ID from document_id and chunk_index.
     */
    private function pointId(int $documentId, int $chunkIndex): int
    {
        // Use a formula that gives unique IDs: documentId * 100000 + chunkIndex
        // This supports up to 100k chunks per document
        return ($documentId * 100000) + $chunkIndex;
    }

    /**
     * Delete all Qdrant points for a specific document.
     */
    public function deleteDocumentPoints(int $organizationId, int $documentId): void
    {
        $collectionName = $this->collectionName($organizationId);

        $check = $this->qdrantClient()->get("/collections/{$collectionName}");
        if (!$check->successful()) {
            Log::channel('rag')->info("[QDRANT] Delete skipped — collection {$collectionName} does not exist");
            return;
        }

        $filter = [
            'filter' => [
                'must' => [
                    ['key' => 'document_id', 'match' => ['value' => $documentId]],
                ],
            ],
        ];
        Log::channel('rag')->info("[QDRANT REQUEST] POST /collections/{$collectionName}/points/delete", [
            'document_id' => $documentId,
            'filter' => $filter,
        ]);

        $response = $this->qdrantClient()->post("/collections/{$collectionName}/points/delete", $filter);
        Log::channel('rag')->info("[QDRANT RESPONSE] DELETE points", [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if (!$response->successful()) {
            Log::warning("RagService: Failed to delete document points from Qdrant", [
                'document_id' => $documentId,
                'body' => $response->body(),
            ]);
        }
    }

    /**
     * Search Qdrant for the top-K most similar chunks to the given query.
     */
    public function searchSimilar(int $organizationId, array $queryEmbedding, int $topK = 3): array
    {
        $collectionName = $this->collectionName($organizationId);

        $check = $this->qdrantClient()->get("/collections/{$collectionName}");
        if (!$check->successful()) {
            Log::channel('rag')->info("[QDRANT] Search skipped — collection {$collectionName} not found");
            return [];
        }

        Log::channel('rag')->info("[QDRANT REQUEST] POST /collections/{$collectionName}/points/search", [
            'limit' => $topK,
            'embedding_dimensions' => count($queryEmbedding),
            'embedding_first_5' => array_slice($queryEmbedding, 0, 5),
        ]);

        $response = $this->qdrantClient()->post("/collections/{$collectionName}/points/search", [
            'vector' => $queryEmbedding,
            'limit' => $topK,
            'with_payload' => true,
        ]);

        Log::channel('rag')->info("[QDRANT RESPONSE] Search results", [
            'status' => $response->status(),
            'total_points_returned' => count($response->json('result', [])),
        ]);

        if (!$response->successful()) {
            Log::error("RagService: Qdrant search failed for org {$organizationId}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        $results = $response->json('result', []);

        $mapped = array_map(function ($point) {
            return [
                'text' => $point['payload']['text'] ?? '',
                'document_id' => $point['payload']['document_id'] ?? null,
                'chunk_index' => $point['payload']['chunk_index'] ?? null,
                'score' => $point['score'] ?? 0,
            ];
        }, $results);

        // Log raw scores before filtering
        Log::channel('rag')->info('[QDRANT] Raw scores before threshold filter', [
            'scores' => array_map(fn($c) => [
                'score' => $c['score'],
                'doc_id' => $c['document_id'],
                'chunk_index' => $c['chunk_index'],
            ], $mapped),
        ]);

        // Filter out very low-confidence matches
        $mapped = array_values(array_filter($mapped, fn($c) => $c['score'] >= 0.10));

        // Log each result with cosine similarity score and matched text
        Log::channel('rag')->info('=== COSINE SIMILARITY RESULTS ===');
        foreach ($mapped as $i => $chunk) {
            $rank = $i + 1;
            Log::channel('rag')->info("[SIMILARITY #{$rank}] score={$chunk['score']} | doc_id={$chunk['document_id']} | chunk_index={$chunk['chunk_index']}", [
                'cosine_score' => $chunk['score'],
                'document_id' => $chunk['document_id'],
                'chunk_index' => $chunk['chunk_index'],
                'matched_text' => mb_substr($chunk['text'], 0, 300),
            ]);
        }
        Log::channel('rag')->info('=== END SIMILARITY RESULTS ===');

        return $mapped;
    }

    /**
     * Build a context string from retrieved chunks for the LLM prompt.
     */
    public function buildContext(array $chunks): string
    {
        if (empty($chunks)) {
            return '';
        }

        $context = '';
        foreach ($chunks as $i => $chunk) {
            $num = $i + 1;
            $context .= "[Chunk {$num}]\n{$chunk['text']}\n\n";
        }

        return trim($context);
    }

    /**
     * Build the strict RAG system prompt that prevents hallucination.
     */
    public function buildRagSystemPrompt(string $context): string
    {
        return <<<EOT
You are a customer support AI assistant. Answer the user's question using ONLY the context provided below.

STRICT RULES:
1. Use ONLY the information from the provided context to answer.
2. If the answer is not found in the context, respond: "I don't have information about that in my knowledge base. Could you rephrase or ask about something else?"
3. Do NOT use any external knowledge, assumptions, or information not present in the context.
4. If the question is a greeting (e.g., "hello", "hi", "thank you"), respond politely.
5. Keep answers concise and helpful.
6. If the context only partially answers the question, provide what you can and mention that more details may not be available.
7. Always respond in the same language as the user's message.

CONTEXT:
{$context}
EOT;
    }

    /**
     * Full RAG pipeline: query → embed → search → context → system prompt.
     */
    public function retrieveContext(int $organizationId, string $apiKey, string $query, int $topK = 3): array
    {
        Log::channel('rag')->info('========== RAG PIPELINE START ==========');
        Log::channel('rag')->info('[RAG] Query received', [
            'org_id' => $organizationId,
            'query' => $query,
            'top_k' => $topK,
        ]);

        Log::channel('rag')->info('[RAG] Generating query embedding via OpenAI...');
        $queryEmbedding = $this->generateQueryEmbedding($apiKey, $query);
        Log::channel('rag')->info('[RAG] Embedding generated', [
            'dimensions' => count($queryEmbedding),
        ]);

        $chunks = $this->searchSimilar($organizationId, $queryEmbedding, $topK);

        if (empty($chunks)) {
            Log::channel('rag')->info('[RAG] No matching chunks found — returning empty context');
            Log::channel('rag')->info('========== RAG PIPELINE END (no results) ==========');
            return [
                'success' => false,
                'context' => '',
                'chunks' => [],
            ];
        }

        $context = $this->buildContext($chunks);

        Log::channel('rag')->info('[RAG] Context built from ' . count($chunks) . ' chunks', [
            'context_length' => strlen($context),
            'context_preview' => mb_substr($context, 0, 500) . '...',
        ]);
        Log::channel('rag')->info('========== RAG PIPELINE END (success) ==========');

        return [
            'success' => true,
            'context' => $context,
            'chunks' => $chunks,
            'system_prompt' => $this->buildRagSystemPrompt($context),
        ];
    }
}
