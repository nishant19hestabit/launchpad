<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TeacherController extends Controller
{
    public function teacher_list(Request $request)
    {
        $teachers = User::join('roles', 'roles.id', 'users.role_id')
            ->where('roles.name', 'teacher')
            ->select('users.*')
            ->get();
        return view('teacher.list', compact('teachers'));
    }
    public function teacher_approve(Request $request)
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
