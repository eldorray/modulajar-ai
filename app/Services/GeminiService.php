<?php

namespace App\Services;

use App\Models\AiUsageLog;
use App\Models\Rpp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected ?string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key');
        $this->model = config('gemini.model', 'gemini-1.5-flash');
        $this->endpoint = config('gemini.endpoint');
    }

    /**
     * Generate RPP content using Gemini AI.
     */
    public function generateRPP(array $data, ?int $userId = null, ?int $rppId = null): array
    {
        $prompt = $this->buildPrompt($data);

        try {
            $response = Http::timeout(120)->post(
                $this->endpoint . $this->model . ':generateContent?key=' . $this->apiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topP' => 0.95,
                        'topK' => 40,
                        'maxOutputTokens' => 8192,
                        'responseMimeType' => 'application/json',
                    ],
                ]
            );

            if ($response->failed()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                // Better error messages based on status code
                $errorMessage = match ($response->status()) {
                    429 => 'Batas kuota API tercapai. Silakan tunggu beberapa saat dan coba lagi.',
                    403 => 'API Key tidak valid atau tidak memiliki akses. Periksa konfigurasi GEMINI_API_KEY.',
                    404 => 'Model AI tidak ditemukan. Periksa konfigurasi GEMINI_MODEL.',
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
            Log::error('Gemini Service Exception', [
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
        $targetPesertaDidik = $data['target_peserta_didik'] ?? 'Reguler';
        $topik = $data['topik'] ?? '';
        $alokasiWaktu = $data['alokasi_waktu'] ?? '';
        $jumlahPertemuan = $data['jumlah_pertemuan'] ?? 1;
        $kompetensiAwal = $data['kompetensi_awal'] ?? '';
        $kataKunci = $data['kata_kunci'] ?? '';
        $modelPembelajaran = $data['model_pembelajaran'] ?? 'Problem Based Learning';
        $jenisAsesmen = $data['jenis_asesmen'] ?? 'Formatif dan Sumatif';

        return <<<PROMPT
Anda adalah seorang ahli pengembangan kurikulum madrasah dan penyusun Modul Ajar di Indonesia dengan pengalaman lebih dari 20 tahun di lingkungan Kementerian Agama. Tugas Anda adalah membuat Modul Ajar yang LENGKAP dan BERKUALITAS sesuai standar **Kurikulum Berbasis Cinta (KBC) Kemenag** berdasarkan Kepdirjen Pendis Nomor 6077 Tahun 2025.

## DATA INPUT MODUL AJAR

**Informasi Umum:**
- Mata Pelajaran: {$mataPelajaran}
- Jenjang Madrasah: {$jenjang}
- Kelas: {$kelas}
- Semester: {$semester}
- Target Peserta Didik: {$targetPesertaDidik}
- Model Pembelajaran: {$modelPembelajaran}
- Kurikulum: Kurikulum Berbasis Cinta (Kemenag)

**Komponen Inti:**
- Topik/Materi: {$topik}
- Alokasi Waktu: {$alokasiWaktu}
- Jumlah Pertemuan: {$jumlahPertemuan}
- Kompetensi Awal: {$kompetensiAwal}
- Kata Kunci: {$kataKunci}
- Jenis Asesmen: {$jenisAsesmen}

## PRINSIP KURIKULUM BERBASIS CINTA

Kurikulum Berbasis Cinta menekankan nilai-nilai:
1. **Cinta kepada Allah SWT** - Menumbuhkan keimanan, ketakwaan, dan ibadah
2. **Cinta kepada Rasulullah SAW** - Meneladani akhlak dan sunnah Rasul
3. **Cinta kepada Sesama** - Toleransi, empati, ukhuwah Islamiyah, dan kerukunan
4. **Cinta kepada Ilmu** - Semangat belajar, rasa ingin tahu, dan pengembangan diri
5. **Cinta kepada Lingkungan** - Menjaga dan melestarikan alam sebagai khalifah

## INSTRUKSI PENTING

1. Buat modul ajar yang KOMPREHENSIF dan bernuansa ISLAMI
2. Integrasikan nilai-nilai cinta dalam setiap kegiatan pembelajaran
3. Terapkan prinsip moderasi beragama (wasathiyah)
4. Sesuaikan dengan tingkat perkembangan peserta didik sesuai jenjang madrasah
5. Gunakan pendekatan pembelajaran yang menyenangkan dan bermakna
6. Pastikan tujuan pembelajaran terukur dan Islami
7. Kegiatan pembelajaran harus detail dengan sentuhan nilai-nilai agama
8. Asesmen harus selaras dengan tujuan pembelajaran

## OUTPUT JSON

Berikan output dalam format JSON VALID dengan struktur berikut:

{
    "informasi_umum": {
        "mata_pelajaran": "{$mataPelajaran}",
        "jenjang": "{$jenjang}",
        "kelas": "{$kelas}",
        "semester": "{$semester}",
        "alokasi_waktu": "{$alokasiWaktu}",
        "jumlah_pertemuan": {$jumlahPertemuan},
        "model_pembelajaran": "{$modelPembelajaran}",
        "target_peserta_didik": "{$targetPesertaDidik}",
        "kurikulum": "Kurikulum Berbasis Cinta (Kemenag)"
    },
    "kompetensi_awal": "Tuliskan prasyarat pengetahuan/keterampilan yang harus dimiliki siswa sebelum mempelajari materi ini",
    "nilai_nilai_cinta": [
        {
            "dimensi": "Cinta kepada Allah SWT",
            "deskripsi": "Penjelasan bagaimana dimensi cinta kepada Allah dikembangkan dalam pembelajaran ini"
        },
        {
            "dimensi": "Cinta kepada Rasulullah SAW",
            "deskripsi": "Penjelasan bagaimana dimensi cinta kepada Rasul dikembangkan"
        },
        {
            "dimensi": "Cinta kepada Sesama",
            "deskripsi": "Penjelasan bagaimana dimensi cinta kepada sesama dikembangkan"
        },
        {
            "dimensi": "Cinta kepada Ilmu",
            "deskripsi": "Penjelasan bagaimana dimensi cinta kepada ilmu dikembangkan"
        },
        {
            "dimensi": "Cinta kepada Lingkungan",
            "deskripsi": "Penjelasan bagaimana dimensi cinta kepada lingkungan dikembangkan"
        }
    ],
    "profil_lulusan_madrasah": [
        {
            "dimensi": "Beriman dan Bertakwa kepada Allah SWT",
            "deskripsi": "Penjelasan bagaimana dimensi ini dikembangkan dalam pembelajaran"
        },
        {
            "dimensi": "Berakhlak Mulia",
            "deskripsi": "Penjelasan pengembangan akhlakul karimah"
        },
        {
            "dimensi": "Moderat dalam Beragama (Wasathiyah)",
            "deskripsi": "Penjelasan penerapan moderasi beragama"
        },
        {
            "dimensi": "Mandiri dan Kreatif",
            "deskripsi": "Penjelasan pengembangan kemandirian dan kreativitas"
        },
        {
            "dimensi": "Bergotong Royong",
            "deskripsi": "Penjelasan pengembangan sikap gotong royong dan ukhuwah"
        }
    ],
    "moderasi_beragama": {
        "nilai_wasathiyah": "Penjelasan nilai jalan tengah (wasathiyah) yang diterapkan dalam pembelajaran ini",
        "implementasi": [
            "Kegiatan implementasi moderasi beragama 1",
            "Kegiatan implementasi moderasi beragama 2"
        ]
    },
    "sarana_prasarana": {
        "alat": ["Alat yang dibutuhkan"],
        "bahan": ["Bahan yang dibutuhkan"],
        "media": ["Media pembelajaran"],
        "sumber_belajar": ["Sumber belajar (buku, Al-Quran, Hadits, website, dll)"]
    },
    "tujuan_pembelajaran": [
        "Tujuan pembelajaran 1 yang SMART dengan nuansa Islami",
        "Tujuan pembelajaran 2 yang SMART dengan nuansa Islami"
    ],
    "pemahaman_bermakna": "Penjelasan tentang manfaat dan relevansi materi dengan kehidupan nyata peserta didik serta nilai-nilai Islami",
    "pertanyaan_pemantik": [
        "Pertanyaan pemantik 1 yang memancing rasa ingin tahu dan refleksi keagamaan",
        "Pertanyaan pemantik 2 yang mendorong berpikir kritis",
        "Pertanyaan pemantik 3 yang menghubungkan dengan pengalaman dan nilai Islam"
    ],
    "kegiatan_pembelajaran": {
        "pendahuluan": {
            "durasi": "15 menit",
            "aktivitas": [
                {
                    "langkah": 1,
                    "kegiatan_guru": "Membuka dengan salam, doa, dan tadarus/muraja'ah",
                    "kegiatan_siswa": "Menjawab salam, berdoa bersama, dan mengikuti tadarus"
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
                    "kegiatan_guru": "Apa yang dilakukan guru secara detail dengan integrasi nilai-nilai cinta",
                    "kegiatan_siswa": "Apa yang dilakukan siswa secara detail",
                    "durasi": "X menit",
                    "nilai_cinta": "Dimensi cinta yang dikembangkan"
                }
            ]
        },
        "penutup": {
            "durasi": "10 menit",
            "aktivitas": [
                {
                    "langkah": 1,
                    "kegiatan_guru": "Refleksi pembelajaran, penguatan nilai-nilai cinta, dan doa penutup",
                    "kegiatan_siswa": "Menyampaikan refleksi dan berdoa bersama"
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
            "Pertanyaan refleksi untuk siswa tentang pemahaman materi dan nilai-nilai yang dipelajari",
            "Pertanyaan refleksi tentang penerapan nilai-nilai cinta dalam kehidupan"
        ],
        "refleksi_guru": [
            "Pertanyaan refleksi untuk guru tentang efektivitas pembelajaran",
            "Pertanyaan refleksi tentang ketercapaian penanaman nilai-nilai cinta"
        ]
    },
    "lkpd": {
        "judul": "Lembar Kerja Peserta Didik - {$topik}",
        "tujuan": "Tujuan LKPD yang selaras dengan tujuan pembelajaran dan nilai-nilai Islami",
        "petunjuk_umum": [
            "Awali dengan membaca Bismillah",
            "Petunjuk pengerjaan lainnya"
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
        "kesimpulan": "Bagian untuk siswa menuliskan kesimpulan pembelajaran dan hikmah yang dipetik"
    },
    "glosarium": [
        {
            "istilah": "Istilah penting 1",
            "definisi": "Definisi istilah"
        }
    ],
    "daftar_pustaka": [
        "Al-Quran dan terjemahannya",
        "Sumber referensi lainnya (format APA)"
    ]
}

PENTING: 
- Pastikan JSON valid dan lengkap
- Isi setiap bagian dengan konten yang substantif, berkualitas, dan bernuansa Islami
- Integrasikan nilai-nilai cinta dalam setiap komponen pembelajaran
- Terapkan prinsip moderasi beragama (tidak ekstrem kanan maupun kiri)
- Sesuaikan tingkat kesulitan dengan jenjang madrasah yang diminta
- Gunakan bahasa Indonesia yang baik, benar, dan santun
PROMPT;
    }

    /**
     * Extract content from Gemini response.
     */
    protected function extractContent(array $result): ?array
    {
        try {
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if (!$text) {
                return null;
            }

            // Parse JSON response
            $content = json_decode($text, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Failed to parse Gemini response as JSON', ['text' => $text]);
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
            $usageMetadata = $result['usageMetadata'] ?? [];
            
            AiUsageLog::create([
                'user_id' => $userId,
                'rpp_id' => $rppId,
                'provider' => 'google',
                'model_name' => $this->model,
                'input_tokens' => $usageMetadata['promptTokenCount'] ?? 0,
                'output_tokens' => $usageMetadata['candidatesTokenCount'] ?? 0,
                'total_tokens' => $usageMetadata['totalTokenCount'] ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log AI usage', ['error' => $e->getMessage()]);
        }
    }
}
