<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

final class ApiResponse
{
    public function success(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json(['data' => $this->resolve($data)], $status);
    }

    public function paginated(LengthAwarePaginator $paginator, ?JsonResource $resource = null): JsonResponse
    {
        $data = $resource?->resolve(request()) ?? $paginator->items();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }

    /** @param array<string, list<string>>|null $errors */
    public function error(
        string $message,
        ApiErrorCode $code,
        int $status,
        ?array $errors = null,
        array $extra = [],
    ): JsonResponse {
        $payload = ['message' => $message, 'code' => $code->value];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json([...$payload, ...$extra], $status);
    }

    private function resolve(mixed $data): mixed
    {
        return $data instanceof JsonResource ? $data->resolve(request()) : $data;
    }
}
