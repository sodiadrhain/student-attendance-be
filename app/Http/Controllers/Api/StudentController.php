<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Student;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Student $student
     * @return \Illuminate\Http\Response
     */
    public function index(Student $student)
    {

        if (auth()->user()->user_type === 'admin') {
            $students = Student::with('user', 'faculty', 'department')->get();
            return response([
                'data' => StudentResource::collection($students),
                'message' => 'Retrieved successfully'
            ], 200);
        }

        $data = $student->where('user_id', auth()->user()->id)->first();
        if ($data) {
            return response([
                'data' => new StudentResource($student
                    ->where('user_id', auth()->user()->id)
                    ->with('user', 'faculty', 'department')
                    ->first()),
                'message' => 'Retrieved successfully'
            ], 200);
        }

        return response([
            'data' => null,
            'message' => 'No Content'
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
//        if (auth()->user()->user_type !== 'admin') {
//            return response([
//                'error' => 'no access'
//            ]);
//        }

        $data = $request->all();

        $data['user_id'] = auth()->user()->id;

        $validator = Validator::make($data, [
            'matric_no' => 'required|unique:students',
            'level' => 'required',
            'department_id' => 'required',
            'faculty_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'gender' => 'required',
            'photo' => 'required',
            'user_id' => 'unique:students'
        ]);


        if($validator->fails()){
            return response([
                'error' => [
                    'type' => 'validation error',
                    'message' => $validator->errors()
                ]], 400);
        }

        $student = Student::create($data);
        if ($student) {
            $get_user = User::where('id', $data['user_id'])->first();
            $get_user['first_name'] = $data['first_name'];
            $get_user['last_name'] = $data['last_name'];
            $get_user['phone_number'] = $data['phone_number'];
            $get_user['gender'] = $data['gender'];
            $get_user['photo'] = $data['photo'];
            $get_user->save();
        }

        return response([
            'data' => new StudentResource($student),
            'message' => 'Student Created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
//        $data = new StudentResource($student
//            ->where('id', $student->id)
//            ->with('user', 'faculty', 'department')
//            ->first());
//
//        if ($data) {
//            return response([
//                'student_data' => $data,
//                'message' => 'Retrieved successfully'
//            ], 200);
//        }
//
//            return response([
//                'student_data' => [],
//            ], 204);

        return response([
            'message' => 'not found'
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        $student->update($request->all());
        return response([
            'data' => new StudentResource($student),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return response([
            'message' => 'Student Record Deleted'
        ]);
    }
}
