<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Student;
use Illuminate\Support\Facades\View;
use App\Models\AcademicYear;
use App\Models\Branch;
use Illuminate\Support\Facades\Route;

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
        if (Route::hasMiddlewareGroup('web')) {
            View::composer('*', function ($view) {
                $academicyear = AcademicYear::where('academic_year', session('academic_year'))->get();
                $user = auth()->user();

                $course = ['NEET', 'JEE', 'XI-OB', 'XII-OB','XII-CBSE'];
                $branches = Branch::when($user && $user->branch, fn($q) => $q->where('id', $user->branch))->get();
                $coachingtype = ['OFFLINE', 'ONLINE', 'ONLINE LIVE', 'ONLINE RECORDED', 'TEST BATCH'];
                $hostel = ['DAYSCHOLAR', 'HOSTEL'];
                $batch = Student::select('batch')->whereNotNull('batch')->where('batch', '!=', '')->distinct()->orderBy('batch')->get()->pluck('batch')->toArray();

                $view->with(compact('academicyear', 'course', 'branches', 'coachingtype', 'hostel', 'batch'));
            });
        }
    }
}
