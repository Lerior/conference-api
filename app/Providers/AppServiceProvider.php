<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Conference;
use App\Models\Topic;
use App\Policies\AttendancePolicy;
use App\Policies\ConferencePolicy;
use App\Policies\TopicPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }
    
    /**
     * Bootstrap any application services.
    */
    public function boot(): void
    {
        Gate::policy(Conference::class, ConferencePolicy::class);
        Gate::policy(Topic::class, TopicPolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
    }
}
