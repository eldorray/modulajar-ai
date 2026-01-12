<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Sample data rows with correct role values (admin or guru)
        return [
            ['John Doe', 'john@example.com', 'password123', 'guru'],
            ['Jane Admin', 'jane@example.com', 'securepass', 'admin'],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'name',
            'email',
            'password',
            'role',
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25,  // name
            'B' => 30,  // email
            'C' => 20,  // password
            'D' => 10,  // role
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        // Add comment/note to header row
        $sheet->getComment('A1')->getText()->createTextRun('Nama lengkap user (wajib)');
        $sheet->getComment('B1')->getText()->createTextRun('Email user, harus unik (wajib)');
        $sheet->getComment('C1')->getText()->createTextRun('Password user (opsional, default: password123)');
        $sheet->getComment('D1')->getText()->createTextRun('Role: admin atau user (opsional, default: user)');

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
            ],
        ];
    }
}
