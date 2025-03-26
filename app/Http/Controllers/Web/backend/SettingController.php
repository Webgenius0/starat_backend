<?php

namespace App\Http\Controllers\Web\backend;

use Illuminate\Http\Request;
use App\Services\SettingService;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Services\Service;

class SettingController extends Service
{
    public $settingServiceObj;

    public  function  __construct()
    {
        $this->settingServiceObj = new SettingService();
    }

    public function adminSetting()
    {
        return $this->settingServiceObj->adminSettingPage();
    }

    public function adminSettingUpdate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'admin_title' => 'required|string|max:150',
            'admin_short_title' => 'nullable|string|max:100',
            'admin_copyright_text' => 'nullable|string|max:500',
        ], [
            'admin_title.required' => 'The admin title is required.',
            'admin_title.max' => 'The admin title must not exceed 150 characters.',
            'admin_short_title.max' => 'The admin short title must not exceed 100 characters.',
            'admin_copyright_text.max' => 'The copyright text must not exceed 500 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        try {
            $setting = SystemSetting::firstOrNew();

            $data = $request->all();
            $data['admin_title'] = Str::title($request->admin_title);

            if ($request->admin_logo != null) {
                if (file_exists($setting->admin_logo) && $setting->admin_logo != 'uploads/systems/logo/logo.png') {
                    unlink($setting->admin_logo);
                }
                $path = $this->fileUpload($request->admin_logo, 'systems/logo/');
                $data['admin_logo'] = $path;
            }

            if ($request->admin_favicon != null) {
                if (file_exists($setting->admin_favicon) && $setting->admin_favicon != 'uploads/systems/favicon/favico.png') {
                    unlink($setting->admin_favicon);
                }
                $path = $this->fileUpload($request->admin_favicon, 'systems/favicon/');
                $data['admin_favicon'] = $path;
            }

            $setting->update($data);

            return redirect()->back()->with('success', 'Update information successfully');

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function systemSetting()
    {
        $data['setting'] = SystemSetting::first();

        return view('backend.layout.setting.system-setting')->with($data);
    }

    public function systemSettingUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'system_title' => 'required|string|max:150',
            'system_short_title' => 'nullable|string|max:100',
            'tag_line' => 'nullable|string|max:255',
            'company_name' => 'required|string|max:150',
            'phone_code' => 'required|string|max:5',
            'phone_number' => 'required|string|max:15|regex:/^\d+$/',
            'email' => 'required|email|max:150',
            'copyright' => 'nullable|string|max:500',
        ], [
            'system_title.required' => 'The system title is required.',
            'system_title.max' => 'The system title must not exceed 150 characters.',
            'company_name.required' => 'The company name is required.',
            'phone_code.required' => 'The phone code is required.',
            'phone_number.required' => 'The phone number is required.',
            'phone_number.regex' => 'The phone number must contain only digits.',
            'email.required' => 'The email is required.',
            'email.email' => 'Enter a valid email address.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        try {
            $setting = SystemSetting::firstOrNew();

            $data = $request->all();
            $data['system_title'] = Str::title($request->system_title);
            if ($request->logo != null) {
                if (file_exists($setting->logo) && $setting->logo != 'uploads/systems/logo/logo.png') {
                    unlink($setting->logo);
                }
                $path = $this->fileUpload($request->logo, 'systems/logo/');
                $data['logo'] = $path;
            }

            if ($request->favicon != null) {
                if (file_exists($setting->favicon) && $setting->favicon != 'uploads/systems/favicon/favico.png') {
                    unlink($setting->favicon);
                }
                $path = $this->fileUpload($request->favicon, 'systems/favicon/');
                $data['favicon'] = $path;
            }

            $setting->update($data);

            return redirect()->back()->with('success', 'Update information successfully');

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mail()
    {
        return view('backend.layout.setting.mail');
    }

    public function mailstore(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|string',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|string',
        ]);
        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak = "\n";
            $envContent = preg_replace([
                '/MAIL_MAILER=(.*)\s/',
                '/MAIL_HOST=(.*)\s/',
                '/MAIL_PORT=(.*)\s/',
                '/MAIL_USERNAME=(.*)\s/',
                '/MAIL_PASSWORD=(.*)\s/',
                '/MAIL_ENCRYPTION=(.*)\s/',
                '/MAIL_FROM_ADDRESS=(.*)\s/',
            ], [
                'MAIL_MAILER=' . $request->mail_mailer . $lineBreak,
                'MAIL_HOST=' . $request->mail_host . $lineBreak,
                'MAIL_PORT=' . $request->mail_port . $lineBreak,
                'MAIL_USERNAME=' . $request->mail_username . $lineBreak,
                'MAIL_PASSWORD=' . $request->mail_password . $lineBreak,
                'MAIL_ENCRYPTION=' . $request->mail_encryption . $lineBreak,
                'MAIL_FROM_ADDRESS=' . '"' . $request->mail_from_address . '"' . $lineBreak,
            ], $envContent);

            if ($envContent !== null) {
                File::put(base_path('.env'), $envContent);
            }
            return back()->with('success', 'Updated successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update');
        }

        return redirect()->back();
    }
}
