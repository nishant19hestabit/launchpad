<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('dashboard');
    }
    public function login(Request $request)
    {
        return view('login');
    }

    public function login_check(Request $request)
    {
        $is_check = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        if (!$is_check) {
            return redirect()->back();
        } else {
            return redirect('/');
        }
    }
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
