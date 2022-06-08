<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentController extends Controller
{
    public function student_add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'required',
            'profile_picture' => 'mimes:jpeg,jpg,png,gif|required|max:3000',
            'current_school' => 'required|string|max:255',
            'previous_school' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (count($errors) > 0) {
                return Response::json(['status' => false, 'message' => 'validation_error', 'data' => $errors->first()], 400);
            }
        } else {
            $image = $request->profile_picture;
            if ($image) {
                $base_url = URL::to('/');
                $file = $request->profile_picture;
                $extention = $file->getClientOriginalExtension();
                $filename = time() . rand(0, 999) . '.' . $extention;
                $publicPath = public_path('uploads/students');
                $file->move($publicPath, $filename);
                $db =  $base_url . '/uploads/students/' . $filename;
            } else {
                $db = null;
            }
            $role = Roles::where('name', 'student')->first();
            if (empty($role)) {
                return Response::json(['status' => false, 'message' => 'student role is missing', 'data' => '']);
            }
            $student = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'profile_picture' => $db,
                'current_school' => $request->current_school,
                'previous_school' => $request->previous_school,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'role_id' => $role->id,
            ]);
            if (!$student) {
                return Response::json(['status' => false, 'message' => 'something went wrong during adding student', 'data' => '']);
            } else {
                unset($student['experience']);
                unset($student['expertise_subject']);
                return Response::json(['status' => true, 'message' => 'Student Added Successfully', 'data' => $student]);
            }
        }
    }
    public function student_detail(Request $request)
    {
        $student = JWTAuth::user();
        unset($student['experience']);
        unset($student['expertise_subject']);
        return Response::json(['status' => true, 'message' => 'Student details found successfully', 'data' => $student]);
    }

    public function student_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'address' => 'required',
            'current_school' => 'required|string|max:255',
            'previous_school' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (count($errors) > 0) {
                return Response::json(['status' => false, 'message' => 'validation_error', 'data' => $errors->first()], 400);
            }
        } else {
            $logged_user = JWTAuth::user();
            $user = User::find($logged_user->id);
            $image = $request->profile_picture;
            if ($image) {
                $base_url = URL::to('/');
                $file = $request->profile_picture;
                $extention = $file->getClientOriginalExtension();
                $filename = time() . rand(0, 999) . '.' . $extention;
                $publicPath = public_path('uploads/students');
                $file->move($publicPath, $filename);
                $db =  $base_url . '/uploads/students/' . $filename;
                $user->profile_picture = $db;
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->address = $request->address;
            $user->current_school = $request->current_school;
            $user->previous_school = $request->previous_school;
            $user->father_name = $request->father_name;
            $user->mother_name = $request->mother_name;
            $user->save();
            unset($user['experience']);
            unset($user['expertise_subject']);
            return Response::json(['status' => true, 'message' => 'Student details updated successfully', 'data' => $user]);
        }
    }

    public function student_delete(Request $request)
    {

        $logged_user_id = JWTAuth::user()->id;
        $forever = true;
        JWTAuth::parseToken()->invalidate($forever);
        User::where('id', $logged_user_id)->delete();
        return Response::json(['status' => true, 'message' => 'Student deleted successfully']);
    }
    public function student_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (count($errors) > 0) {
                return Response::json(['status' => false, 'message' => 'validation_error', 'data' => $errors->first()], 400);
            }
        } else {
            $role = Roles::where('name', 'student')->first();
            $token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password, 'role_id' => $role->id]);
            if (!$token) {
                return Response::json(['status' => false, 'message' => 'Invalid Credentials', 'data' => '']);
            } else {
                $student = JWTAuth::user();
                $student['token'] = $token;
                return Response::json(['status' => true, 'message' => 'Login Successfully', 'data' => $student]);
            }
        }
    }

    public function assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|numeric',
            'teacher_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (count($errors) > 0) {
                return Response::json(['status' => false, 'message' => 'validation_error', 'data' => $errors->first()], 400);
            }
        }
        $student_role = Roles::where('name', 'student')->first();
        $teacher_role = Roles::where('name', 'teacher')->first();
        $student = User::where([
            'id' => $request->student_id,
            'role_id' => $student_role->id
        ])->first();

        if (empty($student)) {
            return Response::json(['status' => false, 'message' => 'No student found', 'data' => '']);
        }
        $teacher = User::where([
            'id' => $request->teacher_id,
            'role_id' => $teacher_role->id
        ])->first();
        if (empty($teacher)) {
            return Response::json(['status' => false, 'message' => 'No teacher found', 'data' => '']);
        }
        $student->teacher_assigned = $request->teacher_id;
        $student->save();
        return Response::json(['status' => true, 'message' => 'Teacher Assigned successfully', 'data' => '']);
    }
}
