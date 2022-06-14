<?php

namespace App\Http\Controllers;

use App\Events\RealTimeMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;

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
        $token = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        if (!isset($token)) {
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
    public function welcome(Request $request)
    {
        // event(new RealTimeMessage('hello world!'));
        return view('sendNotification');
    }
    public function send_notification(Request $request)
    {
        event(new RealTimeMessage('hello world!'));
        echo 'Notification Sent';
    }
}
