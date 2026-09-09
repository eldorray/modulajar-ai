<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\GuruResource;
use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::with('user');
        $query->when($request->filled('search'), fn ($q) => $q->where(fn ($nested) => $nested
            ->where('nama', 'like', '%'.$request->string('search').'%')
            ->orWhere('nik', 'like', '%'.$request->string('search').'%')
            ->orWhere('nip', 'like', '%'.$request->string('search').'%')));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));
        $p = $query->latest()->paginate(15);

        return api_response()->paginated($p, GuruResource::collection($p));
    }

    public function show(Guru $guru)
    {
        return api_response()->success(GuruResource::make($guru->load('user')));
    }

    public function update(Request $r, Guru $guru)
    {
        $d = $r->validate(['nama' => ['required', 'string', 'max:255'], 'nip' => ['nullable', 'string', 'max:50'], 'jenis_kelamin' => ['nullable', 'in:L,P'], 'tempat_lahir' => ['nullable', 'string', 'max:100'], 'tanggal_lahir' => ['nullable', 'date'], 'alamat' => ['nullable', 'string'], 'no_hp' => ['nullable', 'string', 'max:20'], 'email' => ['nullable', 'email', 'max:255'], 'jabatan' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'string', 'max:50']]);
        $guru->update($d);

        return api_response()->success(GuruResource::make($guru->fresh()->load('user')));
    }
}
