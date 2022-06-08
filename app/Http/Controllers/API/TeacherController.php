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

class TeacherController extends Controller
{
    public function teacher_add(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'address' => 'required',
            'profile_picture' => 'mimes:jpeg,jpg,png,gif|required|max:3000',
            'experience' => 'required|numeric',
            'expertise_subject' => 'required|string|max:255',
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
                $publicPath = public_path('uploads/teachers');
                $file->move($publicPath, $filename);
                $db =  $base_url . '/uploads/teachers/' . $filename;
            } else {
                $db = null;
            }
            $role = Roles::where('name', 'teacher')->first();
            if (empty($role)) {
                return Response::json(['status' => false, 'message' => 'Teacher role is missing', 'data' => '']);
            }
            $teacher = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'profile_picture' => $db,
                'current_school' => $request->current_school ? $request->current_school : null,
                'previous_school' => $request->previous_school ? $request->previous_school : null,
                'experience' => $request->experience,
                'expertise_subject' => $request->expertise_subject,
                'role_id' => $role->id,
            ]);
            if (!$teacher) {
                return Response::json(['status' => false, 'message' => 'Something went wrong during adding teacher', 'data' => '']);
            } else {
                unset($teacher['teacher_assigned']);
                unset($teacher['father_name']);
                unset($teacher['mother_name']);
                return Response::json(['status' => true, 'message' => 'Teacher Added Successfully', 'data' => $teacher]);
            }
        }
    }
    public function teacher_detail(Request $request)
    {
        $teacher = JWTAuth::user();
        unset($teacher['teacher_assigned']);
        unset($teacher['father_name']);
        unset($teacher['mother_name']);
        return Response::json(['status' => true, 'message' => 'teacher details found successfully', 'data' => $teacher]);
    }

    public function teacher_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'address' => 'required',
            'experience' => 'required|numeric',
            'expertise_subject' => 'required|string|max:255',
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
                $publicPath = public_path('uploads/teachers');
                $file->move($publicPath, $filename);
                $db =  $base_url . '/uploads/teachers/' . $filename;
                $user->profile_picture = $db;
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->address = $request->address;
            if ($request->current_school) {
                $user->current_school = $request->current_school;
            }
            if ($request->previous_school) {
                $user->previous_school = $request->previous_school;
            }
            $user->experience = $request->experience;
            $user->expertise_subject = $request->expertise_subject;
            $user->save();
            unset($user['father_name']);
            unset($user['mother_name']);
            unset($user['teacher_assigned']);
            return Response::json(['status' => true, 'message' => 'teacher details updated successfully', 'data' => $user]);
        }
    }

    public function teacher_delete(Request $request)
    {
        $logged_user_id = JWTAuth::user()->id;
        $forever = true;
        JWTAuth::parseToken()->invalidate($forever);
        User::where('id', $logged_user_id)->delete();
        return Response::json(['status' => true, 'message' => 'teacher deleted successfully']);
    }
    public function teacher_login(Request $request)
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
            $role = Roles::where('name', 'teacher')->first();
            if (empty($role)) {
                return Response::json(['status' => false, 'message' => 'Role is missing', 'data' => '']);
            }
            $token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password, 'role_id' => $role->id]);
            if (!$token) {
                return Response::json(['status' => false, 'message' => 'Invalid Credentials', 'data' => '']);
            } else {
                $teacher = JWTAuth::user();
                $teacher['token'] = $token;
                return Response::json(['status' => true, 'message' => 'Login Successfully', 'data' => $teacher]);
            }
        }
    }

    public function assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|numeric',
            'teacher_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (count($errors) > 0) {
                return Response::json(['status' => false, 'message' => 'validation_error', 'data' => $errors->first()], 400);
            }
        }
        $teacher_role = Roles::where('name', 'teacher')->first();
        $teacher_role = Roles::where('name', 'teacher')->first();
        $teacher = User::where([
            'id' => $request->teacher_id,
            'role_id' => $teacher_role->id
        ])->first();

        if (empty($teacher)) {
            return Response::json(['status' => false, 'message' => 'No teacher found', 'data' => '']);
        }
        $teacher = User::where([
            'id' => $request->teacher_id,
            'role_id' => $teacher_role->id
        ])->first();
        if (empty($teacher)) {
            return Response::json(['status' => false, 'message' => 'No teacher found', 'data' => '']);
        }
        $teacher->teacher_assigned = $request->teacher_id;
        $teacher->save();
        return Response::json(['status' => true, 'message' => 'Teacher Assigned successfully', 'data' => '']);
    }
}