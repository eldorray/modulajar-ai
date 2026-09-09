<?php

namespace App\Jobs;

use App\Models\Rpp;
use App\Services\DeepSeekService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GenerateRpp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 360;

    public bool $failOnTimeout = true;

    public function __construct(public readonly int $rppId) {}

    /** @return list<object> */
    public function middleware(): array
    {
        return [(new WithoutOverlapping("rpp:{$this->rppId}"))->expireAfter(420)];
    }

    public function handle(DeepSeekService $service): void
    {
        $rpp = Rpp::query()->find($this->rppId);
        if (! $rpp || $rpp->status !== 'processing') {
            return;
        }

        Rpp::query()->whereKey($rpp->id)->where('status', 'processing')->update(['started_at' => now()]);
        $result = $service->generateRPP($rpp->generation_input ?? [], $rpp->user_id, $rpp->id);

        if (($result['success'] ?? false) && ! empty($result['content'])) {
            Rpp::query()->whereKey($rpp->id)->where('status', 'processing')->update([
                'content_result' => $result['content'],
                'status' => 'completed',
                'failure_code' => null,
                'failure_message' => null,
                'completed_at' => now(),
            ]);

            return;
        }

        $this->markFailed();
    }

    public function failed(?Throwable $exception): void
    {
        $this->markFailed();
    }

    private function markFailed(?string $message = null): void
    {
        Rpp::query()->whereKey($this->rppId)->where('status', 'processing')->update([
            'status' => 'failed',
            'failure_code' => 'AI_GENERATION_FAILED',
            'failure_message' => $message ?: 'RPP gagal dibuat. Silakan coba lagi.',
            'failed_at' => now(),
        ]);
    }
}
