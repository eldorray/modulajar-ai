<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Show the settings form.
     */
    public function index(Request $request)
    {
        $unit = SchoolSetting::normalizeJenjang($request->query('unit'));
        $settings = SchoolSetting::getSettings($unit);

        return view('settings.index', compact('settings', 'unit'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'jenjang' => 'nullable|string|in:'.implode(',', SchoolSetting::JENJANG),
            'nama_sekolah' => 'nullable|string|max:255',
            'nsm' => 'nullable|string|max:20',
            'npsn' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'logo_kanan' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'kop_surat' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
        ]);

        $unit = SchoolSetting::normalizeJenjang($request->input('jenjang'));
        $settings = SchoolSetting::getSettings($unit);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $settings->logo = $logoPath;
        }

        // Handle logo kanan upload
        if ($request->hasFile('logo_kanan')) {
            if ($settings->logo_kanan && Storage::disk('public')->exists($settings->logo_kanan)) {
                Storage::disk('public')->delete($settings->logo_kanan);
            }
            $logoKananPath = $request->file('logo_kanan')->store('logos', 'public');
            $settings->logo_kanan = $logoKananPath;
        }

        // Handle kop surat upload
        if ($request->hasFile('kop_surat')) {
            if ($settings->kop_surat && Storage::disk('public')->exists($settings->kop_surat)) {
                Storage::disk('public')->delete($settings->kop_surat);
            }
            $kopSuratPath = $request->file('kop_surat')->store('kop_surat', 'public');
            $settings->kop_surat = $kopSuratPath;
        }

        $settings->nama_sekolah = $validated['nama_sekolah'] ?? null;
        $settings->nsm = $validated['nsm'] ?? null;
        $settings->npsn = $validated['npsn'] ?? null;
        $settings->alamat = $validated['alamat'] ?? null;
        $settings->save();

        return redirect()->route('settings.index', ['unit' => $unit])
            ->with('success', "Pengaturan sekolah {$unit} berhasil disimpan!");
    }

    /**
     * Delete the school logo.
     */
    public function deleteLogo(Request $request)
    {
        $unit = SchoolSetting::normalizeJenjang($request->input('jenjang'));
        $settings = SchoolSetting::getSettings($unit);

        if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
            Storage::disk('public')->delete($settings->logo);
            $settings->logo = null;
            $settings->save();
        }

        return redirect()->route('settings.index', ['unit' => $unit])
            ->with('success', 'Logo berhasil dihapus!');
    }

    /**
     * Delete the right logo (logo kanan).
     */
    public function deleteLogoKanan(Request $request)
    {
        $unit = SchoolSetting::normalizeJenjang($request->input('jenjang'));
        $settings = SchoolSetting::getSettings($unit);

        if ($settings->logo_kanan && Storage::disk('public')->exists($settings->logo_kanan)) {
            Storage::disk('public')->delete($settings->logo_kanan);
            $settings->logo_kanan = null;
            $settings->save();
        }

        return redirect()->route('settings.index', ['unit' => $unit])
            ->with('success', 'Logo kanan berhasil dihapus!');
    }

    /**
     * Delete the letterhead (kop surat).
     */
    public function deleteKopSurat(Request $request)
    {
        $unit = SchoolSetting::normalizeJenjang($request->input('jenjang'));
        $settings = SchoolSetting::getSettings($unit);

        if ($settings->kop_surat && Storage::disk('public')->exists($settings->kop_surat)) {
            Storage::disk('public')->delete($settings->kop_surat);
            $settings->kop_surat = null;
            $settings->save();
        }

        return redirect()->route('settings.index', ['unit' => $unit])
            ->with('success', 'Kop surat berhasil dihapus!');
    }
}
