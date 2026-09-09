<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuruResource extends JsonResource
{
    public function toArray(Request $r): array
    {
        return ['id' => $this->id, 'user_id' => $this->user_id, 'nik' => $this->nik, 'nip' => $this->nip, 'nama' => $this->nama, 'jenis_kelamin' => $this->jenis_kelamin, 'tempat_lahir' => $this->tempat_lahir, 'tanggal_lahir' => $this->tanggal_lahir?->toDateString(), 'alamat' => $this->alamat, 'no_hp' => $this->no_hp, 'email' => $this->email, 'jabatan' => $this->jabatan, 'status' => $this->status, 'user' => $this->whenLoaded('user', fn () => ['id' => $this->user->id, 'name' => $this->user->name, 'email' => $this->user->email, 'role' => $this->user->role])];
    }
}
