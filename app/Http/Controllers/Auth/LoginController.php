<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use APP\Models\Student;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle student login requests.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */


     public function studentLogin(Request $request)
     {
         
         if (Auth::guard('student')->attempt(['user_name' => $request->username, 'password' => $request->password])) {
            
             return redirect()->route('studentdashboard')->with('success', 'Welcome back!');
         }
     
      
         return redirect()->back()->with('error', 'Invalid username or password.');
     }
     
    
}
