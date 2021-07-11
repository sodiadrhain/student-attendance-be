<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lecturer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\LecturerResource;

class LecturerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Lecturer $lecturer
     * @return Response
     */
    public function index(Lecturer $lecturer)
    {
        if (auth()->user()->user_type === 'admin') {
            $lecturer = Lecturer::with('user', 'faculty', 'department')->get();
            return response([
                'data' => LecturerResource::collection($lecturer),
                'message' => 'Retrieved successfully'
            ], 200);
        }

        $data = $lecturer->where('user_id', auth()->user()->id)->first();
        if ($data) {
            return response([
                'data' => new LecturerResource($lecturer
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
     * @return Response
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
            'data' => new LecturerResource($lecturer),
            'message' => 'Lecturer Created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Lecturer  $lecturer
     * @return Response
     */
    public function show(Lecturer $lecturer)
    {
        return response([
            'data' => new LecturerResource($lecturer
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
     * @return Response
     */
    public function update(Request $request, Lecturer $lecturer)
    {
        $lecturer->update($request->all());
        return response([
            'data' => new LecturerResource($lecturer),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Lecturer  $lecturer
     * @return Response
     */
    public function destroy(Lecturer $lecturer)
    {
        $lecturer->delete();
        return response([
            'message' => 'Lecturer Record Deleted'
        ]);
    }
}
