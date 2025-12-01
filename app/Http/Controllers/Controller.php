<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\AcademicYear;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $academic_year = [];

    public function __construct(){
        $this->academic_year = AcademicYear::where('active', 1)->first()->academic_year;
    }
    
    private function financial_year(){
    $financial_year_start = (int)date('m') < 4 ? (int)date('Y') - 1 : (int)date('Y');
    $financial_year_end = (int)date('m') < 4 ? (int)date('Y') : (int)date('Y') + 1;
    return $financial_year_start . '-' . $financial_year_end;
    }
}
