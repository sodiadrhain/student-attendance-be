<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lecturer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\LecturerResource;

class LecturerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Lecturer::with('user', 'faculty', 'department')->get();
        return response([
            'lecturers' => LecturerResource::collection($students),
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
        $data = $request->all();

        $data['user_id'] = auth()->user()->id;

        $validator = Validator::make($data, [
            'title' => 'required',
            'department_id' => 'required',
            'faculty_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'gender' => 'required',
            'photo' => 'required',
            'user_id' => 'unique:lecturers'
        ]);


        if($validator->fails()){
            return response([
                'error' => [
                    'type' => 'validation error',
                    'message' => $validator->errors()
                ]], 400);
        }

        $lecturer = Lecturer::create($data);
        if ($lecturer) {
            $get_user = User::where('id', $data['user_id'])->first();
            $get_user['first_name'] = $data['first_name'];
            $get_user['last_name'] = $data['last_name'];
            $get_user['phone_number'] = $data['phone_number'];
            $get_user['gender'] = $data['gender'];
            $get_user['photo'] = $data['photo'];
            $get_user->save();
        }

        return response([
            'lecturer_data' => new LecturerResource($lecturer),
            'message' => 'Lecturer Created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Lecturer  $lecturer
     * @return \Illuminate\Http\Response
     */
    public function show(Lecturer $lecturer)
    {
        return response([
            'lecturer_data' => new LecturerResource($lecturer
                ->where('id', $lecturer->id)
                ->with('user', 'faculty', 'department')
                ->get()),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Lecturer  $lecturer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lecturer $lecturer)
    {
        $lecturer->update($request->all());
        return response([
            'student' => new LecturerResource($lecturer),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Lecturer  $lecturer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lecturer $lecturer)
    {
        $lecturer->delete();
        return response([
            'message' => 'Lecturer Record Deleted'
        ]);
    }
}
