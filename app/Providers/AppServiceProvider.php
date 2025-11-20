<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\ServiceRecord;
use App\Models\ServiceRecordRequest;
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
    // If running in the console, skip heavy boot tasks — but allow when running unit tests
    // so view shares (like notifications) are available to test-rendered views.
    if ($this->app->runningInConsole() && ! $this->app->runningUnitTests()) {
        return;
    }

    View::composer('admin.service_record', function ($view) {
        // Keep serviceRecords for compatibility and also provide requests for the new request-board view
        $view->with('serviceRecords', ServiceRecord::all());
        // Provide ServiceRecordRequest collection; if table doesn't exist, provide empty collection
        if (Schema::hasTable('service_record_requests')) {
            // Provide pending and in-progress requests so admins see requests that were accepted but still being edited
            $view->with('requests', ServiceRecordRequest::whereIn('request_status', ['pending', 'in_progress'])->latest()->get());
        } else {
            $view->with('requests', collect());
        }
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
