<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\IntelliReply\Models\Document;
use Modules\IntelliReply\Services\RagService;
use App\Models\Setting;
use App\Models\Organization;

class MigrateDocumentsToQdrant extends Command
{
    protected $signature = 'rag:migrate {--org= : Specific organization ID to migrate}';
    protected $description = 'Migrate existing documents with embeddings into Qdrant vector database';

    public function handle()
    {
        $ragService = new RagService();
        $orgId = $this->option('org');

        $query = Document::where('status', 'Complete')->whereNotNull('embeddings');
        if ($orgId) {
            $query->where('organization_id', $orgId);
        }

        $documents = $query->get();
        $this->info("Found {$documents->count()} documents to migrate.");

        $settings = Setting::whereIn('key', [
            'default_open_ai_key',
            'enable_api_key_input',
        ])->pluck('value', 'key');
        $enableApiKeyInput = $settings->get('enable_api_key_input', 1);
        $defaultOpenAiKey = $settings->get('default_open_ai_key', '');

        $bar = $this->output->createProgressBar($documents->count());
        $bar->start();

        $migrated = 0;
        $failed = 0;

        foreach ($documents as $document) {
            try {
                $org = Organization::find($document->organization_id);
                $orgMeta = $org && $org->metadata ? json_decode($org->metadata, true) : [];
                $apiKey = $enableApiKeyInput == 1
                    ? ($orgMeta['ai']['api_key'] ?? $defaultOpenAiKey)
                    : $defaultOpenAiKey;

                if (!$apiKey) {
                    $this->newLine();
                    $this->warn("Skipping doc {$document->id}: No API key for org {$document->organization_id}");
                    $failed++;
                    $bar->advance();
                    continue;
                }

                // Re-chunk with the improved chunking strategy
                $processedContent = $ragService->preprocessText($document->content);
                $chunks = $ragService->chunkText($processedContent);

                // Generate fresh embeddings for the new chunks
                $embeddings = $ragService->generateEmbeddings($apiKey, $chunks);

                // Index into Qdrant
                $ragService->indexDocument($document->organization_id, $document->id, $chunks, $embeddings);

                // Update DB embeddings too
                $document->embeddings = json_encode($embeddings);
                $document->save();

                $migrated++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed doc {$document->id}: {$e->getMessage()}");
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Migration complete: {$migrated} migrated, {$failed} failed.");

        return 0;
    }
}
