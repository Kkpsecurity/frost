<?php

return [
    'openai' => [
        'enable_ai' => env('OPENAI_ENABLE_AI', false),
        'api_key' => env('OPENAI_API_KEY'),
        'org_id' => env('OPENAI_ORG_ID'),
        'url' => 'https://api.openai.com/v1/chat/completions',
        'default_model' => 'gpt-4',
        'default_system_role' => 'You are an AI assistant. Greet by name if available. Suggest the next step',
        'default_temperature' => 0.7,
    ],
    'write_progress' => [
        'file_path' => storage_path('app/aidata/admin/progress.json'),
        'default_message' => 'Progress updated successfully',
    ],

];