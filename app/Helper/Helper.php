<?php

namespace App\Helper;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class Helper
{
    public static function s3upload($path,$imageFile)
    {
        $uploadedUrl = Storage::disk('s3')->put($path, $imageFile);
        return $uploadedUrl;
    }

    // Upload Image
    public static function uploadImage($file, $directory, $oldFilePath = null)
    {
        if (!$file) {
            return $oldFilePath;
        }

        if ($oldFilePath && File::exists(public_path($oldFilePath))) {
            File::delete(public_path($oldFilePath));
        }

        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = $directory . '/' . $filename;

        // Move the uploaded file to the specified directory
        $file->move(public_path($directory), $filename);

        return $filePath;
    }

    //tableCheckbox
    public static function tableCheckbox($row_id)
    {
        return '<div class="form-checkbox">
                <input type="checkbox" class="form-check-input select_data" id="checkbox-' . $row_id . '" value="' . $row_id . '" onClick="select_single_item(' . $row_id . ')">
                <label class="form-check-label" for="checkbox-' . $row_id . '"></label>
            </div>';
    }

    //video upload
    public static function videoUpload($file, $folder, $name)
    {
        $videoName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('uploads/' . $folder), $videoName);
        $path = 'uploads/' . $folder . '/' . $videoName;
        return $path;
    }

    // audio upload
    public static function audioUpload($file, $folder, $name)
    {
        $audioName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('uploads/' . $folder), $audioName);
        $path = 'uploads/' . $folder . '/' . $audioName;
        return $path;
    }

    public static function uploadToS3($file, $directory = 'uploads')
    {
        try {
            $filename = $directory . '/' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($directory, $filename, [
                'disk' => 's3',
                'visibility' => 'public',
            ]);
            return Storage::disk('s3')->url($path);
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return null;
        }
    }
}
