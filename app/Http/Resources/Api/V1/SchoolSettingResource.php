<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolSettingResource extends JsonResource
{
    public function toArray(Request $r): array
    {
        return ['id' => $this->id, 'nama_sekolah' => $this->nama_sekolah, 'nsm' => $this->nsm, 'npsn' => $this->npsn, 'alamat' => $this->alamat, 'logo_url' => $this->logo ? asset('storage/'.$this->logo) : null, 'logo_kanan_url' => $this->logo_kanan ? asset('storage/'.$this->logo_kanan) : null, 'kop_surat_url' => $this->kop_surat ? asset('storage/'.$this->kop_surat) : null];
    }
}
