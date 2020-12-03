<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function checkUser(Request $request)
    {
        $data = $request->only(['user_type', 'user_id']);
        $rules = [
            'user_type' => 'required|in:lecturer,student,admin',
            'user_id' => 'required|exists:users'
        ];

        $validate_user_data = Validator::make($data, $rules);

        if ($validate_user_data->fails()) {
            return response()->json([
                'error' => [
                    'type' => 'validation error',
                    'message' => $validate_user_data->errors()
                ]], 400);
        }

        $user_id = $request->user_id;

        if ($request->user_type === 'lecturer') {
            $this->updateLecturerProfile($user_id);
        }

        if ($request->user_type === 'student') {
            $student_data = [
                'matric_no' => $request->matric_no,
                'level' => $request->level,
                'department' => $request->department_id,
                'faculty' => $request->faculty_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'gender' => $request->gender,
                'photo' => $request->photo
            ];

            $this->updateStudentProfile($user_id, $student_data);
        }
    }

    public function updateStudentProfile($user_id, array $student_data)
    {
        foreach ($student_data as $student_datum)
        {
            $rules = [
                $student_datum => 'required',
            ];

            $validate_user_data = Validator::make($student_data, $rules);

            if ($validate_user_data->fails()) {
                return response()->json([
                    'error' => [
                        'type' => 'validation error',
                        'message' => $validate_user_data->errors()
                    ]], 400);
            }
        }
    }

    public function updateLecturerProfile($user_id)
    {

    }
}
