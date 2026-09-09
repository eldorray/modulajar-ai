<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\RppResource;
use App\Models\Rpp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $query = Rpp::query()->where('user_id', $request->user()->id);
        $counts = (clone $query)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return api_response()->success([
            'stats' => [
                'total' => (int) $counts->sum(),
                'completed' => (int) ($counts['completed'] ?? 0),
                'processing' => (int) ($counts['processing'] ?? 0),
                'failed' => (int) ($counts['failed'] ?? 0),
                'draft' => (int) ($counts['draft'] ?? 0),
            ],
            'recent_rpps' => RppResource::collection((clone $query)->latest()->limit(5)->get())->resolve($request),
        ]);
    }
}
