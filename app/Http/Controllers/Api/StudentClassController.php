<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentClassResource;
use App\Student;
use App\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        if (auth()->user()->user_type !== 'lecturer') {
//            return response([
//                'error' => 'no access'
//            ], 400);
//        }

//        if (!$request->id) {
//            return response([
//                'error' => 'no access'
//            ], 400);
//        }

//        $lecturer_id = Lecturer::where('user_id', auth()->user()->id)->first();
//        $check_attendance = Attendance::where('lecturer_id', $lecturer_id->id)->first();
        $attendance_classes = StudentClass::where('user_id', auth()->user()->id)
            ->with('student', 'user')->get();
        return response([
            'data' => $attendance_classes,
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->user_type !== 'student') {
            return response([
                'error' => 'no access'
            ], 400);
        }

        $data = $request->all();
        $student_id = Student::where('user_id', auth()->user()->id)->first();
        $data['student_id'] = $student_id->id;
        $data['user_id'] = auth()->user()->id;

        $validator = Validator::make($data, [
            'attendance_class_id' => 'required'
        ]);


        if($validator->fails()){
            return response([
                'error' => [
                    'type' => 'validation error',
                    'message' => $validator->errors()
                ]], 400);
        }

        $check_exists = StudentClass::where([
            'student_id' => $data['student_id'],
            'attendance_class_id' => $request->attendance_class_id
        ])->first();

        if ($check_exists) {
            return response([
                'error' => [
                    'type' => 'creating error',
                    'message' => 'student already marked attendance for this class'
                ]], 400);
        }

        $student_class = StudentClass::create($data);
        if ($student_class) {
            return response([
                'data' => new StudentClassResource($student_class),
                'message' => 'Student Class Attendance Created successfully'
            ], 201);
        }

        return response([
            'error' => [
                'type' => 'cannot create student class attendance',
                'message' => 'an error occurred, please try again'
            ]], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StudentClass  $studentClass
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if (auth()->user()->user_type !== 'lecturer') {
            return response([
                'error' => 'no access'
            ], 400);
        }

        if (!$request->student_class) {
            return response([
                'error' => 'no access'
            ], 400);
        }

//        $lecturer_id = Lecturer::where('user_id', auth()->user()->id)->first();
//        $check_attendance = Attendance::where('lecturer_id', $lecturer_id->id)->first();
        $attendance_classes = StudentClass::where('attendance_class_id', $request->student_class)
            ->with('student', 'user')->get();
        return response([
            'data' => StudentClassResource::collection($attendance_classes),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StudentClass  $studentClass
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StudentClass $studentClass)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StudentClass  $studentClass
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudentClass $studentClass)
    {
        //
    }
}
