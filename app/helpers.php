<?php

use App\Support\ApiResponse;

if (! function_exists('api_response')) {
    function api_response(): ApiResponse
    {
        return app(ApiResponse::class);
    }
}
