<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\ServiceRecord;
use App\Models\Notification;

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
        View::composer('admin.service_record', function ($view) {
            $view->with('serviceRecords', ServiceRecord::all());
        });
        
        // Only query notifications if the table exists
        if (Schema::hasTable('notifications')) {
            View::share('notifications', Notification::latest()->get());
        } else {
            View::share('notifications', collect()); // Empty collection as fallback
        }
    }
}
