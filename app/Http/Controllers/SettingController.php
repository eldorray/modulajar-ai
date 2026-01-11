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
    public function index()
    {
        $settings = SchoolSetting::getSettings();
        return view('settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_sekolah' => 'nullable|string|max:255',
            'nsm' => 'nullable|string|max:20',
            'npsn' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $settings = SchoolSetting::getSettings();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            
            // Store new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            $settings->logo = $logoPath;
        }

        $settings->nama_sekolah = $validated['nama_sekolah'] ?? null;
        $settings->nsm = $validated['nsm'] ?? null;
        $settings->npsn = $validated['npsn'] ?? null;
        $settings->alamat = $validated['alamat'] ?? null;
        $settings->save();

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan sekolah berhasil disimpan!');
    }

    /**
     * Delete the school logo.
     */
    public function deleteLogo()
    {
        $settings = SchoolSetting::getSettings();

        if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
            Storage::disk('public')->delete($settings->logo);
            $settings->logo = null;
            $settings->save();
        }

        return redirect()->route('settings.index')
            ->with('success', 'Logo berhasil dihapus!');
    }
}
