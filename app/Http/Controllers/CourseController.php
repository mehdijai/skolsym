<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Models\Course;
use App\Models\Group;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function get_courses()
    {
        $query = Course::query()->where('state', 'active');

        if (request('archived')) {
            $query->where('archived', true);
        }else{
            $query->where('archived', false);
        }

        if (request('notActive')) {
            $query->orWhere('state', '!=', 'active');
        }

        if (request('groups')) {
            $query->with('groups');
        }

        $courses = $query->get();

        return response()->json($courses, 200);
    }

    public function get_course($id)
    {
        return response()->json(Course::find($id), 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'period' => 'required|numeric',
            'price' => 'required|numeric',
            'payment_type' => ['required', Rule::in(StateLists::PAYMENT_TYPE)],
            'teacher_id' => 'required|numeric|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $course = Course::create($validated);
        return response()->json($course, 200);
    }

    public function edit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:courses,id',
            'title' => 'required|string',
            'period' => 'required|numeric',
            'price' => 'required|numeric',
            'payment_type' => ['required', Rule::in(StateLists::PAYMENT_TYPE)],
            'teacher_id' => 'sometimes|required|numeric|exists:teachers,id',
            'state' => ['sometimes', 'nullable', Rule::in(StateLists::COURSE)],
            'archived' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $course = Course::find($validated['id']);

        $course->title = $validated["title"];
        $course->period = $validated["period"];
        $course->price = $validated["price"];
        $course->payment_type = $validated["payment_type"];
        $course->teacher_id = $validated["teacher_id"];

        if (array_key_exists('state', $validated)) {
            $course->state = $validated["state"];
        }

        if (array_key_exists('archived', $validated)) {

            if ((bool) $validated["archived"] == true && $course->archived == false) {
                $course->archived = true;
                $course->archived_at = new DateTime();
            }

            if ((bool) $validated["archived"] == false && $course->archived == true) {
                $course->archived = false;
                $course->archived_at = null;
            }
        }

        $course->save();

        return response()->json($course, 200);
    }

    public function delete($id)
    {
        Group::query()->where('id', $id)->update([
            'archived' => true,
            'archived_at' => new DateTime(),
        ]);
        Course::find($id)->update([
            'state' => 'hold',
            'archived' => true,
            'archived_at' => new DateTime(),
        ]);
    }
}
