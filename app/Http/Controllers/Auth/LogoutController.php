<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use illuminate\Database\Eloquent\Model;
use App\Providers\UserLogServiceProvider;

class LogoutController extends Controller
{
    
    public function logout(Request $request)
    {
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            UserLogServiceProvider::storelog($student->student_id,'Student','logout successful', 'Web' );
            // if ($student instanceof \Illuminate\Database\Eloquent\Model) {
            //     $student->save();
            // }
            Auth::guard('student')->logout();
        } elseif (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            UserLogServiceProvider::storelog($user->id,$user->type,'logout successful', 'Web' );
            Auth::guard('web')->logout();
        }

        $request->session()->flush();
        $request->session()->regenerate();
        
        $response = redirect()->route('login');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
