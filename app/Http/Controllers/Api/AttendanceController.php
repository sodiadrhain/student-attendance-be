<?php

namespace App\Http\Controllers\Api;

use App\Attendance;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceResource;
use App\Lecturer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lecturer_id = Lecturer::where('user_id', auth()->user()->id)->first();
        $attendances = Attendance::where('lecturer_id', $lecturer_id->id)
            ->with('course', 'faculty', 'department')->get();
        return response([
            'attendances' => AttendanceResource::collection($attendances),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->user_type !== 'lecturer') {
            return response([
                'error' => 'no access'
                ], 400);
        }

        $data = $request->all();
        $lecturer_id = Lecturer::where('user_id', auth()->user()->id)->first();
        $data['lecturer_id'] = $lecturer_id->id;

        $validator = Validator::make($data, [
            'academic_session' => 'required',
            'semester' => 'required',
            'lecturer_id' => 'required',
            'faculty_id' => 'required',
            'department_id' => 'required',
            'course_id' => 'required'
        ]);


        if($validator->fails()){
            return response([
                'error' => [
                    'type' => 'validation error',
                    'message' => $validator->errors()
                ]], 400);
        }

        $check_exists = Attendance::where([
            'course_id' => $request->course_id,
            'academic_session' => $request->academic_session
        ])->first();

        if ($check_exists) {
            return response([
                'error' => [
                    'type' => 'creating error',
                    'message' => 'attendance already exists for this course in this session'
                ]], 400);
        }

        $attendance = Attendance::create($data);
        if ($attendance) {
            return response([
                'attendance_data' => new AttendanceResource($attendance),
                'message' => 'Attendance Created successfully'
            ], 201);
        }

        return response([
            'error' => [
                'type' => 'cannot create attendance',
                'message' => 'an error occurred, please try again'
            ]], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        return response([
            'attendance_data' => new AttendanceResource($attendance
                ->where('id', $attendance->id)
                ->with('course', 'faculty', 'department')
                ->get()),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        if (auth()->user()->user_type !== 'lecturer' || auth()->user()->user_type !== 'admin') {
            return response([
                'error' => 'no access'
            ], 400);
        }

        $attendance->update($request->all());
        return response([
            'attendance' => new AttendanceResource($attendance),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        if (auth()->user()->user_type !== 'lecturer' || auth()->user()->user_type !== 'admin') {
            return response([
                'error' => 'no access'
            ], 400);
        }
        $attendance->delete();
        return response([
            'message' => 'Attendance Record Deleted'
        ]);
    }
}
