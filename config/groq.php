<?php

return [
    'api_key' => env('GROQ_API_KEY'),
    'model' => env('GROQ_VISION_MODEL', 'meta-llama/llama-4-maverick-17b-128e-instruct'),
    'endpoint' => env('GROQ_ENDPOINT', 'https://api.groq.com/openai/v1/chat/completions'),
];
