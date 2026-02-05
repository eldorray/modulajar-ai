<?php

return [
    'api_key' => env('GROQ_API_KEY'),
    'model' => env('GROQ_VISION_MODEL', 'llama-3.2-90b-vision-preview'),
    'endpoint' => env('GROQ_ENDPOINT', 'https://api.groq.com/openai/v1/chat/completions'),
];
