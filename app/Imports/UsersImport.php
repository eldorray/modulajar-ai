<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private $rowCount = 0;
    private $skippedCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check if email already exists
        if (User::where('email', $row['email'])->exists()) {
            $this->skippedCount++;
            return null;
        }

        $this->rowCount++;

        // Map role: 'user' -> 'guru', default to 'guru' if empty or invalid
        $role = strtolower(trim($row['role'] ?? 'guru'));
        if ($role === 'user') {
            $role = 'guru';
        }
        if (!in_array($role, ['admin', 'guru'])) {
            $role = 'guru';
        }

        return new User([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password123'),
            'role'     => $role,
        ]);
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role' => 'nullable|string',
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     */
    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'Kolom nama wajib diisi',
            'name.string' => 'Nama harus berupa teks',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Kolom email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
        ];
    }

    /**
     * Get the count of imported rows.
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * Get the count of skipped rows.
     */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
