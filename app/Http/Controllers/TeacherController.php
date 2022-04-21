<?php

namespace App\Http\Controllers;

use App\Enum\StatesEnum;
use App\Models\Course;
use App\Models\Teacher;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function get_teachers($archived = false)
    {
        $teachers = Teacher::where('archived', $archived)->get();
        return response()->json($teachers, 200);
    }

    public function get_teacher($id)
    {
        return response()->json(Teacher::find($id), 200);
    }

    public function store(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'name' => 'required|max:50|min:4',
            'email' => 'required|email|unique:teachers',
            'phone' => 'required|string',
        ])->validated();

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
            'state' => ['nullable', Rule::in(['active', 'removed'])],
            'archived' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 500);
        }

        $validated = $validator->validated();

        $teacher = Teacher::find($validated['id']);

        $teacher->name = $validated["name"];
        $teacher->email = $validated["email"];
        $teacher->phone = $validated["phone"];

        if ($validated["state"]) {
            $teacher->state = $validated["state"];
        }

        if ((bool)$validated["archived"] == true && $teacher->archived == false) {
            $teacher->archived = true;
            $teacher->archived_at = new DateTime();
        }

        if ((bool)$validated["archived"] == false && $teacher->archived == true) {
            $teacher->archived = false;
            $teacher->archived_at = null;
        }

        $teacher->save();

        return response()->json($teacher, 200);
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|numeric',
            'assign_to' => 'numeric|exists:teachers,id',
        ]);

        $teacher = Teacher::find($validated['id']);

        $teacher->state = StatesEnum::REMOVED;

        if ($validated['assign_to'] != null) {
            Course::where('teacher_id', $request['id'])->update(['teacher_id' => $validated['assign_to']]);
        } else {
            $cc = new CourseController();
            foreach ($teacher->courses as $course) {
                $cc->delete($course->id);
            }
        }

        $teacher->save();
    }
}
