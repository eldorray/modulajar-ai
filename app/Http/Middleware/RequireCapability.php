<?php

namespace App\Http\Middleware;

use App\Support\ApiCapabilities;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireCapability
{
    public function handle(Request $r, Closure $n, string $c): Response
    {
        if (! $r->user() || ! in_array($c, ApiCapabilities::for($r->user()), true)) {
            throw new AuthorizationException('Forbidden.');
        }

        return $n($r);
    }
}
