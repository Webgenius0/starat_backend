<?php

namespace App\Providers;

use App\Models\DynamicPage;
use App\Models\SystemSetting;
use App\Services\TwilioService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            // Greetings
            $currentHour = \Carbon\Carbon::now()->hour;
            if ($currentHour < 12) {
                $greetings = [
                    'type' => 'morning',
                    'message' => "Good Morning!"
                ];
            } elseif ($currentHour < 18) {
                $greetings = [
                    'type' => 'afternoon',
                    'message' => "Good Afternoon!"
                ];
            } else {
                $greetings = [
                    'type' => 'evening',
                    'message' => "Good Evening!"
                ];
            }
            $setting = SystemSetting::first();
            $dynamicpage = DynamicPage::where('status', 1)->get();

            $view->with('setting', $setting);
            $view->with('dynamicpages', $dynamicpage);
            $view->with('greetings', $greetings);
        });
    }
}
