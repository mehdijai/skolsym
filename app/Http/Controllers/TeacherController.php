<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Models\Course;
use App\Models\Teacher;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index()
    {
        $query = Teacher::query()->withCount('courses');

        switch (request('filter')) {
            case StateLists::TEACHER['ACTIVE']:
                $query->where('state', 'active')->where('archived', false);
                break;
            case StateLists::TEACHER['REMOVED']:
                $query->where('state', 'removed')->where('archived', false);
                break;
            case 'archived':$query->where('archived', true);
                break;
        }

        if (request('search')) {
            $query->where(function ($query) {
                $query->where('name', 'LIKE', '%' . request('search') . '%')
                    ->orWhere('email', 'LIKE', '%' . request('search') . '%')
                    ->orWhere('phone', 'LIKE', '%' . request('search') . '%');
            });
        }

        return Inertia::render('Teacher/Show', [
            'teachers' => $query->get(),
            'states' => array_merge(['', 'archived'], array_values(StateLists::TEACHER)),
        ]);
    }

    public function create()
    {
        return Inertia::render('Teacher/Create', [
            'states' => StateLists::TEACHER,
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|min:4',
            'email' => 'required|email|unique:teachers',
            'phone' => 'required|string',
            'state' => ['required', Rule::in(StateLists::TEACHER)],
        ]);

        Teacher::create($validator->validated());

        return redirect()->route('teachers.index');
    }

    public function update($id)
    {
        return Inertia::render('Teacher/Create', [
            'states' => StateLists::TEACHER,
            'teacher' => Teacher::findOrFail($id),
        ]);
    }

    public function edit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:teachers,id',
            'name' => 'required|max:50|min:4',
            'email' => 'required|email',
            'phone' => 'required|string',
            'state' => ['required', Rule::in(StateLists::TEACHER)],
            'archived' => 'required|boolean',
        ]);

        $validated = $validator->validated();

        $teacher = Teacher::find($validated['id']);

        $teacher->name = $validated["name"];
        $teacher->email = $validated["email"];
        $teacher->phone = $validated["phone"];
        $teacher->state = $validated["state"];

        if (array_key_exists('archived', $validated)) {

            if ($validated["archived"] == true) {
                $teacher->archived = true;
                $teacher->archived_at = new DateTime();
            }

            if ($validated["archived"] == false) {
                $teacher->archived = false;
                $teacher->archived_at = null;
            }
        }

        $teacher->save();

        return redirect()->route('teachers.index');
    }

    public function archive($id)
    {

        $teacher = Teacher::find($id);

        if ($teacher->archived == true) {

            $teacher->archived = false;
            $teacher->archived_at = null;
        } else {
            $teacher->archived = true;
            $teacher->archived_at = new DateTime();
        }

        $teacher->save();

        return redirect()->route('teachers.index');
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:teachers,id',
            'assign_to' => function ($value, $attribute) {
                if ($value == null) {
                    return [
                        'nullable',
                    ];
                } else {
                    return [
                        'numeric',
                        Rule::exists(Teacher::class, 'id'),
                    ];
                }
            },
        ]);

        if ($validator->fails()) {
            return redirect()->route('teachers.index')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $teacher = Teacher::find($validated['id']);

        $teacher->state = StateLists::TEACHER['REMOVED'];
        $teacher->archived = true;
        $teacher->archived_at = new DateTime();

        if (array_key_exists('assign_to', $validated) && $validated['assign_to'] != null) {
            Course::where('teacher_id', $request['id'])->update(['teacher_id' => $validated['assign_to']]);
        } else {
            $cc = new CourseController();
            foreach ($teacher->courses as $course) {
                $cc->remove($course->id, null, true);
            }
        }

        $teacher->save();

        return redirect()->route('teachers.index');
    }
}
