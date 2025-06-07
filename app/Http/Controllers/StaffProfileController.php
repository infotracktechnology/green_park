<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Branch;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ImportController;

class StaffProfileController extends Controller
{

    public function index(Request $request)
{
    $branches = Branch::all();

    $staff = Staff::with('branch')
    ->when(auth()->user()->branch, function ($query) {
        $query->where('branch_id', 'like', '%' . auth()->user()->branch . '%');
    })
    ->get();

    return view('staff.index', compact('staff', 'branches'));
}

    public function create(Request $request)
    {

        // $branches = DB::table('branch')->select('id', 'name')->get();
        $states = DB::table('district_list')->select('State')->distinct()->orderBy('State')->get();
        if ($request->has('state')) {
            $districts = DB::table('district_list')->where('State', $request->state)->select('District')->distinct()->orderBy('District')->get();
            return response()->json($districts);
        }
        return view('staff.create', compact('states'));
    }
    public function store(Request $request)
    {


        // Handle the file upload if a photo is provided
        $photoPath = null;
        $path = '';
        $children_details = [];
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->move('uploads/staff', $request->file('photo')->getClientOriginalName());
            $path = 'uploads/staff/' . $request->file('photo')->getClientOriginalName();
        }

        // Save the staff details along with children details
        $staff = new Staff($request->except('children'));
        $staff->photo = $path;
        $childrenStudying = $request->has('children_studying') && $request->children_studying == 1 ? 1 : 0;
        // Convert children to JSON before saving
        if ($request->has('children')) {

            foreach ($request->children as $child) {
                $children_details[] = ['name' => $child['name'], 'class' => $child['class'], 'section' => $child['section']];
            }
        }
        $staff->children_studying = $childrenStudying; // Store 1 if Yes, 0 if No
        $staff->children_details =  $children_details;
        $staff->username = $request->biometric_no;
        $staff->password = bcrypt($request->mob_no);
        $staff->mob_no = $request->mob_no;

        $staff->save();

        return redirect()->route('staff.index')->with('success', 'Staff details saved successfully!');
    }




    public function edit(Request $request, Staff $staff)
    {

      
        $districts = DB::table('district_list')->where('State', $staff->state)->select('District')->distinct()->orderBy('District')->get();
        $states = DB::table('district_list')->select('State')->distinct()->orderBy('State')->get();

        return view('staff.edit', compact('staff', 'districts', 'states'));
    }

    public function update(Request $request, Staff $staff)
    {

        $data = $request->except(['photo', 'children']);

        $path = '';
        $children_details = [];
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->move('uploads/staff', $request->file('photo')->getClientOriginalName());
            $path = 'uploads/staff/' . $request->file('photo')->getClientOriginalName();
        }

        $data['photo'] = $path;
        if ($request->has('children')) {

            foreach ($request->children as $child) {
                $children_details[] = ['name' => $child['name'], 'class' => $child['class'], 'section' => $child['section']];
            }
        }
        $data['children_studying'] = $request->has('children_studying') && $request->children_studying == 1 ? 1 : 0;
        $data['children_details'] =  $children_details;
        $data['password'] = bcrypt($request->password1);

        $staff->update($data);

        return redirect()->route('staff.index')->with('success', 'Staff updated successfully');
    }

    public function destroy(Request $request, Staff $staff)
    {

        if ($staff->photo) {
            Storage::delete($staff->photo);
        }
        if ($staff->id_proof) {
            Storage::delete($staff->id_proof);
        }
        if ($staff->experience_certificates) {
            $certificates = json_decode($staff->experience_certificates, true);
            foreach ($certificates as $certificate) {
                Storage::delete($certificate);
            }
        }
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff deleted successfully');
    }

    public function import(Request $request, ImportController $import)
    {
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();
            $data = $import->parseCSV($filePath);
            foreach ($data as $row) {
                $row = array_map('ucwords', $row);
                $id = $row['id'] ?? 0;
                $staff = Staff::find($id);
                if ($staff) {
                    $staff->update($row);
                } else {
                    Staff::create($row);
                }
            }
        }
        return redirect()->route('staff.index')->with('success', 'Staff imported successfully');
    }


    public function export(Request $request)
    {
        $staffs = Staff::selectRaw("id,name,school_initial,staff_type,hostel_dayscholar,gender,dob,age,marital_status,blood_group,department,qualifications,nationality,religion,community,caste,mob_no,alternate_mob_no,aadhaar_no,email,address_line_1,address_line_2,state,city,pincode,photo,biometric_no,father_name,mother_name,spouse_name,spouse_ph_no,spouse_occupation,father_ph_no,date_of_joining,designation,experience,class_handling_type,paper_correction,handeling_class,previous_school")->get()->toArray();
        $file = fopen('staff_export.csv', 'w');
        $headers = array_keys($staffs[0]);
        fputcsv($file, $headers);
        foreach ($staffs as $staff) {
            fputcsv($file, $staff);
        }
        fclose($file);
        return response()->download('staff_export.csv', 'staff_export.csv', ['Content-Type: text/csv', 'Cache-Control' => 'no-cache, must-revalidate', 'Expires' => '0']);
    }


    public function classAssign(Request $request)
    {
        if ($request->isMethod('post')) {
            $staffId = $request->input('name');
            $staff = Staff::find($staffId);

            if ($staff) {
                $staff->class_assign = [
                    'branch_id' => $request->input('branch'),
                    'coaching_types' => $request->input('coaching_type'),
                    'sections' => $request->input('section'),
                ];


                $staff->save();

                return redirect()->route('staff.class')->with('success', 'Class assigned successfully!');
            }

            return redirect()->back()->with('error', 'Staff not found!');
        }

        $branches = Branch::all();
        $staffDetails = Staff::all();
        $departments = Staff::distinct()->pluck('department');
        $sections = Student::distinct()
            ->whereNotNull('section')
            ->whereNotNull('campus')
            ->whereNotNull('coaching_type')
            ->where('section', '!=', '')
            ->where('campus', '!=', '')
            ->where('coaching_type', '!=', '')
            ->get(['section', 'campus', 'coaching_type']);

        return view('staff.class', compact('branches', 'departments', 'staffDetails', 'sections'));
    }


    public function subjectAssign(Request $request)
    {


        $staffId = $request->input('name');
        $staff = Staff::find($staffId);

        if ($staff) {
            $staff->sub_assign = [
                'branch_id' => $request->input('branch'),
                'coaching_types' => $request->input('coaching_type'),
                'sections' => $request->input('section'), // checkbox array
                'subject' => $request->input('subject'),
            ];

            $staff->save();

            return redirect()->route('staff.class')->with('success', 'Subject assigned successfully!');
        }

        return redirect()->back()->with('error', 'Staff not found!');
    }
}
