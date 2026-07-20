<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Rpp;
use App\Models\SchoolSetting;
use Carbon\Carbon;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Image;

/**
 * Ekspor RPPM ke Word dengan tampilan yang sama dengan PDF (rpp/pdf.blade.php):
 * cover bergaya RPPM, ornamen sudut tiap halaman (via header), kata pengantar,
 * daftar isi, tabel header merah, tanda tangan, dan lampiran LKPD.
 */
class RppWordExporter
{
    private const GRAY_TITLE = '4B5563';

    private const BORDER = '666666';

    /** Lebar konten A4 dengan margin 2.5cm, dalam twips (16cm). */
    private const CONTENT_W = 9072;

    private PhpWord $word;

    // Warna tema (tanpa '#'), diisi dari config/rpp_themes.php di export().
    private string $primary = 'B91C1C';

    private string $dark = '7F1D1D';

    private string $accent = 'FACC15';

    private string $themeKey = 'merah';

    public function export(Rpp $rpp, SchoolSetting $schoolSettings): PhpWord
    {
        // Wajib: PHPWord default TIDAK meng-escape XML. Tanpa ini, karakter
        // seperti "&" pada topik menghasilkan document.xml cacat → Word
        // menolak membuka file.
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

        $this->themeKey = $rpp->tema ?: 'merah';
        $theme = config('rpp_themes.'.$this->themeKey) ?? config('rpp_themes.merah');
        $this->primary = strtoupper($theme['primary']);
        $this->dark = strtoupper($theme['dark']);
        $this->accent = strtoupper($theme['accent']);

        $this->word = new PhpWord;
        $this->word->setDefaultFontName('Times New Roman');
        $this->word->setDefaultFontSize(11);
        $this->word->setDefaultParagraphStyle(['spaceAfter' => 120, 'lineHeight' => 1.4]);

        $content = $rpp->content_result ?? [];
        $isKBC = $rpp->kurikulum === 'Kurikulum Berbasis Cinta';
        $schoolName = $schoolSettings->nama_sekolah ?? 'NAMA SEKOLAH';
        $schoolCity = $schoolSettings->kota ?? '';
        $tahunAjaran = date('Y').'/'.(date('Y') + 1);
        $tanggalDok = ($rpp->tanggal ? Carbon::parse($rpp->tanggal) : now())
            ->locale('id')->translatedFormat('d F Y');
        $kotaDok = $rpp->kota ?: ($schoolCity ?: '.............');

        $section = $this->word->addSection([
            'paperSize' => 'A4',
            'marginTop' => Converter::cmToTwip(2.5),
            'marginBottom' => Converter::cmToTwip(2.5),
            'marginLeft' => Converter::cmToTwip(2.5),
            'marginRight' => Converter::cmToTwip(2.5),
            'headerHeight' => Converter::cmToTwip(0.6),
            'footerHeight' => Converter::cmToTwip(0.6),
        ]);

        $this->addDecorations($section);
        $this->addCover($section, $rpp, $schoolSettings, $schoolName, $schoolCity, $tahunAjaran);
        $this->addKataPengantar($section, $rpp, $content, $isKBC, $kotaDok, $tanggalDok);
        $this->addDaftarIsi($section, $content, $isKBC);
        $this->addBody($section, $rpp, $content, $isKBC, $schoolName, $tahunAjaran);
        $this->addSignature($section, $rpp, $schoolName, $kotaDok, $tanggalDok);
        $this->addLkpd($section, $rpp, $content);

        return $this->word;
    }

    /**
     * Ornamen sudut + nomor halaman. Ditempatkan di header/footer supaya
     * berulang otomatis di SEMUA halaman (padanan position:fixed di PDF).
     */
    private function addDecorations($section): void
    {
        $header = $section->addHeader();

        $img = fn (string $file, array $style) => $header->addImage(public_path($file), $style + [
            'positioning' => Image::POSITION_ABSOLUTE,
            'posHorizontalRel' => Image::POSITION_RELATIVE_TO_PAGE,
            'posVerticalRel' => Image::POSITION_RELATIVE_TO_PAGE,
            'wrappingStyle' => 'behind',
        ]);

        // px → pt (0.75) mengikuti ukuran di PDF; ornamen mengikuti tema aktif
        $k = $this->themeKey;
        $img("decor-{$k}-dots.png", [
            'width' => 36, 'height' => 47,
            'posHorizontal' => Image::POSITION_ABSOLUTE, 'marginLeft' => 26,
            'posVertical' => Image::POSITION_ABSOLUTE, 'marginTop' => 26,
        ]);
        $img("decor-{$k}-tr.png", [
            'width' => 135, 'height' => 135,
            'posHorizontal' => Image::POSITION_HORIZONTAL_RIGHT,
            'posVertical' => Image::POSITION_ABSOLUTE, 'marginTop' => 0,
        ]);
        $img("decor-{$k}-bl.png", [
            'width' => 120, 'height' => 120,
            'posHorizontal' => Image::POSITION_ABSOLUTE, 'marginLeft' => 0,
            'posVertical' => Image::POSITION_VERTICAL_BOTTOM,
        ]);
        $img("decor-{$k}-br.png", [
            'width' => 86, 'height' => 86,
            'posHorizontal' => Image::POSITION_HORIZONTAL_RIGHT,
            'posVertical' => Image::POSITION_VERTICAL_BOTTOM,
        ]);

        $footer = $section->addFooter();
        $footer->addPreserveText('{PAGE}', ['bold' => true, 'size' => 11], ['alignment' => Jc::END, 'spaceAfter' => 0]);
    }

    private function addCover($section, Rpp $rpp, SchoolSetting $schoolSettings, string $schoolName, string $schoolCity, string $tahunAjaran): void
    {
        $center = ['alignment' => Jc::CENTER, 'spaceAfter' => 0];

        $section->addTextBreak(2);
        if ($schoolSettings->logo && is_file(storage_path('app/public/'.$schoolSettings->logo))) {
            $section->addImage(storage_path('app/public/'.$schoolSettings->logo), [
                'height' => 64, 'alignment' => Jc::CENTER,
            ]);
        }
        $section->addTextBreak(1);
        $section->addText(mb_strtoupper($schoolName), ['bold' => true, 'size' => 12], $center);
        if ($schoolCity) {
            $section->addText(mb_strtoupper($schoolCity), ['size' => 9, 'color' => '555555'], $center);
        }

        $section->addTextBreak(1);
        $section->addText('RENCANA PELAKSANAAN', ['bold' => true, 'size' => 22, 'color' => self::GRAY_TITLE], $center);
        $section->addText('PEMBELAJARAN MENDALAM', ['bold' => true, 'size' => 22, 'color' => self::GRAY_TITLE], $center);
        $section->addText(mb_strtoupper($rpp->kurikulum ?? 'Kurikulum Merdeka'), ['bold' => true, 'size' => 13, 'color' => self::GRAY_TITLE], $center);
        $section->addText(mb_strtoupper($rpp->mata_pelajaran), ['bold' => true, 'size' => 30, 'color' => $this->accent], $center);
        $section->addText(
            'Semester '.($rpp->semester ?? 'Ganjil').' : Tahun Ajaran '.$tahunAjaran,
            ['size' => 12, 'color' => '6B7280'],
            $center
        );

        $section->addTextBreak(1);
        if (is_file(public_path('garuda.png'))) {
            $section->addImage(public_path('garuda.png'), ['height' => 170, 'alignment' => Jc::CENTER]);
        }

        $section->addTextBreak(1);
        $section->addText('Disusun oleh:', ['size' => 12, 'color' => '374151'], $center);
        $section->addText($rpp->nama_guru, ['bold' => true, 'size' => 16, 'color' => $this->primary], $center);

        $section->addPageBreak();
    }

    private function addKataPengantar($section, Rpp $rpp, array $content, bool $isKBC, string $kotaDok, string $tanggalDok): void
    {
        $section->addText('Kata Pengantar', ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER, 'spaceAfter' => 240]);

        $para = ['alignment' => Jc::BOTH, 'indentation' => ['firstLine' => Converter::cmToTwip(0.9)], 'spaceAfter' => 160, 'lineHeight' => 1.5];
        $mapel = mb_strtolower($rpp->mata_pelajaran);

        $section->addText(
            'Puji syukur kehadirat Tuhan Yang Maha Esa atas segala rahmat dan karunia-Nya sehingga '
            .'Rencana Pelaksanaan Pembelajaran Mendalam (RPPM) dengan topik "'.$rpp->topik.'" ini dapat '
            .'diselesaikan dengan baik. RPPM ini disusun sebagai salah satu perangkat pembelajaran untuk '
            .'mendukung proses pembelajaran yang lebih bermakna bagi peserta didik dalam memahami materi '.$mapel.'.',
            null,
            $para
        );
        $section->addText(
            'Melalui pembelajaran ini, peserta didik diharapkan mampu mengembangkan kompetensi sesuai '
            .'dengan tujuan pembelajaran yang telah ditetapkan. Selain itu, peserta didik juga diharapkan mampu '
            .'menumbuhkan sikap kritis, reflektif, serta mampu menerapkan nilai-nilai yang dipelajari dalam '
            .'kehidupan sehari-hari, bermasyarakat, berbangsa, dan bernegara.',
            null,
            $para
        );
        if ($isKBC) {
            $section->addText(
                'RPPM ini disusun berdasarkan Kurikulum Berbasis Cinta (KBC) Kementerian Agama yang '
                .'mengintegrasikan nilai-nilai cinta, yaitu cinta kepada Allah dan Rasul-Nya, cinta ilmu, cinta '
                .'lingkungan, cinta diri dan sesama manusia, serta cinta tanah air. Integrasi nilai-nilai tersebut '
                .'diharapkan mampu membentuk peserta didik yang berakhlak mulia dan moderat dalam beragama.',
                null,
                $para
            );
        } else {
            $section->addText(
                'RPPM ini juga mengintegrasikan dimensi Profil Pelajar Pancasila yang meliputi beriman dan '
                .'bertakwa kepada Tuhan Yang Maha Esa, mandiri, bergotong royong, bernalar kritis, kreatif, serta '
                .'berkebinekaan global. Integrasi dimensi tersebut diharapkan mampu mendukung pengembangan '
                .'karakter peserta didik secara holistik.',
                null,
                $para
            );
        }
        if (isset($content['integrasi_panca_cinta']) || isset($content['integrasi_adiwiyata']) || isset($content['integrasi_kka'])) {
            $bagian = [];
            if (isset($content['integrasi_panca_cinta'])) {
                $bagian[] = 'integrasi nilai-nilai Panca Cinta';
            }
            if (isset($content['integrasi_adiwiyata'])) {
                $bagian[] = 'integrasi program Adiwiyata (Sekolah Peduli dan Berbudaya Lingkungan)';
            }
            if (isset($content['integrasi_kka'])) {
                $bagian[] = 'integrasi Koding dan Kecerdasan Artifisial (KKA)';
            }
            $section->addText(
                'Selain itu, RPPM ini memuat '.implode(' serta ', $bagian).' yang dihubungkan secara '
                .'kontekstual dengan topik pembelajaran, sehingga penanaman karakter dan kepedulian lingkungan '
                .'menyatu dalam aktivitas belajar peserta didik.',
                null,
                $para
            );
        }
        $section->addText(
            'Ucapan terima kasih disampaikan kepada berbagai pihak yang telah memberikan dukungan, masukan, '
            .'serta bimbingan dalam penyusunan RPPM ini. Semoga RPPM ini dapat memberikan manfaat dalam '
            .'mendukung proses pembelajaran '.$mapel.' yang lebih bermakna dan kontekstual.',
            null,
            $para
        );

        $section->addTextBreak(1);
        $right = ['alignment' => Jc::END, 'spaceAfter' => 0];
        $section->addText($kotaDok.', '.$tanggalDok, null, $right);
        $section->addTextBreak(3);
        $section->addText($rpp->nama_guru, ['bold' => true], $right);

        $section->addPageBreak();
    }

    private function addDaftarIsi($section, array $content, bool $isKBC): void
    {
        $section->addText('Daftar Isi', ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER, 'spaceAfter' => 240]);

        // Tab kanan dengan leader titik-titik (padanan .daftar-isi .dots di PDF)
        $tabStyle = [
            'tabs' => [new \PhpOffice\PhpWord\Style\Tab('right', self::CONTENT_W, 'dot')],
            'spaceAfter' => 100,
        ];
        $row = fn (string $label, string $page, bool $bold = false, int $indent = 0) => $section->addText(
            str_repeat('    ', $indent).$label."\t".$page,
            $bold ? ['bold' => true] : null,
            $tabStyle
        );

        $row('Kata Pengantar', 'ii');
        $row('Daftar Isi', 'iii');
        $row('Rencana Pelaksanaan Pembelajaran Mendalam (RPPM)', '1', true);

        $items = $this->tocItems($content, $isKBC);
        $letter = 'A';
        foreach ($items as $i => $item) {
            $row($letter++.'. '.$item, (string) (1 + intdiv($i, 2)), false, 1);
        }
        if (isset($content['lkpd'])) {
            $row('Lampiran : Lembar Kerja Peserta Didik (LKPD)', (string) (2 + intdiv(count($items), 2)), true);
        }

        $section->addPageBreak();
    }

    /** Daftar section aktif — urutan sama dengan body & daftar isi PDF. */
    private function tocItems(array $content, bool $isKBC): array
    {
        $items = ['Informasi Umum'];
        if (! empty($content['kompetensi_awal'])) {
            $items[] = 'Kompetensi Awal';
        }
        if ($isKBC && isset($content['nilai_nilai_cinta'])) {
            $items[] = 'Nilai-Nilai Cinta';
        }
        if ($isKBC && isset($content['profil_lulusan_madrasah'])) {
            $items[] = 'Profil Lulusan Madrasah';
        }
        if ($isKBC && isset($content['moderasi_beragama'])) {
            $items[] = 'Moderasi Beragama';
        }
        if (isset($content['profil_pelajar_pancasila'])) {
            $items[] = 'Profil Pelajar Pancasila';
        }
        if (isset($content['sarana_prasarana'])) {
            $items[] = 'Sarana dan Prasarana';
        }
        if (isset($content['tujuan_pembelajaran'])) {
            $items[] = 'Tujuan Pembelajaran';
        }
        if (! empty($content['pemahaman_bermakna'])) {
            $items[] = 'Pemahaman Bermakna';
        }
        if (isset($content['pertanyaan_pemantik'])) {
            $items[] = 'Pertanyaan Pemantik';
        }
        if (isset($content['kegiatan_pembelajaran'])) {
            $items[] = 'Kegiatan Pembelajaran';
        }
        if (isset($content['asesmen'])) {
            $items[] = 'Asesmen Pembelajaran';
        }
        if (isset($content['pengayaan_remedial'])) {
            $items[] = 'Pengayaan dan Remedial';
        }
        if (isset($content['refleksi']) || isset($content['refleksi_guru'])) {
            $items[] = 'Refleksi';
        }
        if (isset($content['integrasi_panca_cinta'])) {
            $items[] = 'Integrasi Panca Cinta';
        }
        if (isset($content['integrasi_adiwiyata'])) {
            $items[] = 'Integrasi Adiwiyata';
        }
        if (isset($content['integrasi_kka'])) {
            $items[] = 'Integrasi Koding & Kecerdasan Artifisial';
        }
        if (isset($content['glosarium'])) {
            $items[] = 'Glosarium';
        }
        if (isset($content['daftar_pustaka'])) {
            $items[] = 'Daftar Pustaka';
        }

        return $items;
    }

    // ================= TABEL DASAR =================

    /** Tint hex 90% ke putih (latar kolom tahap). Input & output tanpa '#'. */
    private function tint(string $hex): string
    {
        $mix = fn (int $c) => (int) round($c + (255 - $c) * 0.9);

        return sprintf('%02X%02X%02X',
            $mix(hexdec(substr($hex, 0, 2))),
            $mix(hexdec(substr($hex, 2, 2))),
            $mix(hexdec(substr($hex, 4, 2)))
        );
    }

    private function tableStyle(): array
    {
        return [
            'borderSize' => 6,
            'borderColor' => self::BORDER,
            'cellMargin' => 100,
            'width' => self::CONTENT_W,
            'unit' => 'dxa',
        ];
    }

    private function headerCell($row, int $width, string $text): void
    {
        $row->addCell($width, ['bgColor' => $this->primary, 'valign' => 'center'])
            ->addText($text, ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
    }

    private function labelCell($row, int $width, string $text): void
    {
        $row->addCell($width, ['bgColor' => 'FAFAFA'])
            ->addText($text, ['bold' => true], ['spaceAfter' => 0]);
    }

    private function textCell($row, int $width, string $text, array $font = [], array $par = []): void
    {
        $row->addCell($width)->addText($text, $font ?: null, $par + ['spaceAfter' => 0]);
    }

    private function sectionLetter($section, string $letter, string $title): void
    {
        $section->addText($letter.'. '.$title, ['bold' => true, 'size' => 12], ['spaceBefore' => 220, 'spaceAfter' => 100]);
    }

    /** Tabel 2 kolom label merah generik (dimensi/deskripsi dst). */
    private function twoColRedTable($section, string $head1, string $head2, array $rows): void
    {
        $table = $section->addTable($this->tableStyle());
        $hr = $table->addRow();
        $this->headerCell($hr, 2900, $head1);
        $this->headerCell($hr, self::CONTENT_W - 2900, $head2);
        foreach ($rows as [$label, $value]) {
            $r = $table->addRow();
            $this->labelCell($r, 2900, $label);
            $this->textCell($r, self::CONTENT_W - 2900, $value);
        }
    }

    // ================= BODY =================

    private function addBody($section, Rpp $rpp, array $content, bool $isKBC, string $schoolName, string $tahunAjaran): void
    {
        $section->addText(
            'Rencana Pelaksanaan Pembelajaran Mendalam (RPPM)',
            ['bold' => true, 'size' => 14],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 240]
        );

        // Blok identitas tanpa border
        $info = $section->addTable(['borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 40, 'width' => self::CONTENT_W, 'unit' => 'dxa']);
        foreach ([
            ['Nama Satuan Pendidikan', $schoolName],
            ['Kelas/Fase', ($rpp->kelas ?: '-').' / Fase '.$rpp->fase],
            ['Tahun Pelajaran', $tahunAjaran],
            ['Mata Pelajaran', $rpp->mata_pelajaran],
        ] as [$k, $v]) {
            $r = $info->addRow();
            $r->addCell(2900)->addText($k, null, ['spaceAfter' => 0]);
            $r->addCell(self::CONTENT_W - 2900)->addText(': '.$v, null, ['spaceAfter' => 0]);
        }

        $huruf = 'A';

        // INFORMASI UMUM
        $this->sectionLetter($section, $huruf++, 'Informasi Umum');
        $rows = [
            ['Nama Penyusun', $rpp->nama_guru],
            ['Topik / Materi', $rpp->topik],
            ['Semester', $rpp->semester ?? '-'],
            ['Alokasi Waktu', $rpp->alokasi_waktu.($rpp->jumlah_pertemuan ? ' ('.$rpp->jumlah_pertemuan.' pertemuan)' : '')],
            ['Model Pembelajaran', (string) $rpp->model_pembelajaran],
            ['Kurikulum', $rpp->kurikulum ?? 'Kurikulum Merdeka'],
            ['Jenis Asesmen', $rpp->jenis_asesmen ?? 'Formatif dan Sumatif'],
        ];
        if ($rpp->target_peserta_didik) {
            $rows[] = ['Target Peserta Didik', $rpp->target_peserta_didik];
        }
        $this->twoColRedTable($section, 'Komponen', 'Keterangan', $rows);

        // KOMPETENSI AWAL
        if (! empty($content['kompetensi_awal'])) {
            $this->sectionLetter($section, $huruf++, 'Kompetensi Awal');
            $table = $section->addTable($this->tableStyle());
            $this->headerCell($table->addRow(), self::CONTENT_W, 'Kompetensi Awal Peserta Didik');
            $this->textCell($table->addRow(), self::CONTENT_W, (string) $content['kompetensi_awal']);
        }

        // NILAI-NILAI CINTA (KBC)
        if ($isKBC && isset($content['nilai_nilai_cinta'])) {
            $this->sectionLetter($section, $huruf++, 'Nilai-Nilai Cinta (Kurikulum Berbasis Cinta)');
            $this->twoColRedTable($section, 'Dimensi Cinta', 'Deskripsi Pengembangan', $this->dimensiRows($content['nilai_nilai_cinta']));
        }

        // PROFIL LULUSAN MADRASAH (KBC)
        if ($isKBC && isset($content['profil_lulusan_madrasah'])) {
            $this->sectionLetter($section, $huruf++, 'Profil Lulusan Madrasah');
            $this->twoColRedTable($section, 'Dimensi', 'Deskripsi', $this->dimensiRows($content['profil_lulusan_madrasah']));
        }

        // MODERASI BERAGAMA (KBC)
        if ($isKBC && isset($content['moderasi_beragama'])) {
            $this->sectionLetter($section, $huruf++, 'Moderasi Beragama (Wasathiyah)');
            $table = $section->addTable($this->tableStyle());
            if (! empty($content['moderasi_beragama']['nilai_wasathiyah'])) {
                $this->headerCell($table->addRow(), self::CONTENT_W, 'Nilai Wasathiyah');
                $this->textCell($table->addRow(), self::CONTENT_W, (string) $content['moderasi_beragama']['nilai_wasathiyah']);
            }
            if (! empty($content['moderasi_beragama']['implementasi'])) {
                $this->headerCell($table->addRow(), self::CONTENT_W, 'Implementasi dalam Pembelajaran');
                $cell = $table->addRow()->addCell(self::CONTENT_W);
                foreach ($content['moderasi_beragama']['implementasi'] as $i => $item) {
                    $cell->addText(($i + 1).'. '.$item, null, ['spaceAfter' => 40]);
                }
            }
        }

        // PROFIL PELAJAR PANCASILA
        if (isset($content['profil_pelajar_pancasila'])) {
            $this->sectionLetter($section, $huruf++, 'Profil Pelajar Pancasila');
            $this->twoColRedTable($section, 'Dimensi', 'Deskripsi Pengembangan', $this->dimensiRows($content['profil_pelajar_pancasila']));
        }

        // SARANA PRASARANA
        if (isset($content['sarana_prasarana']) && is_array($content['sarana_prasarana'])) {
            $this->sectionLetter($section, $huruf++, 'Sarana dan Prasarana');
            $table = $section->addTable($this->tableStyle());
            foreach (['alat' => 'Alat', 'bahan' => 'Bahan', 'media' => 'Media', 'sumber_belajar' => 'Sumber Belajar'] as $key => $label) {
                if (! empty($content['sarana_prasarana'][$key])) {
                    $value = $content['sarana_prasarana'][$key];
                    $r = $table->addRow();
                    $this->labelCell($r, 2900, $label);
                    $this->textCell($r, self::CONTENT_W - 2900, is_array($value) ? implode(', ', $value) : (string) $value);
                }
            }
        }

        // TUJUAN PEMBELAJARAN
        if (isset($content['tujuan_pembelajaran'])) {
            $this->sectionLetter($section, $huruf++, 'Tujuan Pembelajaran');
            $table = $section->addTable($this->tableStyle());
            $hr = $table->addRow();
            $this->headerCell($hr, 900, 'No');
            $this->headerCell($hr, self::CONTENT_W - 900, 'Tujuan Pembelajaran');
            foreach ($content['tujuan_pembelajaran'] as $i => $tujuan) {
                $r = $table->addRow();
                $this->textCell($r, 900, (string) ($i + 1), [], ['alignment' => Jc::CENTER]);
                $this->textCell($r, self::CONTENT_W - 900, (string) $tujuan);
            }
        }

        // PEMAHAMAN BERMAKNA
        if (! empty($content['pemahaman_bermakna'])) {
            $this->sectionLetter($section, $huruf++, 'Pemahaman Bermakna');
            $table = $section->addTable($this->tableStyle());
            $this->headerCell($table->addRow(), self::CONTENT_W, 'Pemahaman Bermakna');
            $this->textCell($table->addRow(), self::CONTENT_W, (string) $content['pemahaman_bermakna']);
        }

        // PERTANYAAN PEMANTIK
        if (isset($content['pertanyaan_pemantik'])) {
            $this->sectionLetter($section, $huruf++, 'Pertanyaan Pemantik');
            $table = $section->addTable($this->tableStyle());
            $hr = $table->addRow();
            $this->headerCell($hr, 900, 'No');
            $this->headerCell($hr, self::CONTENT_W - 900, 'Pertanyaan');
            foreach ($content['pertanyaan_pemantik'] as $i => $pertanyaan) {
                $r = $table->addRow();
                $this->textCell($r, 900, (string) ($i + 1), [], ['alignment' => Jc::CENTER]);
                $this->textCell($r, self::CONTENT_W - 900, (string) $pertanyaan);
            }
        }

        // KEGIATAN PEMBELAJARAN
        if (isset($content['kegiatan_pembelajaran'])) {
            $this->sectionLetter($section, $huruf++, 'Kegiatan Pembelajaran');
            $this->addKegiatanTable($section, $content['kegiatan_pembelajaran']);
        }

        // ASESMEN
        if (isset($content['asesmen'])) {
            $this->sectionLetter($section, $huruf++, 'Asesmen Pembelajaran');
            $this->addAsesmen($section, $rpp, $content['asesmen']);
        }

        // PENGAYAAN & REMEDIAL
        if (isset($content['pengayaan_remedial'])) {
            $this->sectionLetter($section, $huruf++, 'Pengayaan dan Remedial');
            $table = $section->addTable($this->tableStyle());
            $hr = $table->addRow();
            $this->headerCell($hr, 1700, 'Program');
            $this->headerCell($hr, 2900, 'Sasaran');
            $this->headerCell($hr, self::CONTENT_W - 4600, 'Kegiatan');
            foreach (['pengayaan' => 'Pengayaan', 'remedial' => 'Remedial'] as $key => $label) {
                $prog = $content['pengayaan_remedial'][$key] ?? null;
                if (! $prog) {
                    continue;
                }
                $r = $table->addRow();
                $this->labelCell($r, 1700, $label);
                $this->textCell($r, 2900, (string) ($prog['sasaran'] ?? '-'));
                $cell = $r->addCell(self::CONTENT_W - 4600);
                foreach ($prog['kegiatan'] ?? [] as $i => $kegiatan) {
                    $cell->addText(($i + 1).'. '.$kegiatan, null, ['spaceAfter' => 40]);
                }
            }
        }

        // REFLEKSI
        if (isset($content['refleksi']) || isset($content['refleksi_guru'])) {
            $this->sectionLetter($section, $huruf++, 'Refleksi');
            $table = $section->addTable($this->tableStyle());
            $siswa = $content['refleksi']['refleksi_siswa'] ?? [];
            $guru = $content['refleksi']['refleksi_guru'] ?? $content['refleksi_guru'] ?? [];
            foreach ([['Refleksi Peserta Didik', $siswa], ['Refleksi Guru', $guru]] as [$label, $itemList]) {
                if (empty($itemList)) {
                    continue;
                }
                $this->headerCell($table->addRow(), self::CONTENT_W, $label);
                $cell = $table->addRow()->addCell(self::CONTENT_W);
                foreach ($itemList as $i => $item) {
                    $cell->addText(($i + 1).'. '.$item, null, ['spaceAfter' => 40]);
                }
            }
        }

        // INTEGRASI PANCA CINTA
        if (isset($content['integrasi_panca_cinta'])) {
            $this->sectionLetter($section, $huruf++, 'Integrasi Panca Cinta');
            $rows = [];
            foreach ($content['integrasi_panca_cinta'] as $item) {
                if (is_array($item)) {
                    $rows[] = [(string) ($item['nilai'] ?? '-'), (string) ($item['implementasi'] ?? '-')];
                }
            }
            $this->twoColRedTable($section, 'Nilai Panca Cinta', 'Implementasi dalam Pembelajaran', $rows);
        }

        // INTEGRASI ADIWIYATA
        if (isset($content['integrasi_adiwiyata'])) {
            $this->sectionLetter($section, $huruf++, 'Integrasi Adiwiyata');
            $rows = [];
            foreach ($content['integrasi_adiwiyata'] as $item) {
                if (is_array($item)) {
                    $rows[] = [(string) ($item['komponen'] ?? '-'), (string) ($item['kegiatan'] ?? '-')];
                }
            }
            $this->twoColRedTable($section, 'Komponen Adiwiyata', 'Kegiatan / Aksi Nyata', $rows);
        }

        // INTEGRASI KKA (KODING & KECERDASAN ARTIFISIAL)
        if (isset($content['integrasi_kka'])) {
            $this->sectionLetter($section, $huruf++, 'Integrasi Koding & Kecerdasan Artifisial (KKA)');
            $rows = [];
            foreach ($content['integrasi_kka'] as $item) {
                if (is_array($item)) {
                    $rows[] = [(string) ($item['aspek'] ?? '-'), (string) ($item['implementasi'] ?? '-')];
                }
            }
            $this->twoColRedTable($section, 'Aspek KKA', 'Implementasi dalam Pembelajaran', $rows);
        }

        // GLOSARIUM
        if (! empty($content['glosarium'])) {
            $this->sectionLetter($section, $huruf++, 'Glosarium');
            $rows = [];
            foreach ($content['glosarium'] as $item) {
                if (is_array($item)) {
                    $rows[] = [(string) ($item['istilah'] ?? '-'), (string) ($item['definisi'] ?? '-')];
                }
            }
            $this->twoColRedTable($section, 'Istilah', 'Definisi', $rows);
        }

        // DAFTAR PUSTAKA
        if (! empty($content['daftar_pustaka'])) {
            $this->sectionLetter($section, $huruf++, 'Daftar Pustaka');
            foreach ($content['daftar_pustaka'] as $i => $pustaka) {
                $section->addText(($i + 1).'. '.$pustaka, null, ['spaceAfter' => 60]);
            }
        }
    }

    /** Normalisasi list dimensi/deskripsi (array asosiatif atau string). */
    private function dimensiRows(array $items): array
    {
        $rows = [];
        foreach ($items as $item) {
            if (is_array($item)) {
                $rows[] = [(string) ($item['dimensi'] ?? '-'), (string) ($item['deskripsi'] ?? '-')];
            } else {
                $rows[] = [(string) $item, ''];
            }
        }

        return $rows;
    }

    private function addKegiatanTable($section, array $kegiatan): void
    {
        $table = $section->addTable($this->tableStyle());
        $hr = $table->addRow();
        $this->headerCell($hr, 1900, 'Tahap');
        $this->headerCell($hr, self::CONTENT_W - 1900, 'Langkah-Langkah Pembelajaran');

        foreach (['pendahuluan' => 'Pendahuluan', 'inti' => 'Kegiatan Inti', 'penutup' => 'Penutup'] as $key => $label) {
            $tahap = $kegiatan[$key] ?? null;
            if (! $tahap) {
                continue;
            }
            $r = $table->addRow();
            $tahapCell = $r->addCell(1900, ['bgColor' => $this->tint($this->primary), 'valign' => 'center']);
            $tahapCell->addText($label, ['bold' => true, 'color' => $this->primary, 'size' => 12], ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
            if (! empty($tahap['durasi'])) {
                $tahapCell->addText('('.$tahap['durasi'].')', ['size' => 9, 'color' => '444444'], ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
            }

            $cell = $r->addCell(self::CONTENT_W - 1900);
            if ($key === 'inti' && ! empty($tahap['sintaks_model'])) {
                $cell->addText('Model: '.$tahap['sintaks_model'], ['bold' => true], ['spaceAfter' => 60]);
            }
            $faseSebelumnya = null;
            $no = 0;
            foreach ($tahap['aktivitas'] ?? [] as $akt) {
                if (is_array($akt)) {
                    if (! empty($akt['fase_sintaks']) && $akt['fase_sintaks'] !== $faseSebelumnya) {
                        $fase = $akt['fase_sintaks'].(! empty($akt['durasi']) ? ' ('.$akt['durasi'].')' : '');
                        $cell->addText($fase, ['bold' => true, 'italic' => true], ['spaceBefore' => 80, 'spaceAfter' => 40]);
                        $faseSebelumnya = $akt['fase_sintaks'];
                    }
                    $no++;
                    $cell->addText($no.'. Guru: '.($akt['kegiatan_guru'] ?? '-'), null, ['spaceAfter' => 0]);
                    $cell->addText('Peserta didik: '.($akt['kegiatan_siswa'] ?? '-'), null, [
                        'indentation' => ['left' => Converter::cmToTwip(0.5)], 'spaceAfter' => 60,
                    ]);
                } else {
                    $no++;
                    $cell->addText($no.'. '.$akt, null, ['spaceAfter' => 60]);
                }
            }
        }
    }

    private function addAsesmen($section, Rpp $rpp, array $asesmen): void
    {
        $table = $section->addTable($this->tableStyle());
        $teknik = $asesmen['teknik'] ?? '-';
        $rows = [
            ['Jenis Asesmen', (string) ($asesmen['jenis'] ?? ($rpp->jenis_asesmen ?? '-'))],
            ['Teknik Asesmen', is_array($teknik) ? implode(', ', $teknik) : (string) $teknik],
        ];
        if (! empty($asesmen['bentuk'])) {
            $rows[] = ['Bentuk Asesmen', (string) $asesmen['bentuk']];
        }
        foreach ($rows as [$label, $value]) {
            $r = $table->addRow();
            $this->labelCell($r, 2900, $label);
            $this->textCell($r, self::CONTENT_W - 2900, $value);
        }

        if (! empty($asesmen['instrumen'])) {
            $section->addText('Instrumen Asesmen', ['bold' => true], ['spaceBefore' => 160, 'spaceAfter' => 80]);
            foreach ($asesmen['instrumen'] as $instrumen) {
                if (! is_array($instrumen)) {
                    continue;
                }
                $box = $section->addTable(['borderSize' => 4, 'borderColor' => 'DDDDDD', 'cellMargin' => 100, 'width' => self::CONTENT_W, 'unit' => 'dxa']);
                $cell = $box->addRow()->addCell(self::CONTENT_W, ['bgColor' => 'FCFCFC']);
                $cell->addText((string) ($instrumen['jenis'] ?? 'Instrumen'), ['bold' => true, 'color' => $this->primary], ['spaceAfter' => 40]);
                if (! empty($instrumen['deskripsi'])) {
                    $cell->addText((string) $instrumen['deskripsi'], null, ['spaceAfter' => 40]);
                }
                if (! empty($instrumen['contoh_soal'])) {
                    $cell->addText('Contoh Soal/Tugas:', ['bold' => true], ['spaceAfter' => 20]);
                    foreach ($instrumen['contoh_soal'] as $i => $soal) {
                        $cell->addText(($i + 1).'. '.$soal, null, ['spaceAfter' => 20]);
                    }
                }
                $section->addTextBreak(1);
            }
        }

        $rubrik = $asesmen['rubrik_penilaian'] ?? $asesmen['rubrik'] ?? [];
        if (is_array($rubrik) && count($rubrik) > 0) {
            $section->addText('Rubrik Penilaian', ['bold' => true], ['spaceBefore' => 160, 'spaceAfter' => 80]);
            $table = $section->addTable($this->tableStyle());
            $hr = $table->addRow();
            $w = intdiv(self::CONTENT_W - 1900, 4);
            $this->headerCell($hr, 1900, 'Kriteria');
            $this->headerCell($hr, $w, 'Sangat Baik (4)');
            $this->headerCell($hr, $w, 'Baik (3)');
            $this->headerCell($hr, $w, 'Cukup (2)');
            $this->headerCell($hr, $w, 'Perlu Perbaikan (1)');
            foreach ($rubrik as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $r = $table->addRow();
                $this->labelCell($r, 1900, (string) ($item['kriteria'] ?? '-'));
                foreach (['skor_4', 'skor_3', 'skor_2', 'skor_1'] as $skor) {
                    $this->textCell($r, $w, (string) ($item[$skor] ?? '-'));
                }
            }
        }
    }

    private function addSignature($section, Rpp $rpp, string $schoolName, string $kotaDok, string $tanggalDok): void
    {
        $section->addTextBreak(1);
        $table = $section->addTable(['borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 40, 'width' => self::CONTENT_W, 'unit' => 'dxa']);
        $half = intdiv(self::CONTENT_W, 2);
        $center = ['alignment' => Jc::CENTER, 'spaceAfter' => 0];

        $r = $table->addRow();
        $r->addCell($half);
        $r->addCell($half)->addText($kotaDok.', '.$tanggalDok, null, $center);

        $r = $table->addRow();
        $left = $r->addCell($half);
        $left->addText('Mengetahui,', null, $center);
        $left->addText('Kepala '.$schoolName, null, $center);
        $left->addTextBreak(3);
        $left->addText($rpp->kepala_sekolah ?: '.................................', ['bold' => true, 'underline' => 'single'], $center);
        $left->addText('NIP. '.($rpp->nip_kepala_sekolah ?: '.................................'), ['size' => 10], $center);

        $right = $r->addCell($half);
        $right->addText('Guru Mata Pelajaran', null, $center);
        $right->addText($rpp->mata_pelajaran, null, $center);
        $right->addTextBreak(3);
        $right->addText($rpp->nama_guru, ['bold' => true, 'underline' => 'single'], $center);
        $right->addText('NIP. -', ['size' => 10], $center);
    }

    private function addLkpd($section, Rpp $rpp, array $content): void
    {
        if (! isset($content['lkpd'])) {
            return;
        }
        $lkpd = $content['lkpd'];

        $section->addPageBreak();
        $section->addText('Lampiran', ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER, 'spaceAfter' => 160]);
        $section->addText('Lampiran 1 : Lembar Kerja Peserta Didik (LKPD)', ['bold' => true], ['spaceAfter' => 120]);

        // Bingkai LKPD = tabel satu sel berborder merah (padanan .lkpd-wrapper)
        $frame = $section->addTable(['borderSize' => 12, 'borderColor' => $this->primary, 'cellMargin' => 160, 'width' => self::CONTENT_W, 'unit' => 'dxa']);
        $cell = $frame->addRow()->addCell(self::CONTENT_W);

        $cell->addText((string) ($lkpd['judul'] ?? 'LEMBAR KERJA PESERTA DIDIK'), ['bold' => true, 'size' => 13, 'color' => $this->primary], ['alignment' => Jc::CENTER, 'spaceAfter' => 20]);
        $cell->addText($rpp->mata_pelajaran.' — Fase '.$rpp->fase, ['size' => 10, 'color' => '666666'], ['alignment' => Jc::CENTER, 'spaceAfter' => 160]);

        foreach (['Nama', 'Kelas', 'Tanggal'] as $field) {
            $cell->addText($field."\t: ................................................", null, [
                'tabs' => [new \PhpOffice\PhpWord\Style\Tab('left', 1700)], 'spaceAfter' => 40,
            ]);
        }

        if (! empty($lkpd['tujuan'])) {
            $cell->addText('Tujuan Kegiatan:', ['bold' => true], ['spaceBefore' => 120, 'spaceAfter' => 20]);
            $cell->addText((string) $lkpd['tujuan'], null, ['spaceAfter' => 60]);
        }

        $petunjuk = $lkpd['petunjuk_umum'] ?? $lkpd['petunjuk_pengerjaan'] ?? $lkpd['petunjuk'] ?? [];
        if (! empty($petunjuk)) {
            $cell->addText('Petunjuk Pengerjaan:', ['bold' => true], ['spaceBefore' => 80, 'spaceAfter' => 20]);
            foreach ($petunjuk as $i => $p) {
                $cell->addText(($i + 1).'. '.$p, null, ['spaceAfter' => 20]);
            }
        }

        foreach ($lkpd['kegiatan'] ?? [] as $idx => $keg) {
            $judul = $keg['judul_kegiatan'] ?? $keg['judul'] ?? '';
            $cell->addText(
                'Kegiatan '.($keg['nomor'] ?? $idx + 1).': '.$judul,
                ['bold' => true, 'color' => $this->primary],
                ['spaceBefore' => 160, 'spaceAfter' => 20]
            );
            if (! empty($keg['petunjuk'])) {
                $cell->addText((string) $keg['petunjuk'], ['italic' => true], ['spaceAfter' => 40]);
            }
            $soalList = [];
            foreach ($keg['soal_tugas'] ?? [] as $soal) {
                $soalList[] = ($soal['nomor'] ?? '').'. '.($soal['pertanyaan'] ?? '');
            }
            foreach ($keg['pertanyaan'] ?? [] as $i => $pq) {
                $soalList[] = ($i + 1).'. '.$pq;
            }
            foreach ($soalList as $soalText) {
                $cell->addText($soalText, null, ['spaceAfter' => 20]);
                $cell->addText('Jawaban: ............................................................................................................', ['size' => 9, 'color' => '999999'], ['spaceAfter' => 20]);
                $cell->addText('............................................................................................................................', ['size' => 9, 'color' => '999999'], ['spaceAfter' => 80]);
            }
        }

        if (! empty($lkpd['kesimpulan'])) {
            $cell->addText('Kesimpulan:', ['bold' => true], ['spaceBefore' => 160, 'spaceAfter' => 20]);
            $cell->addText((string) $lkpd['kesimpulan'], null, ['spaceAfter' => 40]);
            $cell->addText('Tulis kesimpulanmu di sini: ....................................................................................', ['size' => 9, 'color' => '999999'], ['spaceAfter' => 20]);
            $cell->addText('............................................................................................................................', ['size' => 9, 'color' => '999999'], ['spaceAfter' => 0]);
        }
    }
}
