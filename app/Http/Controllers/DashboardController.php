<?php

namespace App\Http\Controllers;

use App\Models\Rpp;
use App\Models\AiUsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // DeepSeek Pricing (per 1 million tokens in USD)
    // Model: deepseek-chat (DeepSeek-V3.2 Non-thinking Mode)
    // https://platform.deepseek.com/api-docs/pricing/
    const DEEPSEEK_PRICE_INPUT_CACHE_HIT = 0.028;    // $0.028 per 1M input tokens (cache hit)
    const DEEPSEEK_PRICE_INPUT_CACHE_MISS = 0.28;    // $0.28 per 1M input tokens (cache miss)
    const DEEPSEEK_PRICE_OUTPUT_PER_MILLION = 0.42;  // $0.42 per 1M output tokens

    // Gemini 2.5 Flash Pricing (per 1 million tokens in USD)
    const GEMINI_PRICE_INPUT_PER_MILLION = 0.30;   // $0.30 per 1M input tokens
    const GEMINI_PRICE_OUTPUT_PER_MILLION = 2.50;  // $2.50 per 1M output tokens
    
    const USD_TO_IDR = 16717;               // Exchange rate USD to IDR

    /**
     * Get current AI provider info.
     */
    private function getProviderInfo(): array
    {
        $provider = config('ai.default', 'gemini');
        
        return match ($provider) {
            'deepseek' => [
                'name' => 'DeepSeek Chat (V3.2)',
                'input_price' => self::DEEPSEEK_PRICE_INPUT_CACHE_MISS, // Using cache miss price as default
                'input_price_cache_hit' => self::DEEPSEEK_PRICE_INPUT_CACHE_HIT,
                'output_price' => self::DEEPSEEK_PRICE_OUTPUT_PER_MILLION,
            ],
            default => [
                'name' => 'Gemini 2.5 Flash',
                'input_price' => self::GEMINI_PRICE_INPUT_PER_MILLION,
                'input_price_cache_hit' => null,
                'output_price' => self::GEMINI_PRICE_OUTPUT_PER_MILLION,
            ],
        };
    }

    /**
     * Calculate token cost in Rupiah.
     */
    private function calculateTokenCost(int $inputTokens, int $outputTokens): array
    {
        $providerInfo = $this->getProviderInfo();
        
        // Calculate cost in USD
        $inputCostUsd = ($inputTokens / 1000000) * $providerInfo['input_price'];
        $outputCostUsd = ($outputTokens / 1000000) * $providerInfo['output_price'];
        $totalCostUsd = $inputCostUsd + $outputCostUsd;

        // Convert to IDR
        $inputCostIdr = $inputCostUsd * self::USD_TO_IDR;
        $outputCostIdr = $outputCostUsd * self::USD_TO_IDR;
        $totalCostIdr = $totalCostUsd * self::USD_TO_IDR;

        return [
            'input_cost_usd' => $inputCostUsd,
            'output_cost_usd' => $outputCostUsd,
            'total_cost_usd' => $totalCostUsd,
            'input_cost_idr' => $inputCostIdr,
            'output_cost_idr' => $outputCostIdr,
            'total_cost_idr' => $totalCostIdr,
            'provider' => $providerInfo,
        ];
    }

    /**
     * Calculate token cost for specific provider.
     */
    private function calculateTokenCostForProvider(int $inputTokens, int $outputTokens, string $provider): array
    {
        $providerInfo = match ($provider) {
            'deepseek' => [
                'name' => 'DeepSeek Chat',
                'input_price' => self::DEEPSEEK_PRICE_INPUT_CACHE_MISS,
                'output_price' => self::DEEPSEEK_PRICE_OUTPUT_PER_MILLION,
            ],
            default => [
                'name' => 'Gemini 2.5 Flash',
                'input_price' => self::GEMINI_PRICE_INPUT_PER_MILLION,
                'output_price' => self::GEMINI_PRICE_OUTPUT_PER_MILLION,
            ],
        };
        
        // Calculate cost in USD
        $inputCostUsd = ($inputTokens / 1000000) * $providerInfo['input_price'];
        $outputCostUsd = ($outputTokens / 1000000) * $providerInfo['output_price'];
        $totalCostUsd = $inputCostUsd + $outputCostUsd;

        // Convert to IDR
        $totalCostIdr = $totalCostUsd * self::USD_TO_IDR;

        return [
            'total_cost_usd' => $totalCostUsd,
            'total_cost_idr' => $totalCostIdr,
            'provider' => $providerInfo,
        ];
    }

    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's RPP statistics
        $stats = [
            'total_rpp' => Rpp::forUser($user->id)->count(),
            'completed_rpp' => Rpp::forUser($user->id)->where('status', 'completed')->count(),
            'processing_rpp' => Rpp::forUser($user->id)->where('status', 'processing')->count(),
            'failed_rpp' => Rpp::forUser($user->id)->where('status', 'failed')->count(),
        ];

        // Get user's token usage
        $userTokens = AiUsageLog::where('user_id', $user->id)->selectRaw('
            COALESCE(SUM(input_tokens), 0) as input_tokens,
            COALESCE(SUM(output_tokens), 0) as output_tokens,
            COALESCE(SUM(total_tokens), 0) as total_tokens
        ')->first();

        $userCost = $this->calculateTokenCost(
            $userTokens->input_tokens ?? 0,
            $userTokens->output_tokens ?? 0
        );

        // Get recent RPPs
        $recentRpps = Rpp::forUser($user->id)
            ->latest()
            ->limit(5)
            ->get();

        // Admin stats
        $adminStats = null;
        if ($user->isAdmin()) {
            // Get tokens by provider (using deepseek and google as logged)
            $deepseekTokens = AiUsageLog::where('provider', 'deepseek')->selectRaw('
                COALESCE(SUM(input_tokens), 0) as input_tokens,
                COALESCE(SUM(output_tokens), 0) as output_tokens,
                COALESCE(SUM(total_tokens), 0) as total_tokens
            ')->first();

            $geminiTokens = AiUsageLog::whereIn('provider', ['gemini', 'google'])->selectRaw('
                COALESCE(SUM(input_tokens), 0) as input_tokens,
                COALESCE(SUM(output_tokens), 0) as output_tokens,
                COALESCE(SUM(total_tokens), 0) as total_tokens
            ')->first();

            // Calculate costs for each provider
            $deepseekCost = $this->calculateTokenCostForProvider(
                $deepseekTokens->input_tokens ?? 0,
                $deepseekTokens->output_tokens ?? 0,
                'deepseek'
            );

            $geminiCost = $this->calculateTokenCostForProvider(
                $geminiTokens->input_tokens ?? 0,
                $geminiTokens->output_tokens ?? 0,
                'gemini'
            );

            $adminStats = [
                'total_users' => \App\Models\User::count(),
                'total_rpps' => Rpp::count(),
                'deepseek' => [
                    'input_tokens' => $deepseekTokens->input_tokens ?? 0,
                    'output_tokens' => $deepseekTokens->output_tokens ?? 0,
                    'total_tokens' => $deepseekTokens->total_tokens ?? 0,
                    'cost' => $deepseekCost,
                ],
                'gemini' => [
                    'input_tokens' => $geminiTokens->input_tokens ?? 0,
                    'output_tokens' => $geminiTokens->output_tokens ?? 0,
                    'total_tokens' => $geminiTokens->total_tokens ?? 0,
                    'cost' => $geminiCost,
                ],
                'total_cost_idr' => $deepseekCost['total_cost_idr'] + $geminiCost['total_cost_idr'],
            ];
        }

        return view('dashboard', compact('stats', 'recentRpps', 'adminStats', 'userTokens', 'userCost'));
    }
}

