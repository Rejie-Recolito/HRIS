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
    if ($this->app->runningInConsole()) {
        return;
    }

    View::composer('admin.service_record', function ($view) {
        $view->with('serviceRecords', ServiceRecord::all());
    });

    if (Schema::hasTable('notifications')) {
        View::share('notifications', Notification::latest()->get());
    } else {
        View::share('notifications', collect());
    }

    // Share pending users count for admin approval badge
    if (Schema::hasTable('users')) {
        try {
            $pendingCount = \App\Models\User::where('is_approved', false)->count();
            View::share('pendingUsersCount', $pendingCount);
        } catch (\Throwable $e) {
            View::share('pendingUsersCount', 0);
        }
    } else {
        View::share('pendingUsersCount', 0);
    }
}
}
