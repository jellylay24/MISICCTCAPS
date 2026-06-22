<?php

use Illuminate\Support\Facades\File;

if (!function_exists('vite_asset')) {
    function vite_asset(string $entry): string
    {
        $manifestPath = public_path('build/manifest.json');
        if (!File::exists($manifestPath)) {
            return $entry;
        }

        $manifest = json_decode(File::get($manifestPath), true);
        $path = $manifest[$entry]['file'] ?? $entry;

        return asset('build/' . $path);
    }
}
