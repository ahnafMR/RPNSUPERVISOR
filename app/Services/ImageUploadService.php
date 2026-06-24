<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    public function storeMultiple(array $files, string $directory): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->storeSingle($file, $directory);
            }
        }

        return $paths;
    }

    public function storeSingle(UploadedFile $file, string $directory): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($directory, $filename, 'public');
    }

    public function storeBase64(string $base64, string $directory): string
    {
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $decoded = base64_decode($image);
        $filename = Str::uuid() . '.jpg';
        $path = $directory . '/' . $filename;

        Storage::disk('public')->put($path, $decoded);

        return $path;
    }

    public function delete(string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
