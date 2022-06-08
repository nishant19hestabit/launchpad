<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class StudentController extends Controller
{
    public function student_list(Request $request)
    {
        $students = User::join('roles', 'roles.id', 'users.role_id')
            ->where('roles.name', 'student')
            ->select('users.*')
            ->get();
        return view('student.list', compact('students'));
    }
    public function student_approve(Request $request)
    {
        $user = User::find($request->id);
        if ($user->is_approved == 0) {
            $user->is_approved = 1;
        } else if ($user->is_approved == 1) {
            $user->is_approved = 0;
        }
        $user->save();
        return Redirect::back();
    }
}
