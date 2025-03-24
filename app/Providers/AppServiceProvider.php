<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Student;
use Illuminate\Support\Facades\View;
use App\Models\AcademicYear;
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
        $academicyear = AcademicYear::where('enable', 1)->get();
        View::share('academicyear', $academicyear);
    }
}
