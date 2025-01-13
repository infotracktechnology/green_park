<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use illuminate\Database\Eloquent\Model;

class LoginController extends Controller
{
    
     public function login(Request $request)
     {
         
         if (Auth::guard('web')->attempt(['username' => $request->username, 'password' => $request->password])) {
            
             return redirect()->route('admin.home')->with('success', 'Welcome back!');
         }
         elseif(Auth::guard('student')->attempt(['user_name' => $request->username, 'password' => $request->password])) {
             // Update the active column to 1
        $student = Auth::guard('student')->user();
        $student->active = 1;
        if ($student instanceof \Illuminate\Database\Eloquent\Model) {
            $student->save();
        }
        
        
            
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
