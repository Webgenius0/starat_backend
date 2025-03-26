<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'system_title' => ['required', 'min:2'],
           'company_name' => ['required', 'string'],
           'tag_line'     => ['required', 'string'],
           'phone_code'   => ['required'],
           'phone_number' => ['required', 'string'],
           'email'        => ['required', 'email', 'unique:system_settings,email'],
           'time_zone'    => ['required'],
           'language'     => ['required'],
           'registration' => ['required', 'in:on,off'],
           'country'      => ['required'],
           'currency'     => ['required'],
           'logo'         => ['required', 'image','mimes:jpg,jpeg,png,webp', 'max:2048'],
           'favicon'      => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Please upload an system logo.',
            'logo.image'    => 'The file must be an image.',
            'logo.mimes'    => 'The image must be a file of type: png, jpg, webp, jpeg.',
            'logo.max'      => 'The image size must not exceed 2MB.',

            'favicon.required' => 'Please upload an system logo.',
            'favicon.image'    => 'The file must be an image.',
            'favicon.mimes'    => 'The image must be a file of type: png, jpg, webp, jpeg.',
            'favicon.max'      => 'The image size must not exceed 2MB.',

            'system_title.required' => 'The title is required.',
            'system_title.min'      => 'The title must be at least 2 characters.',
        ];
    }
}
