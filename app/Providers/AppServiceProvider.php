<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('admin', function () {
            return Auth::check() && Auth::user()->isAdmin();
        });

        Gate::define('subdivisionStaff', function () {
            $user = Auth::user();
            return $user && ($user->isAdmin() || $user->isSubdivisionStaff());
        });

        Gate::define('examiner', function () {
            $user = Auth::user();
            return $user && ($user->isAdmin() || $user->isSubdivisionStaff() || $user->isExaminer());
        });

        Gate::define('mentor', function () {
            $user = Auth::user();
            return $user && ($user->isAdmin() || $user->isSubdivisionStaff() || $user->isExaminer() || $user->isMentor());
        });

        Gate::define('student', function () {
            $user = Auth::user();
            return $user && $user->isStudent();
        });
    }
}
