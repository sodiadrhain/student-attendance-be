<?php

namespace App\Http\Controllers\Api;

use App\Attendance;
use App\AttendanceClass;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceClassResource;
use App\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lecturer_id = Lecturer::where('user_id', auth()->user()->id)->first();
        $check_attendance = Attendance::where('lecturer_id', $lecturer_id->id)->first();
        $attendance_classes = AttendanceClass::where('attendance_id', $check_attendance->id)
            ->with('attendance')->get();
        return response([
            'attendance classes' => AttendanceClassResource::collection($attendance_classes),
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
        if (auth()->user()->user_type !== 'lecturer') {
            return response([
                'error' => 'no access'
            ], 400);
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'attendance_id' => 'required'
        ]);

        if($validator->fails()){
            return response([
                'error' => [
                    'type' => 'validation error',
                    'message' => $validator->errors()
                ]], 400);
        }

        $check_attendance_id = Attendance::where('id', $data['attendance_id'])->first();
        if (!$check_attendance_id) {
            return response([
                'error' => [
                    'type' => 'creating error',
                    'message' => 'attendance does not exist, enter correct attendance id'
                ]], 400);
        }

        $lecturer_id = Lecturer::where('user_id', auth()->user()->id)->first();
        $check_attendance = Attendance::where('lecturer_id', $lecturer_id->id)->get();
        foreach ($check_attendance as $check_attend) {
            if ($check_attend->id === $data['attendance_id']) {
                $check_exists = AttendanceClass::where([
                    'qr_code_data' => $request->qr_code_data
                ])->first();

                if ($check_exists) {
                    return response([
                        'error' => [
                            'type' => 'creating error',
                            'message' => 'attendance class already exists for this course in this session'
                        ]], 400);
                }

                $data['qr_code_data'] = md5($check_attendance_id.time());

                $attendance_class = AttendanceClass::create($data);
                if ($attendance_class) {
                    return response([
                        'attendance_class_data' => new AttendanceClassResource($attendance_class),
                        'message' => 'Attendance Class Created successfully'
                    ], 201);
                }
            }
        }

        return response([
            'error' => [
                'type' => 'cannot create attendance class',
                'message' => 'an error occurred, please try again'
            ]], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AttendanceClass  $attendanceClass
     * @return \Illuminate\Http\Response
     */
    public function show(AttendanceClass $attendanceClass)
    {
        return response([
            'attendance_data' => new AttendanceClassResource($attendanceClass
                ->where('id', $attendanceClass->id)
                ->with('attendance')
                ->get()),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AttendanceClass  $attendanceClass
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AttendanceClass $attendanceClass)
    {
        if (auth()->user()->user_type !== 'lecturer' || auth()->user()->user_type !== 'admin') {
            return response([
                'error' => 'no access'
            ], 400);
        }

        $attendanceClass->update($request->all());
        return response([
            'attendance' => new AttendanceClassResource($attendanceClass),
            'message' => 'Updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AttendanceClass  $attendanceClass
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttendanceClass $attendanceClass)
    {
        //
    }
}
