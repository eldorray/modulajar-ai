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

        // Build prompt for LJK analysis
        $prompt = <<<PROMPT
Kamu adalah sistem OCR untuk membaca Lembar Jawaban Komputer (LJK) Indonesia.

Gambar ini adalah foto LJK yang sudah diisi siswa. Pada bagian "JAWABAN", terdapat grid jawaban dengan {$jumlahSoal} soal.
Setiap soal memiliki pilihan {$optionsStr}. Jawaban yang dipilih siswa ditandai dengan kotak yang dihitamkan/diarsir.

STRUKTUR GRID:
- Grid jawaban disusun dalam 5 kolom
- Kolom 1: soal 1-4, Kolom 2: soal 5-8, Kolom 3: soal 9-12, Kolom 4: soal 13-16, Kolom 5: soal 17-20
- Setiap baris memiliki nomor soal di kiri diikuti kotak-kotak pilihan {$optionsStr}

TUGAS:
1. Identifikasi bagian "JAWABAN" pada gambar
2. Untuk setiap nomor soal 1-{$jumlahSoal}, tentukan kotak mana yang dihitamkan
3. Jawaban yang dihitamkan terlihat lebih gelap/hitam dibanding kotak kosong

OUTPUT (JSON SAJA, TANPA PENJELASAN):
{
    "answers": ["A", "C", "B", null, "D", ...],
    "confidence": 0.85
}

Dimana:
- "answers" adalah array dengan panjang {$jumlahSoal}
- Setiap elemen berisi pilihan jawaban ({$optionsStr}) atau null jika tidak terdeteksi/kosong
- "confidence" adalah tingkat keyakinan deteksi (0-1)
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
