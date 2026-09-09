<?php

namespace App\Support;

enum ApiErrorCode: string
{
    case Unauthenticated = 'UNAUTHENTICATED';
    case Forbidden = 'FORBIDDEN';
    case NotFound = 'NOT_FOUND';
    case MethodNotAllowed = 'METHOD_NOT_ALLOWED';
    case ValidationFailed = 'VALIDATION_FAILED';
    case InvalidCredentials = 'INVALID_CREDENTIALS';
    case IdempotencyConflict = 'IDEMPOTENCY_CONFLICT';
    case ResourceNotReady = 'RESOURCE_NOT_READY';
    case RateLimited = 'RATE_LIMITED';
    case InternalError = 'INTERNAL_ERROR';
}
