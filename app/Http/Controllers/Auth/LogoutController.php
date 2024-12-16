<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    /**
     * Handle the logout request and fully destroy the session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Log the user out
        Auth::logout();

        // Destroy all session data
        $request->session()->flush();

        // Regenerate the session ID to prevent session fixation attacks
        $request->session()->regenerate();

        // Optionally, invalidate the "remember me" token if using "remember me"
        if (Auth::viaRemember()) {
            Auth::user()->tokens->each(function ($token) {
                $token->delete();
            });
        }

        // Set headers to prevent the browser from caching the page
        $response = redirect()->route('login');
        
        // Add headers to prevent caching
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
