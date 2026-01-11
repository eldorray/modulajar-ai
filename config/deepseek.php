<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DeepSeek API Configuration
    |--------------------------------------------------------------------------
    */

    'api_key' => env('DEEPSEEK_API_KEY'),
    
    'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
    
    'endpoint' => 'https://api.deepseek.com/v1/chat/completions',
];
