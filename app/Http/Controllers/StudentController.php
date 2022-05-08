<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Models\Course;
use App\Models\Student;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index()
    {
        $allowed = ['groups', 'groups.course', 'groups.course.teacher'];

        $query = Student::query()->with('groups.course.payments', function($q){
            $q->latest();
        });

        if (request('search')) {
            if (strpos(request('search'), ':') !== false) {
                $filter = explode(":", request('search'));
                if (in_array($filter[0], $allowed)) {
                    $query->whereRelation($filter[0], $filter[0] == 'groups' ? 'group_id' : 'id', $filter[1]);
                } else if ($filter[0] === 'student') {
                    $query->where('id', $filter[1]);
                }
            } else {
                $query->where(function ($query) {
                    $query->whereRelation('groups', 'title', 'LIKE', '%' . request('search') . '%')
                        ->orWhereRelation('groups.course', 'title', 'LIKE', '%' . request('search') . '%')
                        ->orWhereRelation('groups.course.teacher', 'name', 'LIKE', '%' . request('search') . '%')
                        ->orWhereRelation('payments', 'state', 'LIKE', '%' . request('search') . '%')
                        ->orWhere('name', 'LIKE', '%' . request('search') . '%')
                        ->orWhere('email', 'LIKE', '%' . request('search') . '%')
                        ->orWhere('phone', 'LIKE', '%' . request('search') . '%')
                        ->orWhere('age', 'LIKE', '%' . request('search') . '%')
                        ->orWhere('grade', 'LIKE', '%' . request('search') . '%');
                });
            }
        }

        if (in_array(request('filter'), StateLists::STUDENT)) {
            $query->where('state', request('filter'))->orWhereRelation('payments', 'state', request('filter'));
        }

        if (request('filter') == 'archived') {
            $query->where('archived', true);
        }

        $query->with(['payments' => function ($q) {
            $q->where('created_at', '>=', Carbon::now()->subMonth())->with('course', function($c){
                $c->select('id', 'price');
            })->latest()->first();
        }]);

        return Inertia::render('Student/Show', [
            'students' => $query->get(),
            'states' => array_merge(['', 'archived'], array_values(StateLists::STUDENT)),
        ]);
    }

    public function create()
    {
        return Inertia::render('Student/Create', [
            'states' => StateLists::STUDENT,
            'courses' => Course::query()
                ->where('archived', false)
                ->select('id', 'title', 'teacher_id')
                ->with([
                    'groups' => function ($query) {
                        $query->where('archived', false)->select('id', 'title', 'course_id');
                    },
                    'teacher' => function ($query) {
                        $query->select('id', 'name');
                    },
                ])
                ->get(),
            'withGroup' => request('group') ?? null,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|min:4',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|string',
            'age' => 'sometimes|numeric',
            'grade' => 'sometimes|string',
            'groups' => 'array',
        ]);

        session()->flash($validator->errors());

        $validated = $validator->validate();

        $student = new Student();
        $student->name = $validated['name'];
        $student->email = $validated['email'];
        $student->phone = $validated['phone'];
        $student->age = $validated['age'];
        $student->grade = $validated['grade'];
        $student->save();

        if ($validated['groups'] !== null) {
            $student->groups()->attach($validated['groups']);
        }

        return redirect()->route('students.index');
    }

    public function update($id)
    {
        return Inertia::render('Student/Create', [
            'student' => Student::with(['groups' => fn($q) => $q->select('groups.id', 'groups.course_id')])->findOrFail($id),
            'states' => StateLists::STUDENT,
            'courses' => Course::query()
                ->where('archived', false)
                ->select('id', 'title', 'teacher_id')
                ->with([
                    'groups' => function ($query) {
                        $query->where('archived', false)->select('id', 'title', 'course_id');
                    },
                    'teacher' => function ($query) {
                        $query->select('id', 'name');
                    },
                ])
                ->get(),
            'withGroup' => request('group') ?? null,
        ]);
    }

    public function edit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:students,id',
            'name' => 'required|max:50|min:4',
            'email' => ['required', 'email', 'unique:students,email,' . $request->id],
            'phone' => 'required|string',
            'age' => 'sometimes|numeric',
            'grade' => 'sometimes|string',
            'state' => ['sometimes', 'nullable', Rule::in(StateLists::STUDENT)],
            'archived' => 'sometimes|boolean',
            'groups' => 'array',
        ]);

        $validated = $validator->validated();

        $student = Student::find($validated['id']);

        $student->name = $validated["name"];
        $student->email = $validated["email"];
        $student->phone = $validated["phone"];
        $student->age = $validated["age"];
        $student->grade = $validated["grade"];

        if (array_key_exists('state', $validated)) {
            $student->state = $validated["state"];
        }

        if (array_key_exists('archived', $validated)) {

            if ($validated["archived"] == true) {
                $student->archived = true;
                $student->archived_at = new DateTime();
            }

            if ($validated["archived"] == false) {
                $student->archived = false;
                $student->archived_at = null;
            }
        }

        $student->save();

        if ($validated['groups'] !== null) {
            $student->groups()->detach();
            $student->groups()->attach($validated['groups']);
        }

        return redirect()->route('students.index');
    }

    public function archive($id)
    {

        $student = Student::find($id);

        if ($student->archived == true) {

            $student->archived = false;
            $student->archived_at = null;
        } else {
            $student->archived = true;
            $student->archived_at = new DateTime();
        }

        $student->save();

        return redirect()->back();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:students,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validate();

        $student = Student::find($validated['id']);

        $student->state = "removed";
        $student->archived = true;
        $student->archived_at = new DateTime();

        $student->save();

        return redirect()->back();
    }

    public function assign_to_group(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|numeric|exists:students,id',
            'group_id' => 'required|numeric|exists:groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $student = Student::find($validated["student_id"]);

        $student->groups()->attach($validated['group_id']);

        return response()->json($student, 200);
    }

    public function detach_from_group(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|numeric|exists:students,id',
            'group_id' => 'required|numeric|exists:groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $student = Student::find($validated["student_id"]);

        $student->groups()->detach($validated['group_id']);

        return response()->json($student, 200);
    }
}
