<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteSettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::pluck('value', 'key');
        return view('admin.site-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'          => 'required|string|max:100',
            'site_tagline'       => 'nullable|string|max:255',
            'footer_description' => 'nullable|string|max:1000',
            'copyright_text'     => 'nullable|string|max:255',
            'favicon'            => 'nullable|image|mimes:png,jpg,jpeg,ico,webp|max:512',
            'header_logo'        => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'footer_logo'        => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        // Text fields
        foreach (['site_name', 'site_tagline', 'footer_description', 'copyright_text'] as $key) {
            SiteSetting::updateOrCreate(['key' => $key], [
                'value' => $request->input($key, ''),
                'type'  => 'text',
            ]);
        }

        // Image fields
        foreach (['favicon', 'header_logo', 'footer_logo'] as $key) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store("site/{$key}", 'public');
                SiteSetting::updateOrCreate(['key' => $key], ['value' => $path, 'type' => 'image']);
            }
        }

        Cache::forget('site_settings');

        return redirect()->route('admin.site.settings')->with('success', 'Site settings saved.');
    }
}
