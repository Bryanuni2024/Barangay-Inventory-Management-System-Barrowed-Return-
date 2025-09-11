<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        
        // Set cache control headers to prevent caching
        $response = response()->view('auth.login');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        
        return $response;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Try to find user by email or name
        $user = User::where('email', $credentials['username'])
                   ->orWhere('name', $credentials['username'])
                   ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Start a fresh session
            $request->session()->start();
            
            // Set session variables for authentication
            $request->session()->put('user_logged_in', true);
            $request->session()->put('user_id', $user->id);
            $request->session()->put('username', $user->name);
            $request->session()->put('user_email', $user->email);
            
            // Force session to save
            $request->session()->save();
            
            // Test if session is working
            if ($request->session()->has('user_logged_in')) {
                return redirect()->route('inventory.dashboard')->with('success', 'Login successful!');
            } else {
                return redirect()->route('login')->with('error', 'Session error. Please try again.');
            }
        }

        return redirect()->route('login')->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        // Clear all session data
        session()->flush();
        
        // Set cache control headers to prevent caching
        $response = redirect()->route('login')->with('success', 'Logged out successfully!');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        
        return $response;
    }
}
