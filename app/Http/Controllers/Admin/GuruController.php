<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuruController extends Controller
{
    /**
     * Display a listing of gurus.
     */
    public function index()
    {
        $gurus = Guru::with('user')->latest()->paginate(15);
        
        return view('admin.guru.index', compact('gurus'));
    }

    /**
     * Display the specified guru.
     */
    public function show(Guru $guru)
    {
        $guru->load('user');
        
        return view('admin.guru.show', compact('guru'));
    }

    /**
     * Show the form for editing the specified guru.
     */
    public function edit(Guru $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    /**
     * Update the specified guru.
     */
    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $guru->update($validated);

        // Also update user name if linked
        if ($guru->user) {
            $guru->user->update(['name' => $validated['nama']]);
        }

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Sync gurus from external API.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'source' => ['required', 'in:guru-mi,guru-smp'],
        ], [
            'source.required' => 'Pilih lembaga terlebih dahulu.',
            'source.in' => 'Lembaga tidak valid. Pilih MI atau SMP.',
        ]);

        try {
            $source = $request->input('source');
            $result = $this->syncFromApi($source);
            
            $lembaga = $source === 'guru-mi' ? 'MI Daarul Hikmah' : 'SMP Garuda';
            
            return redirect()
                ->route('admin.guru.index')
                ->with('success', "Berhasil sync {$result['synced']} data guru dari {$lembaga}. {$result['users_created']} akun user baru dibuat.");
        } catch (\Exception $e) {
            Log::error('Guru sync failed: ' . $e->getMessage());
            
            return redirect()
                ->route('admin.guru.index')
                ->with('error', 'Gagal sync data guru: ' . $e->getMessage());
        }
    }

    /**
     * Perform the actual sync from external API.
     */
    protected function syncFromApi(string $source): array
    {
        $apiBaseUrl = env('SYNC_API_BASE_URL', 'https://datainduk.ypdhalmadani.sch.id');
        $baseUrl = "{$apiBaseUrl}/api/{$source}/all";

        $synced = 0;
        $usersCreated = 0;

        // Make API request
        $response = Http::timeout(60)->get($baseUrl);

        if (!$response->successful()) {
            throw new \Exception('API request failed with status: ' . $response->status());
        }

        $data = $response->json();
        
        // Handle API response - data is in 'data' key
        $guruData = $data['data'] ?? [];

        if (!is_array($guruData)) {
            throw new \Exception('Invalid API response format');
        }

        DB::beginTransaction();

        try {
            foreach ($guruData as $item) {
                // Get NIK - primary identifier
                $nik = $item['nik'] ?? null;
                if (empty($nik)) {
                    continue;
                }

                // Find or create guru by NIK
                $guru = Guru::updateOrCreate(
                    ['nik' => $nik],
                    [
                        'nip' => $item['nip'] ?? null,
                        'nama' => $item['full_name'] ?? $item['nama'] ?? 'Unknown',
                        'jenis_kelamin' => $this->normalizeGender($item['gender'] ?? null),
                        'tempat_lahir' => $item['pob'] ?? null,
                        'tanggal_lahir' => $this->parseDate($item['dob'] ?? null),
                        'alamat' => $item['address'] ?? null,
                        'no_hp' => $item['phone_number'] ?? null,
                        'email' => $item['email'] ?? null,
                        'jabatan' => $item['jabatan'] ?? null,
                        'status' => $item['status_pegawai'] ?? null,
                        'foto' => $item['foto'] ?? $item['photo'] ?? null,
                    ]
                );

                $synced++;

                // Create user account if not exists
                if (!$guru->user_id) {
                    $user = $this->createUserForGuru($guru, $nik);
                    if ($user) {
                        $guru->update(['user_id' => $user->id]);
                        $usersCreated++;
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'synced' => $synced,
            'users_created' => $usersCreated,
        ];
    }


    /**
     * Create a user account for the guru.
     * Password default = NIP
     */
    protected function createUserForGuru(Guru $guru, string $nip): ?User
    {
        // Use NIP as email identifier
        $email = $nip . '@guru.local';
        
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return $existingUser;
        }

        // Password default = NIP
        return User::create([
            'name' => $guru->nama,
            'email' => $email,
            'password' => Hash::make($nip),
            'role' => 'guru',
        ]);
    }

    /**
     * Normalize gender value to L or P.
     */
    protected function normalizeGender(?string $gender): ?string
    {
        if (empty($gender)) {
            return null;
        }

        $gender = strtoupper(trim($gender));
        
        if (in_array($gender, ['L', 'LAKI-LAKI', 'LAKI LAKI', 'MALE', 'M', '1'])) {
            return 'L';
        }
        
        if (in_array($gender, ['P', 'PEREMPUAN', 'FEMALE', 'F', '0', '2'])) {
            return 'P';
        }

        return null;
    }

    /**
     * Parse date from various formats.
     */
    protected function parseDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
