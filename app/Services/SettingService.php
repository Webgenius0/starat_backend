<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SettingService extends Service
{
    public function adminSettingPage()
    {
        $data['setting'] = SystemSetting::first();

        return view('backend.layout.setting.admin-setting')->with($data);
    }

    public function adminSettingUpdate($title, $logo, $favicon, $tag, $code, $phone, $email, $copyright)
    {
        try {
            DB::beginTransaction();
            $setting = SystemSetting::firstOrNew();
            $setting->system_title = Str::title($title);
            $setting->tag_line = $tag;
            $setting->phone_code = $code;
            $setting->phone_number = $phone;
            $setting->email = $email;

            if ($logo != null) {
                $path = $this->fileUpload($logo, 'systems/logo/');
                $setting->logo = $path;
            }

            if ($favicon != null) {
                $path = $this->fileUpload($favicon, 'systems/favicon/');
                $setting->favicon = $path;
            }

            $setting->copyright_text = $copyright;

            $res = $setting->save();

            DB::commit();
            if ($res) {
                return redirect()->back()->with('message', 'Update information successfully');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
