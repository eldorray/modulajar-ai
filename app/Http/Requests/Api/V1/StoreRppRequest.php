<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreRppRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            '_idempotency_key' => ['required', 'string', 'min:16', 'max:128'],
            'nama_guru' => ['required', 'string', 'max:255'],
            'kepala_sekolah' => ['nullable', 'string', 'max:255'],
            'nip_kepala_sekolah' => ['nullable', 'string', 'max:50'],
            'kota' => ['nullable', 'string', 'max:100'],
            'tanggal' => ['nullable', 'date'],
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'fase' => ['required', 'string', 'in:A,B,C,D,E,F,RA,MI Rendah,MI Tinggi,MTs,MA'],
            'kelas' => ['nullable', 'string', 'max:20'],
            'semester' => ['nullable', 'string', 'in:Ganjil,Genap'],
            'target_peserta_didik' => ['nullable', 'string', 'max:100'],
            'topik' => ['required', 'string', 'max:1000'],
            'alokasi_waktu' => ['required', 'string', 'max:100'],
            'jumlah_pertemuan' => ['nullable', 'integer', 'min:1', 'max:10'],
            'kompetensi_awal' => ['nullable', 'string', 'max:1000'],
            'kata_kunci' => ['nullable', 'string', 'max:500'],
            'model_pembelajaran' => ['nullable', 'string', 'max:255'],
            'jenis_asesmen' => ['nullable', 'array'],
            'jenis_asesmen.*' => ['string', 'in:Diagnostik Kognitif,Diagnostik Non-Kognitif,Formatif,Sumatif'],
            'kurikulum' => ['required', 'string', 'max:255'],
            'tema' => ['nullable', 'string', 'in:'.implode(',', array_keys(config('rpp_themes')))],
            'panca_cinta' => ['nullable', 'boolean'],
            'adiwiyata' => ['nullable', 'boolean'],
            'kka' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, mixed> */
    public function generationInput(): array
    {
        $data = $this->validated();
        $assessments = $data['jenis_asesmen'] ?? ['Formatif', 'Sumatif'];
        if ($assessments === []) {
            $assessments = ['Formatif', 'Sumatif'];
        }

        return [
            ...$data,
            'tanggal' => $data['tanggal'] ?? now()->toDateString(),
            'target_peserta_didik' => $data['target_peserta_didik'] ?? 'Reguler',
            'jumlah_pertemuan' => $data['jumlah_pertemuan'] ?? 1,
            'model_pembelajaran' => $data['model_pembelajaran'] ?? 'Problem Based Learning',
            'jenis_asesmen' => implode(', ', $assessments),
            'jenis_asesmen_array' => array_values($assessments),
            'tema' => $data['tema'] ?? 'merah',
            'panca_cinta' => $this->boolean('panca_cinta'),
            'adiwiyata' => $this->boolean('adiwiyata'),
            'kka' => $this->boolean('kka'),
        ];
    }

    public function idempotencyKey(): string
    {
        return (string) $this->header('Idempotency-Key');
    }

    protected function prepareForValidation(): void
    {
        if (! $this->hasHeader('Idempotency-Key')) {
            $this->merge(['_idempotency_key' => null]);
        }
    }

    /** @return array<string, mixed> */
    public function validationData(): array
    {
        return [...parent::validationData(), '_idempotency_key' => $this->header('Idempotency-Key')];
    }
}
