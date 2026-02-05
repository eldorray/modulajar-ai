<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected ?string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('groq.api_key');
        $this->model = config('groq.model', 'llama-3.2-90b-vision-preview');
        $this->endpoint = config('groq.endpoint', 'https://api.groq.com/openai/v1/chat/completions');
    }

    /**
     * Analyze LJK image and extract answers
     */
    public function analyzeJawaban(string $base64Image, int $jumlahSoal, int $jumlahPilihan): array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'error' => 'Groq API key tidak dikonfigurasi.',
            ];
        }

        $optionLabels = array_slice(['A', 'B', 'C', 'D', 'E'], 0, $jumlahPilihan);
        $optionsStr = implode(', ', $optionLabels);

        // Build prompt for LJK analysis - very specific instructions
        $rowsPerColumn = (int) ceil($jumlahSoal / 5);
        $col2Start = $rowsPerColumn + 1;
        $col2End = $rowsPerColumn * 2;
        $col3Start = $rowsPerColumn * 2 + 1;
        $col3End = $rowsPerColumn * 3;
        $col4Start = $rowsPerColumn * 3 + 1;
        $col4End = $rowsPerColumn * 4;
        $col5Start = $rowsPerColumn * 4 + 1;
        
        $prompt = <<<PROMPT
Analyze this Indonesian LJK (Answer Sheet) photo.

CRITICAL: The BLACKENED/FILLED/DARK boxes are the student's answers. White/empty boxes are NOT selected.

STRUCTURE in the "JAWABAN" section:
- 5 columns, {$rowsPerColumn} rows each
- Col 1: Q1-{$rowsPerColumn}, Col 2: Q{$col2Start}-{$col2End}, Col 3: Q{$col3Start}-{$col3End}, Col 4: Q{$col4Start}-{$col4End}, Col 5: Q{$col5Start}-{$jumlahSoal}
- Format: [Number] [A] [B] [C] [D] where filled box = answer

For EACH question 1 to {$jumlahSoal}:
1. Find the row with that question number
2. Look for the BLACK/DARK/FILLED box among A, B, C, D
3. That letter is the answer

Return ONLY valid JSON:
{"answers":["B","C","D","A","B","C","D","A","B","C","D","A","B","C","D","A","B","C","D","A"],"confidence":0.9}

Rules:
- Exactly {$jumlahSoal} answers in sequential order (Q1, Q2, Q3...)
- Only use: {$optionsStr} or null
- BLACK box = selected answer
- WHITE box = not selected
PROMPT;

        try {
            // Remove data:image/... prefix if present
            $imageData = $base64Image;
            if (str_contains($base64Image, ',')) {
                $imageData = explode(',', $base64Image)[1];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $prompt,
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => 'data:image/jpeg;base64,' . $imageData,
                                ],
                            ],
                        ],
                    ],
                ],
                'temperature' => 0.1,
                'max_tokens' => 1024,
            ]);

            if ($response->failed()) {
                Log::error('Groq API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                $errorMessage = match ($response->status()) {
                    429 => 'Batas kuota API tercapai. Silakan tunggu beberapa saat.',
                    401 => 'API Key tidak valid.',
                    403 => 'Akses ditolak.',
                    413 => 'Gambar terlalu besar. Maksimal 4MB.',
                    500, 503 => 'Server AI tidak tersedia. Coba lagi nanti.',
                    default => 'Gagal menganalisis gambar. Status: ' . $response->status(),
                };

                return [
                    'success' => false,
                    'error' => $errorMessage,
                ];
            }

            $result = $response->json();
            $content = $result['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                return [
                    'success' => false,
                    'error' => 'Tidak ada respons dari AI.',
                ];
            }

            // Parse JSON from response
            $parsed = $this->extractJson($content);

            if (!$parsed || !isset($parsed['answers'])) {
                Log::warning('Failed to parse Groq response', ['content' => $content]);
                return [
                    'success' => false,
                    'error' => 'Gagal memproses respons AI.',
                    'raw' => $content,
                ];
            }

            return [
                'success' => true,
                'answers' => $parsed['answers'],
                'confidence' => $parsed['confidence'] ?? 0.5,
            ];

        } catch (\Exception $e) {
            Log::error('Groq Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Extract JSON from AI response (may contain markdown code blocks)
     */
    protected function extractJson(string $content): ?array
    {
        // Try direct parse first
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Try extracting from code block
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $matches)) {
            $decoded = json_decode(trim($matches[1]), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // Try finding JSON object pattern
        if (preg_match('/\{[\s\S]*"answers"[\s\S]*\}/', $content, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return null;
    }
}
