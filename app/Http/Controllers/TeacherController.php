<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Models\Course;
use App\Models\Teacher;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function get_teachers()
    {
        $query = Teacher::query()->where('state', 'active');

        if (request('archived')) {
            $query->where('archived', true);
        } else {
            $query->where('archived', false);
        }

        if (request('notActive')) {
            $query->orWhere('state', '!=', 'active');
        }

        if (request('courses')) {
            $query->with('courses');
        }

        $teachers = $query->get();

        return response()->json($teachers, 200);
    }

    public function get_teacher($id)
    {
        $query = Teacher::query();

        if (request('courses')) {
            $query->with('courses');
        }

        $teacher = $query->find($id);

        return response()->json($teacher, 200);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|min:4',
            'email' => 'required|email|unique:teachers',
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $teacher = Teacher::create($validated);
        return response()->json($teacher, 200);
    }

    public function edit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:teachers,id',
            'name' => 'required|max:50|min:4',
            'email' => 'required|email',
            'phone' => 'required|string',
            'state' => ['sometimes', 'nullable', Rule::in(StateLists::TEACHER)],
            'archived' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $teacher = Teacher::find($validated['id']);

        $teacher->name = $validated["name"];
        $teacher->email = $validated["email"];
        $teacher->phone = $validated["phone"];

        if (array_key_exists('state', $validated)) {
            $teacher->state = $validated["state"];
        }

        if (array_key_exists('archived', $validated)) {

            if ((bool) $validated["archived"] == true && $teacher->archived == false) {
                $teacher->archived = true;
                $teacher->archived_at = new DateTime();
            }

            if ((bool) $validated["archived"] == false && $teacher->archived == true) {
                $teacher->archived = false;
                $teacher->archived_at = null;
            }
        }

        $teacher->save();

        return response()->json($teacher, 200);
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|numeric',
            'assign_to' => 'sometimes|numeric|exists:teachers,id',
        ]);

        $teacher = Teacher::find($validated['id']);

        $teacher->state = "removed";
        $teacher->archived = true;
        $teacher->archived_at = new DateTime();

        if (array_key_exists('assign_to', $validated) && $validated['assign_to'] != null) {
            Course::where('teacher_id', $request['id'])->update(['teacher_id' => $validated['assign_to']]);
        } else {
            $cc = new CourseController();
            foreach ($teacher->courses as $course) {
                $cc->delete($course->id);
            }
        }

        $teacher->save();

        return response()->json(['message' => 'teacher deleted'], 200);
    }
}
