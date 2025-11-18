<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display settings management page.
     */
    public function index()
    {
        // Get all settings grouped by category
        $aboutSettings = Setting::whereIn('key', [
            'about_visi',
            'about_misi',
            'about_tugas_pokok',
            'about_fungsi',
            'about_sejarah'
        ])->get()->keyBy('key');

        $contactSettings = Setting::whereIn('key', [
            'contact_address',
            'contact_phone',
            'contact_email',
            'contact_hours',
            'contact_map_url'
        ])->get()->keyBy('key');

        return view('admin.settings.index', compact('aboutSettings', 'contactSettings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'about_visi' => 'nullable|string',
            'about_misi' => 'nullable|string',
            'about_tugas_pokok' => 'nullable|string',
            'about_fungsi' => 'nullable|string',
            'about_sejarah' => 'nullable|string',
            'contact_address' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_hours' => 'nullable|string',
            'contact_map_url' => 'nullable|url',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'textarea', ucwords(str_replace('_', ' ', $key)));
        }

        return redirect()->route('dinas.settings.index')
            ->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
