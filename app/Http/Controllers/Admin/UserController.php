<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,guru'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,guru'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password satu user dan simpan password sementaranya.
     */
    public function resetPassword(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Gunakan halaman profil untuk mengganti password sendiri.');
        }

        $validated = $request->validate([
            'password' => ['nullable', 'string', 'min:8', 'max:64'],
        ]);

        $password = $validated['password'] ?? self::generatePassword();

        self::simpanPassword($user, $password);

        return back()->with('success', "Password {$user->name} berhasil direset. Password baru tampil di kolom Password.");
    }

    /**
     * Reset password beberapa user sekaligus.
     */
    public function resetPasswordBatch(Request $request)
    {
        $validated = $request->validate([
            'users' => ['required', 'array', 'min:1'],
            'users.*' => ['integer', 'exists:users,id'],
            'password' => ['nullable', 'string', 'min:8', 'max:64'],
        ], [
            'users.required' => 'Pilih minimal satu user.',
        ]);

        $users = User::whereIn('id', $validated['users'])
            ->where('id', '!=', auth()->id())
            ->get();

        if ($users->isEmpty()) {
            return back()->with('error', 'Tidak ada user yang bisa direset. Akun sendiri dilewati.');
        }

        foreach ($users as $user) {
            // Password seragam bila admin mengisinya, acak bila dikosongkan
            $password = $validated['password'] ?? self::generatePassword();

            self::simpanPassword($user, $password);
        }

        $catatan = count($validated['users']) > $users->count() ? ' Akun sendiri dilewati.' : '';

        return back()->with('success', "Password {$users->count()} user berhasil direset.{$catatan}");
    }

    /**
     * Hapus password sementara dari database (password login tetap berlaku).
     */
    public function clearTempPassword(User $user)
    {
        $user->forceFill(['temp_password' => null, 'temp_password_at' => null])->save();

        return back()->with('success', "Password sementara {$user->name} dihapus dari daftar.");
    }

    /**
     * Simpan password baru + salinan sementaranya.
     * forceFill dipakai karena temp_password sengaja di luar $fillable
     * supaya tidak bisa diisi lewat request biasa.
     */
    private static function simpanPassword(User $user, string $password): void
    {
        $user->forceFill([
            'password' => Hash::make($password),
            'temp_password' => $password,
            'temp_password_at' => now(),
        ])->save();
    }

    /**
     * Password acak tanpa karakter yang mudah tertukar (0/O, 1/l/I).
     */
    private static function generatePassword(int $length = 10): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $password;
    }

    /**
     * Download template Excel for user import.
     */
    public function downloadTemplate()
    {
        return Excel::download(new UsersTemplateExport, 'template_import_users.xlsx');
    }

    /**
     * Import users from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus Excel (.xlsx, .xls) atau CSV.',
            'file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $import = new UsersImport;
            Excel::import($import, $request->file('file'));

            $imported = $import->getRowCount();
            $skipped = $import->getSkippedCount();

            $message = "Berhasil mengimpor {$imported} user.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati (email sudah ada).";
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', $message);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: ".implode(', ', $failure->errors());
            }

            return back()->with('error', 'Gagal import: '.implode(' | ', array_slice($errors, 0, 3)));
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors with Indonesian messages
            $errorMessage = $this->translateDatabaseError($e->getMessage());

            return back()->with('error', 'Gagal import: '.$errorMessage);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: Terjadi kesalahan. Pastikan format file sesuai template.');
        }
    }

    /**
     * Translate database error messages to Indonesian.
     */
    private function translateDatabaseError(string $message): string
    {
        if (str_contains($message, 'Duplicate entry') || str_contains($message, 'UNIQUE constraint')) {
            return 'Email sudah terdaftar di sistem.';
        }
        if (str_contains($message, 'CHECK constraint failed: role')) {
            return 'Role tidak valid. Gunakan "admin" atau "guru".';
        }
        if (str_contains($message, 'cannot be null')) {
            return 'Data tidak lengkap. Pastikan semua kolom wajib terisi.';
        }

        return 'Terjadi kesalahan database. Periksa format data Anda.';
    }
}
