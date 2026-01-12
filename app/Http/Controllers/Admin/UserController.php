<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Imports\UsersImport;
use App\Exports\UsersTemplateExport;
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,guru'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if (!empty($validated['password'])) {
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
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            return back()->with('error', 'Gagal import: ' . implode(' | ', array_slice($errors, 0, 3)));
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors with Indonesian messages
            $errorMessage = $this->translateDatabaseError($e->getMessage());
            return back()->with('error', 'Gagal import: ' . $errorMessage);
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
