<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RppResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_guru' => $this->nama_guru,
            'mata_pelajaran' => $this->mata_pelajaran,
            'fase' => $this->fase,
            'kelas' => $this->kelas,
            'semester' => $this->semester,
            'topik' => $this->topik,
            'alokasi_waktu' => $this->alokasi_waktu,
            'jumlah_pertemuan' => $this->jumlah_pertemuan,
            'kurikulum' => $this->kurikulum,
            'tema' => $this->tema,
            'status' => $this->status,
            'content_result' => $this->when($this->status === 'completed', $this->content_result),
            'failure_code' => $this->when($this->status === 'failed', $this->failure_code),
            'failure_message' => $this->when($this->status === 'failed', $this->failure_message),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'failed_at' => $this->failed_at?->toISOString(),
            'owner' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
        ];
    }
}
