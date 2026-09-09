<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SchoolSettingResource;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends Controller
{
    public function show()
    {
        return api_response()->success(SchoolSettingResource::make(SchoolSetting::getSettings()));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nama_sekolah' => ['nullable', 'string', 'max:255'],
            'nsm' => ['nullable', 'string', 'max:20'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'logo_kanan' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'kop_surat' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:4096'],
        ]);
        $settings = SchoolSetting::getSettings();
        foreach (['logo' => 'logos', 'logo_kanan' => 'logos', 'kop_surat' => 'kop_surat'] as $field => $directory) {
            if ($request->hasFile($field)) {
                $old = $settings->{$field};
                $data[$field] = $request->file($field)->store($directory, 'public');
                if ($old) {
                    Storage::disk('public')->delete($old);
                }
            }
        }
        $settings->update($data);

        return api_response()->success(SchoolSettingResource::make($settings->fresh()));
    }

    public function deleteAsset(string $field): Response
    {
        abort_unless(in_array($field, ['logo', 'logo_kanan', 'kop_surat'], true), 404);
        $settings = SchoolSetting::getSettings();
        if ($settings->{$field}) {
            Storage::disk('public')->delete($settings->{$field});
        }
        $settings->forceFill([$field => null])->save();

        return response()->noContent();
    }
}
