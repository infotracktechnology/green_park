<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Options;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Providers\FcmServiceProvider;
//use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $activeUsersCount = Student::where('active', 1)->count();

        if ($request->has('academic_year')) {
            DB::table('academic_year')->update(['active' => 0]);
            AcademicYear::where('academic_year', $request->academic_year)->update(['active' => 1]);
        }

        return view('home', compact('activeUsersCount'));
    }
    public function parent_concern(Request $request)
    {
        $parentconcerns = DB::table('parent_concern')->where('status', '!=', 'Closed')->get();

        if ($request->has('submit')) {
            $updateData = [
                'status' => $request->status,
            ];

            if ($request->status === 'Closed') {
                if ($request->hasFile('file')) {
                    $parentconcern = DB::table('parent_concern')->where('id', $request->id)->first();

                    if ($parentconcern && $parentconcern->file) {
                        $oldFilePath = $parentconcern->file;
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }

                    $file = $request->file('file');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move('uploads/concern', $fileName);
                    $updateData['file'] = 'uploads/concern/'.$fileName;
                }

                $updateData['progress'] = $request->progress;
            }

            DB::table('parent_concern')->where('id', $request->id)->update($updateData);

            return redirect()->route('parent_concern')->with('success', 'Status updated successfully!');
        }

        return view('announcement.parent_concern', compact('parentconcerns'));
    }




    public function chat(Request $request)
    {
        $users = DB::table('chat')->where('sender_id', '!=', auth()->user()->id)->orWhere('receiver_id', '=', auth()->user()->id)->groupBy('sender_id')->selectRaw("sender_id,receiver_id,count(chat_read=0 and receiver_id=" . auth()->user()->id . ") as unread")->get()->map(function ($user) {
            return [
                'id' => $user->sender_id,
                'name' => Student::where('student_id', $user->sender_id)->first()->student_name ?? '',
                'unread' => $user->unread,
            ];
        });

        if ($request->has('submit')) {
            $parentconcern = DB::table('parent_concern')->where('id', $request->id)->update(['status' => $request->status]);
            return redirect()->route('parent_concern')->with('success', 'Status updated successfully!');
        }

        return view('chat', compact('users'));
    }
    public function studentmenu_branch(Request $request)
    {
        $menus = Options::where('type','student menu')->first();
        $menus = $menus->value ?? [];
        $menu_branch =[];
        if($request->has('branch')) {
            $menu_branch = Options::where('type',$request->branch_name.' menu')->first();
            $menu_branch = collect($menu_branch->value ?? [])->map(function ($menu) {
                return $menu['title'];
            })->toArray();
        }
        if($request->has('assign')) {
            $menu = collect($request->fields)->map(function ($menu) {
               return json_decode($menu, true);
            })->toArray();
            Options::updateOrCreate(['type' => $request->branch_name." menu"],['value' => $menu]);
            Student::where('campus', $request->branch)->update(['menu' => $menu]);
            return redirect()->route('studentmenu.branch')->with('success', 'Menu updated successfully!');
        }
       
        return view('studentmenu.branch', compact('menus','menu_branch'));
    }
   
    public function studentmenu_type(Request $request)
    {
        $menus = Options::where('type','student menu')->first();
        $menus = $menus->value ?? [];
        $types = [];
        $menu_type =[];
        if($request->has('branch')) {
            $types =  Student::where('campus', $request->branch)->select('coaching_type')->distinct()->get();
        }
        if($request->has('type')) {
            $menu_type = Options::where('type',$request->branch_name.$request->type.' menu')->first();
            $menu_type = collect($menu_type->value ?? [])->map(function ($menu) {
                return $menu['title'];
            })->toArray();
        }

        if($request->has('assign')) {
            $menu = collect($request->fields)->map(function ($menu) {
               return json_decode($menu, true);
            })->toArray();
            Options::updateOrCreate(['type' => $request->branch_name.$request->type." menu"],['value' => $menu]);
            Student::where('campus', $request->branch)->where('coaching_type', $request->type)->update(['menu' => $menu]);
            return redirect()->route('studentmenu.type')->with('success', 'Menu updated successfully!');
        }
       
        return view('studentmenu.type', compact('menus','menu_type','types'));
    }
    public function studentmenu_student(Request $request)
    {
        $menus = Options::where('type','student menu')->first();
        $menus = $menus->value ?? [];
        $types = [];
        $menu_student =[];
        $students = [];
        if($request->has('branch')) {
            $types =  Student::where('campus', $request->branch)->select('coaching_type')->distinct()->get();
        }

        if($request->has('type')) {
            $students =  Student::where('campus', $request->branch)->where('coaching_type', $request->type)->get();
        }
        
        if($request->has('student')) {
            $menu_student = Student::where('student_id', $request->student)->first();
            $menu_student = collect($menu_student->menu ?? [])->map(function ($menu) {
                return $menu['title'];
            })->toArray();
        }

        if($request->has('assign')) {
            $menu = collect($request->fields)->map(function ($menu) {
               return json_decode($menu, true);
            })->toArray();
            Student::where('student_id', $request->student)->update(['menu' => $menu]);
            return redirect()->route('studentmenu.student')->with('success', 'Menu updated successfully!');
        }
       
        return view('studentmenu.student', compact('menus','menu_student','types','students'));
    }

    public function notify(FcmServiceProvider  $fcm)
    {
        $nofify = $fcm->sendToDevice("danSFIxYRH6bLPK9hcMOin:APA91bG3NHjtoOTWT9zG35jJFRa86BUNQzzDqZXIDRGBUIUVvgHplPvCeyNNy5Y-2jVOHLo-nikFqS2wkoWPN9aWfcKznRnvkT707YyumFerhNoGlZjv1Mw", "GPCC Notification", "Testing", null, [], "https://www.google.com");
       dd($nofify);
    }


}
