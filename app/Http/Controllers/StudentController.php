<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function student_list(Request $request)
    {
        $students = User::join('roles', 'roles.id', 'users.role_id')->where('roles.name', 'student')->get();
        return view('student.list',compact('students'));
    }
}
