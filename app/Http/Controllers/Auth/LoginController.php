<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use illuminate\Database\Eloquent\Model;

class LoginController extends Controller
{
    
     public function login(Request $request)
     {
         
         if (Auth::guard('web')->attempt(['username' => $request->username, 'password' => $request->password])) {
             return redirect()->route('admin.home')->with('success', 'Welcome back!');
         }
        elseif($student = Student::where('user_name', $request->username)->where('password', $request->password)->first()) {
            Auth::guard('student')->login($student);
            $student->update(['active' => 1]);   
            return redirect()->route('studentdashboard')->with('success', 'Welcome back!');
        }
    
         return redirect()->back()->with('error', 'Invalid username or password.');
     }

     function showLoginForm(){
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.home');
        }
        elseif(Auth::guard('student')->check()) {
            return redirect()->route('studentdashboard');
       }
   
        return view('auth.login');
     }
     
    
}
