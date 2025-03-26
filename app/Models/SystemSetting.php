<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'system_title',
        'system_short_title',
        'logo',
        'minilogo',
        'favicon',
        'company_name',
        'tag_line',
        'phone_code',
        'phone_number',
        'whatsapp',
        'email',
        'time_zone',
        'language',
        'copyright_text',

        'admin_title',
        'admin_short_title',
        'admin_logo',
        'admin_mini_logo',
        'admin_favicon',
        'admin_copyright_text',
    ];
}
