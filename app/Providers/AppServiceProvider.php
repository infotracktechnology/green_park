<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Student;
use Illuminate\Support\Facades\View;
use App\Models\AcademicYear;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $academicyear = AcademicYear::where('active', 1)->get();
        $user = Auth::user();
        $branchs = Branch::when($user && $user->branch, function ($query) {
            $query->where('id', $user->branch);
        })->get();

        View::share('academicyear', $academicyear);
        View::share('branches', $branchs);
    }
}
