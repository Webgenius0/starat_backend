<?php

use App\Console\Commands\DayStoreDeleted;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(DayStoreDeleted::class)->everyMinute();

// Schedule::call(function () {
//     logger('chcek');
// })->everySecond();
