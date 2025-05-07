<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Options;
use App\Models\FeesPlan;
class FinanceController extends Controller
{
    public function feetype(Request $request)
    {
        $feetype = Options::where('type', 'fees')->first();
        $feetype = $feetype->value ?? [];
        if($request->has('create')){   
            $feetype[] = ['feetype' => $request->feetype, 'amount' => $request->amount];
            Options::updateOrCreate(['type' => 'fees'],['value' => $feetype]);
            return redirect()->back()->with('success', 'Fee Type added successfully!');
        }

        if($request->has('update')){   
            $feetype[$request->index] = ['feetype' => $request->feetype, 'amount' => $request->amount];
            Options::updateOrCreate(['type' => 'fees'],['value' => $feetype]);
            return redirect()->back()->with('success', 'Fee Type updated successfully!');
        }

        if($request->has('delete')){   
            unset($feetype[$request->index]);
            Options::updateOrCreate(['type' => 'fees'],['value' => $feetype]);
            return redirect()->back()->with('success', 'Fee Type deleted successfully!');
        }

        return view('finance.feetype', compact('feetype'));
    }
    public function index(Request $request){
        $fees_plan = FeesPlan::where('academic_year', $this->academic_year)->get();
        return view('finance.index', compact('fees_plan'));
    }

    public function create(Request $request){
        $coaching_type = Student::select('coaching_type')->distinct()->get();
        $feetype = Options::where('type', 'fees')->first();
        $feetype = $feetype->value ?? [];
        return view('finance.create', compact('coaching_type', 'feetype'));
    }

    public function store(Request $request){
        $data = $request->except(['coaching_type','item']);
        $data['academic_year'] = $this->academic_year;
        $data['coaching_type'] = implode(',', $request->coaching_type);
        $fees_plan = FeesPlan::create($data);
        $fees_plan->items()->createMany($request->item);
        return redirect()->route('feetype.index')->with('success', 'Fees Plan created successfully!');
    }

    public function destroy(Request $request, FeesPlan $fees_plan){
        $fees_plan->items->delete();
        $fees_plan->delete();
        return redirect()->route('feetype.index')->with('success', 'Fees Plan deleted successfully!');
    }
  
   public function collection(Request $request)
{
    $branches = Branch::pluck('name', 'id');
    $coachingTypes = Student::select('coaching_type')->distinct()->get();
    $students = Student::select('student_id', 'student_name', 'user_name', 'father_name', 'mother_name', 'ph_no1')->get();
    
    $student = null;

    if ($request->filled('student_query') && $request->filled('student_search_type')) {
        $field = $request->input('student_search_type');
        $query = $request->input('student_query');

        $student = Student::where($field, $query)->first();
    }

    return view('finance.collection', compact('branches', 'coachingTypes', 'students', 'student'));
}

    
    
    
    
    
    
}
