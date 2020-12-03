<?php

namespace App\Http\Controllers\Api;

use App\Faculty;
use App\Http\Controllers\Controller;
use App\Http\Resources\FacultyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faculties = Faculty::all();
        return response([
            'faculties' => FacultyResource::collection($faculties),
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

        $validator = Validator::make($data, [
            'faculty_name' => 'required|unique:faculties',
            'campus' => 'required'
        ]);


        if($validator->fails()){
            return response([
                'error' => [
                    'type' => 'validation error',
                    'message' => $validator->errors()
                ]], 400);
        }

        $faculty = Faculty::create($data);
        if ($faculty) {
            return response([
                'faculty_data' => new FacultyResource($faculty),
                'message' => 'Faculty Created successfully'
            ], 201);
        }

        return response([
            'error' => [
                'type' => 'cannot create faculty',
                'message' => 'an error occurred, please try again'
            ]], 400);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Faculty  $faculty
     * @return \Illuminate\Http\Response
     */
    public function show(Faculty $faculty)
    {
        return response([
            'faculty_data' => new FacultyResource($faculty),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Faculty  $faculty
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Faculty $faculty)
    {
        $faculty->update($request->all());
        return response([
            'faculty' => new FacultyResource($faculty),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Faculty  $faculty
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faculty $faculty)
    {
        $faculty->delete();
        return response([
            'message' => 'Faculty Record Deleted'
        ]);
    }
}
