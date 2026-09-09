<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AiSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Alat ukur, bukan perbaikan.
 *
 * Dipakai saat generate RPP gagal dengan pesan generik "Gagal membuat RPP".
 * Pesan itu muncul ketika panggilan AI menjawab HTTP 200 tapi isinya tidak bisa
 * dipakai — dan dua dari tiga jalur penyebabnya tidak mencatat apa pun ke log,
 * jadi tanpa alat ini tidak ada yang bisa dibaca.
 *
 * Command ini memakai konfigurasi efektif yang sama dengan DeepSeekService,
 * melakukan satu panggilan kecil, lalu melaporkan bentuk responsnya.
 *
 *   php artisan ai:diagnose
 *
 * API key TIDAK pernah dicetak, hanya panjangnya.
 */
class DiagnoseAi extends Command
{
    protected $signature = 'ai:diagnose {--show-body : Cetak 800 karakter pertama body respons}';

    protected $description = 'Periksa konfigurasi AI efektif dan bentuk respons provider';

    public function handle(): int
    {
        $config = AiSetting::resolved();

        $this->line('');
        $this->info('Konfigurasi efektif (DB dulu, lalu .env)');
        $this->table(['Kunci', 'Nilai'], [
            ['endpoint', $config['endpoint'] ?: '(KOSONG)'],
            ['model', $config['model'] ?: '(KOSONG)'],
            ['temperature', (string) $config['temperature']],
            ['max_tokens', (string) $config['max_tokens']],
            ['api_key', $config['api_key'] ? 'terisi, '.strlen((string) $config['api_key']).' karakter' : '(KOSONG)'],
            ['sumber api_key', AiSetting::getSettings()->api_key ? 'database' : '.env'],
        ]);

        foreach (['api_key', 'endpoint', 'model'] as $wajib) {
            if (blank($config[$wajib])) {
                $this->error("'{$wajib}' kosong. Isi di halaman admin Pengaturan AI atau di .env.");

                return self::FAILURE;
            }
        }

        // Endpoint yang tidak berujung /chat/completions adalah penyebab paling
        // sering: provider menjawab 200 dengan bentuk lain, bukan choices[].
        if (! str_ends_with(rtrim($config['endpoint'], '/'), '/chat/completions')) {
            $this->warn('Endpoint tidak berakhir dengan /chat/completions.');
            $this->line('  Yang benar biasanya: https://api.deepseek.com/v1/chat/completions');
        }

        $this->line('');
        $this->info('Memanggil provider (prompt minimal, max_tokens 2000)…');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$config['api_key'],
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($config['endpoint'], [
                'model' => $config['model'],
                'messages' => [
                    ['role' => 'system', 'content' => 'Jawab hanya dengan JSON valid.'],
                    ['role' => 'user', 'content' => 'Balas tepat: {"ok":true}'],
                ],
                'max_tokens' => 2000,
                'response_format' => ['type' => 'json_object'],
            ]);
        } catch (\Throwable $e) {
            $this->error('Koneksi gagal: '.$e->getMessage());
            $this->line('  Cek apakah shared hosting memblokir koneksi keluar ke domain provider.');

            return self::FAILURE;
        }

        $this->line('  HTTP status : '.$response->status());

        $json = $response->json();
        $this->line('  Kunci teratas: '.(is_array($json) ? implode(', ', array_keys($json)) : '(bukan JSON)'));

        if ($response->failed()) {
            $this->error('Provider menolak permintaan.');
            $this->line('  Body: '.mb_substr($response->body(), 0, 400));

            return self::FAILURE;
        }

        // Persis jalur yang dipakai DeepSeekService::extractContent().
        $text = data_get($json, 'choices.0.message.content');
        $finish = data_get($json, 'choices.0.finish_reason');
        $reasoning = (string) data_get($json, 'choices.0.message.reasoning_content');

        $this->line('  finish_reason: '.($finish ?? '(tidak ada)'));
        $this->line('  token jawaban: '.(data_get($json, 'usage.completion_tokens') ?? '?'));

        if ($reasoning !== '') {
            $this->line('  reasoning     : '.strlen($reasoning).' karakter');
            $this->warn('  Model ini MODEL REASONING.');
            $this->line('    reasoning_content ikut memakan kuota max_tokens, dan content baru');
            $this->line('    ditulis setelah reasoning selesai. Untuk dokumen sepanjang RPP,');
            $this->line("    max_tokens {$config['max_tokens']} bisa habis sebelum content mulai.");
        }

        if (blank($text)) {
            $this->error('choices.0.message.content KOSONG.');
            $this->line('  Inilah penyebab "Gagal membuat RPP" yang tidak berjejak di log.');

            if ($finish === 'length') {
                $this->line('  finish_reason=length: kuota max_tokens habis sebelum content ditulis.');
                $this->line("  PERBAIKAN: naikkan max_tokens (sekarang {$config['max_tokens']}), atau");
                $this->line('  ganti ke model non-reasoning seperti deepseek-chat.');
            } else {
                $this->line('  finish_reason bukan "length", jadi bukan soal kuota: endpoint/model ini');
                $this->line('  tampaknya tidak menjawab dalam format OpenAI chat completions.');
                $this->line('  Body: '.mb_substr($response->body(), 0, 400));
            }

            return self::FAILURE;
        }

        if ($finish === 'length') {
            $this->error('finish_reason=length: jawaban TERPOTONG.');
            $this->line('  Pada prompt RPP yang penuh, potongan ini membuat JSON tidak valid');
            $this->line('  dan generate gagal. Naikkan max_tokens atau pakai model non-reasoning.');

            return self::FAILURE;
        }

        $decoded = json_decode((string) $text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Isi content bukan JSON valid: '.json_last_error_msg());
            $this->line('  Provider mengabaikan response_format json_object.');
            $this->line('  Content: '.mb_substr((string) $text, 0, 400));

            return self::FAILURE;
        }

        if (blank($decoded)) {
            $this->error('Content berupa JSON kosong ({} atau []).');
            $this->line('  json_decode menghasilkan array kosong, dan array kosong itu falsy di PHP,');
            $this->line('  sehingga controller menganggapnya gagal tanpa pesan.');

            return self::FAILURE;
        }

        if ($this->option('show-body')) {
            $this->line('  Body: '.mb_substr($response->body(), 0, 800));
        }

        $this->line('');
        $this->info('Rantai berhasil: HTTP 200 → choices[0].message.content → JSON terurai.');
        $this->line('Konfigurasi AI sehat. Kegagalan generate RPP kemungkinan bukan dari sini —');
        $this->line('curigai timeout pada prompt penuh (jauh lebih panjang dari uji ini) atau');
        $this->line('jawaban terpotong karena max_tokens.');

        return self::SUCCESS;
    }
}
