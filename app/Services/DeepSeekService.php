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
        $isDeepLearning = ($data['kurikulum'] ?? '') === 'Kurikulum Merdeka Deep Learning';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout($isDeepLearning ? 300 : 120)->post($this->endpoint, [
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

        // Check if using Kurikulum Merdeka Deep Learning (RPPM)
        if ($kurikulum === 'Kurikulum Merdeka Deep Learning') {
            return $this->buildRPPMPrompt($data);
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
     * Build the prompt for RPPM (Kurikulum Merdeka Deep Learning) generation.
     */
    protected function buildRPPMPrompt(array $data): string
    {
        $mataPelajaran = $data['mata_pelajaran'] ?? '';
        $fase = $data['fase'] ?? '';
        $kelas = $data['kelas'] ?? '';
        $semester = $data['semester'] ?? '';
        $topik = $data['topik'] ?? '';
        $alokasiWaktu = $data['alokasi_waktu'] ?? '';
        $jumlahPertemuan = $data['jumlah_pertemuan'] ?? 3;
        $kompetensiAwal = $data['kompetensi_awal'] ?? '';
        $modelPembelajaran = $data['model_pembelajaran'] ?? 'Discovery Based Learning';
        $namaGuru = $data['nama_guru'] ?? '';
        $kataKunci = $data['kata_kunci'] ?? '';
        $targetPesertaDidik = $data['target_peserta_didik'] ?? '';

        // Normalize jenis_asesmen into array
        $asesmenArray = $data['jenis_asesmen_array'] ?? null;
        if (!is_array($asesmenArray)) {
            $asesmenRaw = $data['jenis_asesmen'] ?? 'Diagnostik Kognitif, Diagnostik Non-Kognitif, Formatif, Sumatif';
            $asesmenArray = array_filter(array_map('trim', explode(',', $asesmenRaw)));
        }
        if (empty($asesmenArray)) {
            $asesmenArray = ['Diagnostik Kognitif', 'Diagnostik Non-Kognitif', 'Formatif', 'Sumatif'];
        }

        $hasDiagKog = in_array('Diagnostik Kognitif', $asesmenArray, true);
        $hasDiagNon = in_array('Diagnostik Non-Kognitif', $asesmenArray, true);
        $hasFormatif = in_array('Formatif', $asesmenArray, true);
        $hasSumatif = in_array('Sumatif', $asesmenArray, true);

        // Build asesmen JSON fragment conditionally
        $asesmenParts = [];
        if ($hasDiagKog) {
            $asesmenParts[] = '"diagnostik_kognitif":{"deskripsi":"Tes diagnostik pengetahuan awal tentang '.$topik.'","kisi_kisi":[{"capaian_pembelajaran":"CP relevan topik","tujuan":"tujuan diagnostik","indikator":"indikator soal 1","level_kognitif":"C1","nomor_soal":1},{"capaian_pembelajaran":"CP relevan topik","tujuan":"tujuan diagnostik","indikator":"indikator soal 2","level_kognitif":"C2","nomor_soal":2},{"capaian_pembelajaran":"CP relevan topik","tujuan":"tujuan diagnostik","indikator":"indikator soal 3","level_kognitif":"C4","nomor_soal":3}],"soal":[{"nomor":1,"stimulus":"stimulus soal 1 sesuai topik","pertanyaan":"pertanyaan soal 1?","pilihan":{"A":"pilihan A","B":"pilihan B","C":"pilihan C","D":"pilihan D","E":"pilihan E"},"kunci":"B","level":"C1"},{"nomor":2,"stimulus":"stimulus soal 2 sesuai topik","pertanyaan":"pertanyaan soal 2?","pilihan":{"A":"pilihan A","B":"pilihan B","C":"pilihan C","D":"pilihan D","E":"pilihan E"},"kunci":"C","level":"C2"},{"nomor":3,"stimulus":"stimulus soal 3 sesuai topik","pertanyaan":"pertanyaan soal 3?","pilihan":{"A":"pilihan A","B":"pilihan B","C":"pilihan C","D":"pilihan D","E":"pilihan E"},"kunci":"A","level":"C4"}],"kriteria_penilaian":{"rumus":"Jumlah Jawaban Benar / Total Soal x 100","kategori":[{"rentang":"86-100","kategori":"Sangat Mahir","deskripsi":"pemahaman sangat baik"},{"rentang":"70-85","kategori":"Mahir","deskripsi":"pemahaman baik, perlu penguatan"},{"rentang":"0-69","kategori":"Perlu Bimbingan","deskripsi":"perlu pendampingan konsep dasar"}]}}';
        }
        if ($hasDiagNon) {
            $asesmenParts[] = '"diagnostik_non_kognitif":{"jenis":"Gaya Belajar (Visual, Auditori, Kinestetik)","deskripsi":"Asesmen gaya belajar peserta didik","instrumen":"Kuesioner 20 pernyataan (A=Visual, B=Auditori, C=Kinestetik)","rekomendasi":[{"gaya":"Visual","strategi":"gambar, diagram, video, infografis"},{"gaya":"Auditori","strategi":"diskusi, tanya jawab, presentasi lisan"},{"gaya":"Kinestetik","strategi":"praktik, simulasi, proyek"}]}';
        }
        if ($hasFormatif) {
            $asesmenParts[] = '"formatif":{"pertemuan":2,"jenis":"Penilaian Diskusi Kelompok","indikator":["Kemampuan Bekerja Sama","Kemampuan Menjelaskan","Kekompakan","Keaktifan","Kemampuan Menerima Pendapat"],"rubrik_diskusi":[{"aspek":"Kemampuan bekerja sama","skor_4":"Bekerja sama sangat baik, jadi fasilitator","skor_3":"Kurang bekerja sama","skor_2":"Sangat individu","skor_1":"Tidak bekerja sama"},{"aspek":"Kemampuan menyampaikan pendapat","skor_4":"Selalu menyampaikan pendapat jelas","skor_3":"Sering menyampaikan pendapat","skor_2":"Jarang menyampaikan pendapat","skor_1":"Tidak pernah menyampaikan pendapat"},{"aspek":"Keaktifan","skor_4":"Sangat aktif berpartisipasi","skor_3":"Cukup aktif","skor_2":"Jarang aktif","skor_1":"Tidak aktif"}],"teknik_penilaian_diskusi":"Penilaian = Perolehan Skor x 5","interval_diskusi":[{"rentang":"81-100","keterangan":"Luar Biasa"},{"rentang":"61-80","keterangan":"Sangat Baik"},{"rentang":"41-60","keterangan":"Baik"},{"rentang":"21-40","keterangan":"Sedang"},{"rentang":"5-20","keterangan":"Rendah"}],"rubrik_produk":[{"kriteria":"Kreativitas","skor_5":"Ide orisinal dan inovatif","skor_4":"Ide orisinal menarik","skor_3":"Cukup orisinal","skor_2":"Kurang orisinal","skor_1":"Tidak orisinal"},{"kriteria":"Relevansi","skor_5":"Sangat relevan dengan topik","skor_4":"Mayoritas relevan","skor_3":"Sebagian relevan","skor_2":"Minim relevansi","skor_1":"Tidak relevan"},{"kriteria":"Ketepatan Informasi","skor_5":"Akurat, mendalam, berbasis bukti","skor_4":"Sebagian besar akurat","skor_3":"Beberapa akurat","skor_2":"Kurang akurat","skor_1":"Tidak akurat"}],"rubrik_presentasi":[{"aspek":"Penguasaan materi","skor_3":"Memahami materi menyeluruh","skor_2":"Memahami kurang menyeluruh","skor_1":"Tidak memahami materi"},{"aspek":"Sistematika","skor_3":"Presentasi jelas dan terstruktur","skor_2":"Kurang jelas","skor_1":"Tidak jelas"},{"aspek":"Bahasa","skor_3":"Kosakata tepat","skor_2":"Kurang tepat","skor_1":"Tidak tepat"}],"interval_kelompok":[{"rentang":"86-100","kategori":"Sangat Baik"},{"rentang":"71-85","kategori":"Baik"},{"rentang":"56-70","kategori":"Cukup"},{"rentang":"0-55","kategori":"Perlu Bimbingan"}]}';
        }
        if ($hasSumatif) {
            $asesmenParts[] = '"sumatif":{"deskripsi":"Asesmen akhir ketercapaian tujuan pembelajaran tentang '.$topik.'","waktu":"Setelah seluruh CP selesai","bentuk":"Tes tertulis (pilihan ganda dan essay)","kisi_kisi":[{"tujuan":"tujuan sumatif 1","indikator":"indikator 1","level":"C3","nomor":1},{"tujuan":"tujuan sumatif 2","indikator":"indikator 2","level":"C4","nomor":2}]}';
        }
        $asesmenJson = '"asesmen":{' . implode(',', $asesmenParts) . '}';

        $asesmenList = implode(', ', $asesmenArray);
        $asesmenInstruksi = 'WAJIB isi HANYA bagian asesmen berikut: ' . $asesmenList . '. JANGAN tambahkan key asesmen lain selain yang diminta.';

        return <<<PROMPT
Buat RPPM (Rencana Pelaksanaan Pembelajaran Mendalam) Kurikulum Merdeka Deep Learning dalam format JSON.

INPUT: Mapel={$mataPelajaran} | Fase={$fase} | Kelas={$kelas} | Semester={$semester} | Topik={$topik} | Waktu={$alokasiWaktu} ({$jumlahPertemuan} pertemuan) | Model={$modelPembelajaran} | Jenis Asesmen={$asesmenList} | Kompetensi Awal={$kompetensiAwal} | Kata Kunci={$kataKunci} | Target Peserta Didik={$targetPesertaDidik} | Nama Guru={$namaGuru}

STRUKTUR 3 FASE: Pertemuan 1=Memahami (Stimulasi→Identifikasi Masalah→Pengumpulan Data), Pertemuan 2=Mengaplikasi (Pengolahan Data 1→Pengolahan Data 2), Pertemuan 3=Merefleksi (Pembuktian→Penarikan Kesimpulan).
KSE CASEL: Kesadaran Diri/Self-Awareness | Manajemen Diri/Self-Management | Kesadaran Sosial/Social Awareness | Keterampilan Hubungan/Relationship Skills | Pengambilan Keputusan Bertanggung Jawab/Responsible Decision-Making

ASESMEN: {$asesmenInstruksi}

OUTPUT JSON PERSIS dengan struktur ini (isi semua field dengan konten SPESIFIK sesuai topik):

{"identifikasi":{"peserta_didik":"deskripsi kemampuan peserta didik","materi_pelajaran":"cakupan materi","profil_lulusan":[{"kode":"DPL 2","nama":"Kewargaan","dipilih":true},{"kode":"DPL 3","nama":"Penalaran Kritis","dipilih":true},{"kode":"DPL 6","nama":"Kemandirian","dipilih":false},{"kode":"DPL 8","nama":"Komunikasi","dipilih":true}]},"desain_pembelajaran":{"capaian_pembelajaran":"rumusan CP","topik":"{$topik}","lintas_disiplin":["Mapel 1 : keterkaitan","Mapel 2 : keterkaitan"],"alokasi_total":"{$alokasiWaktu}","tujuan_pembelajaran":[{"kode":"1.1","tujuan":"tujuan spesifik 1","topik":"sub topik 1"},{"kode":"1.2","tujuan":"tujuan spesifik 2","topik":"sub topik 2"},{"kode":"1.3","tujuan":"tujuan spesifik 3","topik":"sub topik 3"},{"kode":"1.4","tujuan":"tujuan spesifik 4","topik":"sub topik 4"},{"kode":"1.5","tujuan":"tujuan spesifik 5","topik":"sub topik 5"},{"kode":"1.6","tujuan":"tujuan spesifik 6","topik":"sub topik 6"},{"kode":"1.7","tujuan":"tujuan spesifik 7","topik":"sub topik 7"},{"kode":"1.8","tujuan":"tujuan spesifik 8","topik":"sub topik 8"},{"kode":"1.9","tujuan":"tujuan spesifik 9","topik":"sub topik 9"}],"praktik_pedagogis":{"model":"{$modelPembelajaran}","metode":"Student Active Learning (diskusi kelompok, tanya jawab, berpikir kritis)","pendekatan":"TPACK"},"kemitraan":["Lingkungan Sekolah = guru relevan","Lingkungan Luar Sekolah = praktisi terkait"],"lingkungan_pembelajaran":["Budaya Belajar : Kolaboratif","Lingkungan Fisik : Ruang Kelas","Lingkungan Virtual : video dan platform online"],"pemanfaatan_digital":["Perencanaan : LMS Google Classroom","Pelaksanaan : Pertemuan luring","Asesmen : teknologi digital"]},"langkah_pembelajaran":{"pembuka":{"alokasi":"10 Menit","deskripsi":"Berkesadaran, Menggembirakan","aktivitas":[{"kegiatan":"Guru mengucapkan salam dengan penuh semangat.","kse":""},{"kegiatan":"Ketua kelas memimpin doa.","kse":"Kesadaran Diri/Self-Awareness"},{"kegiatan":"Murid memeriksa kerapihan pakaian dan kebersihan kelas.","kse":"Manajemen Diri/Self-Management"},{"kegiatan":"Guru mengajak murid berbagi wawasan awal terkait materi.","kse":"Kesadaran Diri/Self-Awareness"},{"kegiatan":"Guru menyampaikan tujuan pembelajaran dan tahapan kegiatan.","kse":""}]},"pertemuan_1":{"fase":"Memahami","tujuan_pembelajaran":["1.1","1.2","1.3","1.4"],"stimulasi":[{"nomor":1,"kegiatan":"kegiatan stimulasi spesifik 1 sesuai topik","kse":""},{"nomor":2,"kegiatan":"kegiatan stimulasi spesifik 2","kse":"Kesadaran Diri/Self-Awareness"},{"nomor":3,"kegiatan":"kegiatan stimulasi spesifik 3","kse":""},{"nomor":4,"kegiatan":"kegiatan stimulasi spesifik 4","kse":"Kesadaran Diri/Self-Awareness"},{"nomor":5,"kegiatan":"kegiatan stimulasi spesifik 5","kse":"Keterampilan Hubungan/Relationship Skills"}],"identifikasi_masalah":[{"nomor":6,"kegiatan":"kegiatan identifikasi masalah 1 spesifik topik","kse":""},{"nomor":7,"kegiatan":"kegiatan identifikasi masalah 2","kse":"Manajemen Diri/Self-Management"},{"nomor":8,"kegiatan":"kegiatan identifikasi masalah 3","kse":""}],"pengumpulan_data":[{"nomor":9,"kegiatan":"kegiatan pengumpulan data 1 spesifik topik","kse":"Keterampilan Hubungan/Relationship Skills"},{"nomor":10,"kegiatan":"kegiatan pengumpulan data 2","kse":""},{"nomor":11,"kegiatan":"kegiatan pengumpulan data 3","kse":""}]},"pertemuan_2":{"fase":"Mengaplikasi","tujuan_pembelajaran":["1.5","1.6","1.7"],"pengolahan_data_1":[{"nomor":12,"kegiatan":"kegiatan pengolahan data 1 spesifik topik","kse":"Keterampilan Hubungan/Relationship Skills"},{"nomor":13,"kegiatan":"kegiatan pengolahan data 2","kse":"Manajemen Diri/Self-Management"},{"nomor":14,"kegiatan":"kegiatan pengolahan data 3","kse":"Keterampilan Hubungan/Relationship Skills"}],"pengolahan_data_2":[{"nomor":15,"kegiatan":"kegiatan pengolahan data lanjutan 1 spesifik topik","kse":"Pengambilan Keputusan Bertanggung Jawab/Responsible Decision-Making"},{"nomor":16,"kegiatan":"kegiatan pengolahan data lanjutan 2","kse":""}]},"pertemuan_3":{"fase":"Merefleksi","tujuan_pembelajaran":["1.8","1.9"],"pembuktian":[{"nomor":17,"kegiatan":"kegiatan pembuktian 1 spesifik topik","kse":"Keterampilan Hubungan/Relationship Skills"},{"nomor":18,"kegiatan":"kegiatan pembuktian 2","kse":"Kesadaran Sosial/Social Awareness"},{"nomor":19,"kegiatan":"kegiatan pembuktian 3","kse":""},{"nomor":20,"kegiatan":"kegiatan pembuktian 4","kse":"Pengambilan Keputusan Bertanggung Jawab/Responsible Decision-Making"}],"penarikan_kesimpulan":[{"nomor":21,"kegiatan":"kegiatan penarikan kesimpulan 1 spesifik topik","kse":""},{"nomor":22,"kegiatan":"kegiatan penarikan kesimpulan 2","kse":"Kesadaran Diri/Self-Awareness"},{"nomor":23,"kegiatan":"kegiatan penarikan kesimpulan 3","kse":"Kesadaran Sosial/Social Awareness"}]},"penutup":{"alokasi":"10 Menit","deskripsi":"Bermakna, Berkesadaran","aktivitas":[{"nomor":24,"kegiatan":"Peserta didik menuliskan satu hal baru yang dipelajari.","kse":"Kesadaran Diri/Self-Awareness"},{"nomor":25,"kegiatan":"Guru merangkum poin-poin penting pembelajaran.","kse":""},{"nomor":26,"kegiatan":"Guru menutup pembelajaran dengan apresiasi.","kse":"Kesadaran Sosial/Social Awareness"}],"penugasan":"tugas spesifik sesuai topik untuk pertemuan berikutnya"}},{$asesmenJson},"lampiran":{"lkpd":{"judul":"LKPD - {$topik}","tujuan":"tujuan LKPD spesifik sesuai topik","petunjuk":["Baca setiap pertanyaan cermat","Kerjakan secara berkelompok","Gunakan berbagai sumber referensi","Tuliskan hasil temuan sistematis"],"kegiatan":[{"nomor":1,"judul":"Eksplorasi Awal","petunjuk":"petunjuk kegiatan 1 spesifik topik","pertanyaan":["pertanyaan eksplorasi 1 spesifik topik?","pertanyaan eksplorasi 2?","pertanyaan eksplorasi 3?"]},{"nomor":2,"judul":"Analisis dan Temuan","petunjuk":"petunjuk kegiatan 2 spesifik topik","pertanyaan":["pertanyaan analisis 1 spesifik topik?","pertanyaan analisis 2?","pertanyaan analisis 3?"]}]},"materi_ajar":{"pendahuluan":"penjelasan konteks topik dalam kehidupan nyata","sub_materi":[{"judul":"Sub Materi 1: Konsep Dasar {$topik}","konten":"penjelasan konsep dasar spesifik topik"},{"judul":"Sub Materi 2: Analisis {$topik}","konten":"analisis mendalam spesifik topik"},{"judul":"Sub Materi 3: Relevansi dan Aplikasi","konten":"relevansi dan aplikasi dalam kehidupan"}],"referensi":["referensi 1 format APA","referensi 2 format APA"]}}}

PENTING: Ganti SEMUA placeholder dengan konten SPESIFIK dan SUBSTANTIF sesuai "{$mataPelajaran}" dan "{$topik}". Output JSON VALID saja tanpa teks lain.
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
                'max_tokens' => 8192,
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

            // Check if response was truncated due to max_tokens
            $finishReason = $result['choices'][0]['finish_reason'] ?? 'unknown';
            if ($finishReason === 'length') {
                Log::warning('DeepSeek STS: Response truncated (finish_reason=length)', [
                    'model' => $this->model,
                ]);
            }

            $content = $this->extractContent($result);

            // Log usage (reuse existing method)
            $this->logUsage($result, $userId, $stsId);

            if (!$content) {
                $rawText = $result['choices'][0]['message']['content'] ?? '(empty)';
                Log::error('DeepSeek STS: Failed to parse AI response content', [
                    'finish_reason' => $finishReason,
                    'raw_response_length' => strlen($rawText),
                    'raw_response_preview' => substr($rawText, 0, 500),
                ]);
                return [
                    'success' => false,
                    'error' => $finishReason === 'length'
                        ? 'Response AI terpotong karena terlalu panjang. Coba kurangi jumlah soal dan coba lagi.'
                        : 'Gagal memproses hasil AI. Format response tidak valid. Silakan coba lagi.',
                ];
            }

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

