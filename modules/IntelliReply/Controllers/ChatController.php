<?php

namespace Modules\IntelliReply\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Organization;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Modules\IntelliReply\Services\RagService;

class ChatController extends BaseController
{
    public function chat(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::find($organizationId);
        $metadata = $organization?->metadata ? json_decode($organization->metadata, true) : [];
        $aiMetadata = $metadata['ai'] ?? [];

        $settings = Setting::whereIn('key', [
            'default_open_ai_key',
            'default_open_ai_model',
            'enable_model_selection',
            'enable_api_key_input',
        ])->pluck('value', 'key');

        $enableApiKeyInput = $settings->get('enable_api_key_input', 1);
        $defaultOpenAiKey = $settings->get('default_open_ai_key', $aiMetadata['api_key'] ?? '');
        $enableModelSelection = $settings->get('enable_model_selection', 1);
        $defaultOpenAiModel = $settings->get('default_open_ai_model', $aiMetadata['model'] ?? '');

        $apiKey = $enableApiKeyInput == 1 ? ($aiMetadata['api_key'] ?? '') : $defaultOpenAiKey;
        $model = $enableModelSelection == 1 ? ($aiMetadata['model'] ?? '') : $defaultOpenAiModel;
        $maxTokens = (int) ($aiMetadata['max_tokens'] ?? 1000);
        $temperature = (float) ($aiMetadata['temperature'] ?? 0.7);

        $query = $request->input('query');

        // Use RagService for proper vector search via Qdrant
        $ragService = new RagService();
        $ragResult = $ragService->retrieveContext($organizationId, $apiKey, $query, 5);

        $messages = [];

        if ($ragResult['success']) {
            $messages[] = ['role' => 'system', 'content' => $ragResult['system_prompt']];
        } else {
            $messages[] = ['role' => 'system', 'content' => 'You are a customer support AI assistant. If you don\'t have information about the question, say so.'];
        }

        $messages[] = ['role' => 'user', 'content' => $query];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ]);

        $result = $response->json();

        $text = $result['choices'][0]['message']['content']
            ?? 'Sorry but I don\'t have any information about this.';

        return response()->json(['response' => $text]);
    }
}
