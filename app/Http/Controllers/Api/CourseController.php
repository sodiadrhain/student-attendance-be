<?php

namespace App\Http\Controllers\Api;

use App\Course;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Course::with('department', 'faculty')->get();
        return response([
            'courses' => CourseResource::collection($courses),
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
            'course_code' => 'required|unique:courses',
            'faculty_id' => 'required',
            'department_id' => 'required',
            'faculty_course' => 'required',
            'department_course' => 'required'
        ]);


        if($validator->fails()){
            return response([
                'error' => [
                    'type' => 'validation error',
                    'message' => $validator->errors()
                ]], 400);
        }

        $course = Course::create($data);
        if ($course) {
            return response([
                'course_data' => new CourseResource($course),
                'message' => 'Course Created successfully'
            ], 201);
        }

        return response([
            'error' => [
                'type' => 'cannot create course',
                'message' => 'an error occurred, please try again'
            ]], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        return response([
            'course_data' => new CourseResource($course
                ->where('id', $course->id)
                ->with('department', 'faculty')
                ->get()),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        $course->update($request->all());
        return response([
            'course' => new CourseResource($course),
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return response([
            'message' => 'Course Record Deleted'
        ]);
    }
}
