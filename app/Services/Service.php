<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class Service
{
    public static function fileUpload($file, $folder)
    {
        try {
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $targetPath = public_path('uploads/' . $folder);

            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0755, true);
            }

            $file->move($targetPath, $fileName);

            return 'uploads/' . $folder . '/' . $fileName;

        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            return null;
        }
    }
}
