<?php

namespace App\Services;

use App\Models\AiUsageLog;
use App\Models\Rpp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSeekService
{
    protected ?string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('deepseek.api_key');
        $this->model = config('deepseek.model', 'deepseek-chat');
        $this->endpoint = config('deepseek.endpoint');
    }

    /**
     * Generate RPP content using DeepSeek AI.
     */
    public function generateRPP(array $data, ?int $userId = null, ?int $rppId = null): array
    {
        $prompt = $this->buildPrompt($data);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Anda adalah seorang ahli pendidikan Indonesia yang bertugas membuat Rencana Pelaksanaan Pembelajaran (RPP) / Modul Ajar. Berikan output dalam format JSON yang valid.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 8192,
                'response_format' => [
                    'type' => 'json_object'
                ],
            ]);

            if ($response->failed()) {
                Log::error('DeepSeek API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                // Better error messages based on status code
                $errorMessage = match ($response->status()) {
                    429 => 'Batas kuota API tercapai. Silakan tunggu beberapa saat dan coba lagi.',
                    401 => 'API Key tidak valid. Periksa konfigurasi DEEPSEEK_API_KEY.',
                    403 => 'Akses ditolak. API Key tidak memiliki izin.',
                    404 => 'Endpoint tidak ditemukan. Periksa konfigurasi.',
                    500, 503 => 'Server AI sedang tidak tersedia. Silakan coba lagi nanti.',
                    default => 'Gagal menghasilkan RPP. Silakan coba lagi.',
                };
                
                return [
                    'success' => false,
                    'error' => $errorMessage,
                ];
            }

            $result = $response->json();
            
            // Extract content from response
            $content = $this->extractContent($result);
            
            // Log token usage
            $this->logUsage($result, $userId, $rppId);

            return [
                'success' => true,
                'content' => $content,
            ];

        } catch (\Exception $e) {
            Log::error('DeepSeek Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Build the prompt for Modul Ajar generation.
     */
    protected function buildPrompt(array $data): string
    {
        $mataPelajaran = $data['mata_pelajaran'] ?? '';
        $fase = $data['fase'] ?? '';
        $kelas = $data['kelas'] ?? '';
        $semester = $data['semester'] ?? '';
        $targetPesertaDidik = $data['target_peserta_didik'] ?? 'Reguler';
        $topik = $data['topik'] ?? '';
        $alokasiWaktu = $data['alokasi_waktu'] ?? '';
        $jumlahPertemuan = $data['jumlah_pertemuan'] ?? 1;
        $kompetensiAwal = $data['kompetensi_awal'] ?? '';
        $kataKunci = $data['kata_kunci'] ?? '';
        $modelPembelajaran = $data['model_pembelajaran'] ?? 'Problem Based Learning';
        $kurikulum = $data['kurikulum'] ?? 'Kurikulum Merdeka';
        $jenisAsesmen = $data['jenis_asesmen'] ?? 'Formatif dan Sumatif';

        // Check if using Kurikulum Berbasis Cinta (Kemenag)
        if ($kurikulum === 'Kurikulum Berbasis Cinta') {
            return $this->buildKBCPrompt($data);
        }

        return <<<PROMPT
Anda adalah seorang ahli pengembangan kurikulum dan penyusun Modul Ajar di Indonesia dengan pengalaman lebih dari 20 tahun. Tugas Anda adalah membuat Modul Ajar yang LENGKAP dan BERKUALITAS sesuai standar Kemdikbud Kurikulum Merdeka.

## DATA INPUT MODUL AJAR

**Informasi Umum:**
- Mata Pelajaran: {$mataPelajaran}
- Fase: {$fase}
- Kelas: {$kelas}
- Semester: {$semester}
- Target Peserta Didik: {$targetPesertaDidik}
- Model Pembelajaran: {$modelPembelajaran}
- Kurikulum: {$kurikulum}

**Komponen Inti:**
- Topik/Materi: {$topik}
- Alokasi Waktu: {$alokasiWaktu}
- Jumlah Pertemuan: {$jumlahPertemuan}
- Kompetensi Awal: {$kompetensiAwal}
- Kata Kunci: {$kataKunci}
- Jenis Asesmen: {$jenisAsesmen}

## INSTRUKSI PENTING

1. Buat modul ajar yang KOMPREHENSIF dan APLIKATIF
2. Sesuaikan dengan tingkat perkembangan peserta didik sesuai fase
3. Gunakan pendekatan pembelajaran aktif dan berpusat pada siswa
4. Pastikan tujuan pembelajaran terukur (SMART)
5. Kegiatan pembelajaran harus detail dan jelas langkah-langkahnya
6. Asesmen harus selaras dengan tujuan pembelajaran
7. LKPD harus relevan dan dapat digunakan langsung

## OUTPUT JSON

Berikan output dalam format JSON VALID dengan struktur berikut:

{
    "informasi_umum": {
        "mata_pelajaran": "{$mataPelajaran}",
        "fase": "{$fase}",
        "kelas": "{$kelas}",
        "semester": "{$semester}",
        "alokasi_waktu": "{$alokasiWaktu}",
        "jumlah_pertemuan": {$jumlahPertemuan},
        "model_pembelajaran": "{$modelPembelajaran}",
        "target_peserta_didik": "{$targetPesertaDidik}"
    },
    "kompetensi_awal": "Tuliskan prasyarat pengetahuan/keterampilan yang harus dimiliki siswa sebelum mempelajari materi ini",
    "profil_pelajar_pancasila": [
        {
            "dimensi": "Nama Dimensi (Beriman, Mandiri, Bergotong Royong, Bernalar Kritis, Kreatif, Berkebinekaan Global)",
            "deskripsi": "Penjelasan bagaimana dimensi ini dikembangkan dalam pembelajaran"
        }
    ],
    "sarana_prasarana": {
        "alat": ["Alat yang dibutuhkan"],
        "bahan": ["Bahan yang dibutuhkan"],
        "media": ["Media pembelajaran"],
        "sumber_belajar": ["Sumber belajar (buku, website, dll)"]
    },
    "tujuan_pembelajaran": [
        "Tujuan pembelajaran 1 yang SMART (Specific, Measurable, Achievable, Relevant, Time-bound)",
        "Tujuan pembelajaran 2 yang SMART"
    ],
    "pemahaman_bermakna": "Penjelasan tentang manfaat dan relevansi materi dengan kehidupan nyata peserta didik",
    "pertanyaan_pemantik": [
        "Pertanyaan pemantik 1 yang memancing rasa ingin tahu",
        "Pertanyaan pemantik 2 yang mendorong berpikir kritis",
        "Pertanyaan pemantik 3 yang menghubungkan dengan pengalaman siswa"
    ],
    "kegiatan_pembelajaran": {
        "pendahuluan": {
            "durasi": "15 menit",
            "aktivitas": [
                {
                    "langkah": 1,
                    "kegiatan_guru": "Apa yang dilakukan guru",
                    "kegiatan_siswa": "Apa yang dilakukan siswa"
                }
            ]
        },
        "inti": {
            "durasi": "Sesuaikan dengan alokasi waktu",
            "sintaks_model": "{$modelPembelajaran}",
            "aktivitas": [
                {
                    "fase_sintaks": "Nama fase sesuai model pembelajaran",
                    "langkah": 1,
                    "kegiatan_guru": "Apa yang dilakukan guru secara detail",
                    "kegiatan_siswa": "Apa yang dilakukan siswa secara detail",
                    "durasi": "X menit"
                }
            ]
        },
        "penutup": {
            "durasi": "10 menit",
            "aktivitas": [
                {
                    "langkah": 1,
                    "kegiatan_guru": "Apa yang dilakukan guru",
                    "kegiatan_siswa": "Apa yang dilakukan siswa"
                }
            ]
        }
    },
    "asesmen": {
        "jenis": "{$jenisAsesmen}",
        "teknik": ["Teknik asesmen 1", "Teknik asesmen 2"],
        "bentuk": "Bentuk asesmen (tes tertulis, kinerja, proyek, dll)",
        "instrumen": [
            {
                "jenis": "Jenis instrumen",
                "deskripsi": "Deskripsi instrumen",
                "contoh_soal": ["Contoh soal/tugas 1", "Contoh soal/tugas 2"]
            }
        ],
        "rubrik_penilaian": [
            {
                "kriteria": "Kriteria penilaian",
                "skor_4": "Deskriptor Sangat Baik",
                "skor_3": "Deskriptor Baik",
                "skor_2": "Deskriptor Cukup",
                "skor_1": "Deskriptor Perlu Perbaikan"
            }
        ]
    },
    "pengayaan_remedial": {
        "pengayaan": {
            "sasaran": "Peserta didik dengan pencapaian tinggi",
            "kegiatan": ["Kegiatan pengayaan 1", "Kegiatan pengayaan 2"]
        },
        "remedial": {
            "sasaran": "Peserta didik dengan kesulitan belajar",
            "kegiatan": ["Kegiatan remedial 1", "Kegiatan remedial 2"]
        }
    },
    "refleksi": {
        "refleksi_siswa": [
            "Pertanyaan refleksi untuk siswa 1",
            "Pertanyaan refleksi untuk siswa 2"
        ],
        "refleksi_guru": [
            "Pertanyaan refleksi untuk guru 1",
            "Pertanyaan refleksi untuk guru 2"
        ]
    },
    "lkpd": {
        "judul": "Lembar Kerja Peserta Didik - {$topik}",
        "tujuan": "Tujuan LKPD yang selaras dengan tujuan pembelajaran",
        "petunjuk_umum": [
            "Petunjuk pengerjaan 1",
            "Petunjuk pengerjaan 2"
        ],
        "kegiatan": [
            {
                "nomor": 1,
                "judul_kegiatan": "Nama kegiatan",
                "petunjuk": "Petunjuk khusus kegiatan ini",
                "soal_tugas": [
                    {
                        "nomor": "1",
                        "pertanyaan": "Pertanyaan atau instruksi",
                        "tipe": "essay/pilihan_ganda/praktik"
                    }
                ]
            }
        ],
        "kesimpulan": "Bagian untuk siswa menuliskan kesimpulan pembelajaran"
    },
    "glosarium": [
        {
            "istilah": "Istilah penting 1",
            "definisi": "Definisi istilah"
        }
    ],
    "daftar_pustaka": [
        "Sumber referensi 1 (format APA)",
        "Sumber referensi 2 (format APA)"
    ]
}

PENTING: 
- Pastikan JSON valid dan lengkap
- Isi setiap bagian dengan konten yang substantif dan berkualitas
- Sesuaikan tingkat kesulitan dengan fase/kelas yang diminta
- Gunakan bahasa Indonesia yang baik dan benar
PROMPT;
    }

    /**
     * Build the prompt for Kurikulum Berbasis Cinta (Kemenag) Modul Ajar generation.
     */
    protected function buildKBCPrompt(array $data): string
    {
        $mataPelajaran = $data['mata_pelajaran'] ?? '';
        $jenjang = $data['fase'] ?? '';
        $kelas = $data['kelas'] ?? '';
        $semester = $data['semester'] ?? '';
        $topik = $data['topik'] ?? '';
        $alokasiWaktu = $data['alokasi_waktu'] ?? '';
        $jumlahPertemuan = $data['jumlah_pertemuan'] ?? 1;
        $modelPembelajaran = $data['model_pembelajaran'] ?? 'Problem Based Learning';
        $jenisAsesmen = $data['jenis_asesmen'] ?? 'Formatif dan Sumatif';

        return <<<PROMPT
Anda ahli kurikulum madrasah. Buat Modul Ajar sesuai **Kurikulum Berbasis Cinta (KBC) Kemenag**.

## INPUT
- Mapel: {$mataPelajaran} | Jenjang: {$jenjang} | Kelas: {$kelas} | Semester: {$semester}
- Topik: {$topik} | Waktu: {$alokasiWaktu} | Pertemuan: {$jumlahPertemuan}
- Model: {$modelPembelajaran} | Asesmen: {$jenisAsesmen}

## NILAI CINTA KBC
1. Cinta Allah - keimanan & ibadah
2. Cinta Rasul - akhlak & sunnah
3. Cinta Sesama - toleransi & ukhuwah
4. Cinta Ilmu - semangat belajar
5. Cinta Lingkungan - menjaga alam

## OUTPUT JSON
```json
{
  "informasi_umum": {"mata_pelajaran":"","jenjang":"","kelas":"","semester":"","alokasi_waktu":"","jumlah_pertemuan":0,"model_pembelajaran":"","kurikulum":"KBC Kemenag"},
  "kompetensi_awal": "prasyarat pengetahuan",
  "nilai_nilai_cinta": [{"dimensi":"Cinta kepada Allah SWT","deskripsi":""}],
  "profil_lulusan_madrasah": [{"dimensi":"Beriman Bertakwa","deskripsi":""}],
  "moderasi_beragama": {"nilai_wasathiyah":"","implementasi":[""]},
  "sarana_prasarana": {"alat":[""],"bahan":[""],"media":[""],"sumber_belajar":[""]},
  "tujuan_pembelajaran": ["tujuan SMART Islami"],
  "pemahaman_bermakna": "relevansi materi",
  "pertanyaan_pemantik": ["pertanyaan"],
  "kegiatan_pembelajaran": {
    "pendahuluan": {"durasi":"15 menit","aktivitas":[{"langkah":1,"kegiatan_guru":"salam, doa, tadarus","kegiatan_siswa":"menjawab salam, berdoa"}]},
    "inti": {"durasi":"","sintaks_model":"","aktivitas":[{"fase_sintaks":"","langkah":1,"kegiatan_guru":"","kegiatan_siswa":"","durasi":""}]},
    "penutup": {"durasi":"10 menit","aktivitas":[{"langkah":1,"kegiatan_guru":"refleksi, doa","kegiatan_siswa":"refleksi, berdoa"}]}
  },
  "asesmen": {"jenis":"","teknik":[""],"bentuk":"","instrumen":[{"jenis":"","deskripsi":"","contoh_soal":[""]}],"rubrik_penilaian":[{"kriteria":"","skor_4":"","skor_3":"","skor_2":"","skor_1":""}]},
  "pengayaan_remedial": {"pengayaan":{"sasaran":"","kegiatan":[""]},"remedial":{"sasaran":"","kegiatan":[""]}},
  "refleksi": {"refleksi_siswa":[""],"refleksi_guru":[""]},
  "lkpd": {"judul":"LKPD - {$topik}","tujuan":"","petunjuk_umum":["Bismillah"],"kegiatan":[{"nomor":1,"judul_kegiatan":"","petunjuk":"","soal_tugas":[{"nomor":"1","pertanyaan":"","tipe":"essay"}]}],"kesimpulan":""},
  "glosarium": [{"istilah":"","definisi":""}],
  "daftar_pustaka": ["Al-Quran"]
}
```

Isi SEMUA field dengan konten substantif sesuai topik. Integrasikan nilai-nilai cinta. Output JSON VALID saja.
PROMPT;
    }

    /**
     * Extract content from DeepSeek response.
     */
    protected function extractContent(array $result): ?array
    {
        try {
            $text = $result['choices'][0]['message']['content'] ?? null;
            
            if (!$text) {
                return null;
            }

            // Parse JSON response
            $content = json_decode($text, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Failed to parse DeepSeek response as JSON', ['text' => $text]);
                return null;
            }

            return $content;
        } catch (\Exception $e) {
            Log::error('Error extracting content', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Log AI usage for tracking.
     */
    protected function logUsage(array $result, ?int $userId, ?int $rppId): void
    {
        try {
            $usage = $result['usage'] ?? [];
            
            AiUsageLog::create([
                'user_id' => $userId,
                'rpp_id' => $rppId,
                'provider' => 'deepseek',
                'model_name' => $this->model,
                'input_tokens' => $usage['prompt_tokens'] ?? 0,
                'output_tokens' => $usage['completion_tokens'] ?? 0,
                'total_tokens' => $usage['total_tokens'] ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log AI usage', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Generate STS (Sumatif Tengah Semester) questions using DeepSeek AI.
     */
    public function generateSTS(array $data, ?int $userId = null, ?int $stsId = null): array
    {
        $prompt = $this->buildSTSPrompt($data);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Anda adalah seorang Guru Profesional dan Ahli Kurikulum yang berpengalaman dalam menyusun asesmen sesuai Kurikulum Merdeka. Berikan output dalam format JSON yang valid.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.5,
                'max_tokens' => 4096,
                'response_format' => [
                    'type' => 'json_object'
                ],
            ]);

            if ($response->failed()) {
                Log::error('DeepSeek STS API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                $errorMessage = match ($response->status()) {
                    429 => 'Batas kuota API tercapai. Silakan tunggu beberapa saat dan coba lagi.',
                    401 => 'API Key tidak valid. Periksa konfigurasi DEEPSEEK_API_KEY.',
                    403 => 'Akses ditolak. API Key tidak memiliki izin.',
                    404 => 'Endpoint tidak ditemukan. Periksa konfigurasi.',
                    500, 503 => 'Server AI sedang tidak tersedia. Silakan coba lagi nanti.',
                    default => 'Gagal menghasilkan soal STS. Silakan coba lagi.',
                };
                
                return [
                    'success' => false,
                    'error' => $errorMessage,
                ];
            }

            $result = $response->json();
            $content = $this->extractContent($result);

            // Log usage (reuse existing method)
            $this->logUsage($result, $userId, $stsId);

            return [
                'success' => true,
                'content' => $content,
            ];

        } catch (\Exception $e) {
            Log::error('DeepSeek STS Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Build the prompt for STS question generation.
     */
    protected function buildSTSPrompt(array $data): string
    {
        $mataPelajaran = $data['mata_pelajaran'] ?? '';
        $kelas = $data['kelas'] ?? '';
        $fase = $data['fase'] ?? '';
        $topik = $data['topik'] ?? '';
        $tujuanPembelajaran = $data['tujuan_pembelajaran'] ?? '';
        $jumlahPG = $data['jumlah_pg'] ?? 10;
        $jumlahPGKompleks = $data['jumlah_pg_kompleks'] ?? 3;
        $jumlahMenjodohkan = $data['jumlah_menjodohkan'] ?? 5;
        $jumlahUraian = $data['jumlah_uraian'] ?? 2;
        $materi = $data['materi'] ?? '';

        $totalSoal = $jumlahPG + $jumlahPGKompleks + $jumlahMenjodohkan + $jumlahUraian;

        return <<<PROMPT
Buat soal STS/PTS untuk: {$mataPelajaran}, Kelas {$kelas}/{$fase}, Topik: {$topik}

Spesifikasi: {$jumlahPG} PG, {$jumlahPGKompleks} PG Kompleks, {$jumlahMenjodohkan} Menjodohkan, {$jumlahUraian} Uraian
TP: {$tujuanPembelajaran}
Materi: {$materi}

Output JSON:
```json
{
  "kisi_kisi":[{"nomor_soal":"1","tujuan_pembelajaran":"","materi":"","level_kognitif":"C1-C6","bentuk_soal":""}],
  "soal_pilihan_ganda":[{"nomor":1,"pertanyaan":"","pilihan":{"A":"","B":"","C":"","D":""}}],
  "soal_pg_kompleks":[{"nomor":1,"pertanyaan":"","pernyataan":[{"teks":"","benar":true}]}],
  "soal_menjodohkan":[{"nomor":1,"soal":"","jawaban":""}],
  "soal_uraian":[{"nomor":1,"pertanyaan":""}],
  "kunci_jawaban":{"pilihan_ganda":["A"],"pg_kompleks":[{"nomor":1,"jawaban":"Benar:1|Salah:2"}],"menjodohkan":["1-A"],"uraian":[{"nomor":1,"jawaban":""}]},
  "rubrik_penilaian":[{"nomor_soal":1,"kriteria":[{"deskripsi":"Sangat Baik","skor":4},{"deskripsi":"Baik","skor":3},{"deskripsi":"Cukup","skor":2},{"deskripsi":"Kurang","skor":1}]}]
}
```

Isi SEMUA soal sesuai jumlah. Proporsi: LOTS 30%, MOTS 40%, HOTS 30%. JSON valid saja.
PROMPT;
    }
}

