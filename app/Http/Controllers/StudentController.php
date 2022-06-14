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
        $student_array = array();
        foreach ($students as $i) {
            $data = User::join('roles', 'roles.id', 'users.role_id')
                ->where('roles.name', 'teacher')
                ->where('users.id', $i->teacher_assigned)
                ->select('users.name')
                ->first();
            $i['teacher'] = $data ? $data->name : null;
            array_push($student_array, $i);
        }
        return view('student.list', compact('student_array'));
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
