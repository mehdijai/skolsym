<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Http\Requests\StudentRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\Student;
use App\QueryFilter\Filters\StudentsFilter;
use App\QueryFilter\Searches\StudentsSearch;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->model = new Student();
    }

    public function index()
    {
        $query = Student::query()
            ->with([
                'payments' => function ($q) {
                    $q->currentMonth()->latest();
                },
                'groups.course.payments' => function ($q) {
                    $q->currentMonth()->latest();
                }
            ]);

        $students = app(Pipeline::class)
            ->send($query)
            ->through([
                StudentsSearch::class,
                StudentsFilter::class,
            ])
            ->thenReturn()
            ->latest()
            ->get();

        return Inertia::render('Student/Show', [
            'students' => $students,
            'states' => array_merge(['', 'archived'], array_values(StateLists::STUDENT), array_values(StateLists::PAYMENT)),
        ]);
    }

    public function view($id)
    {
        $student = Student::with([
            'payments' => function ($query) {
                $query->with('course.teacher', 'student')
                    ->latest();
            },
            'groups' => function ($query) {
                $query->withCount('students')
                    ->with('course');
            },
        ])
            ->find($id);

        $student->groups()->get()->map(function (Group $group) {
            $group->append('month_revenue');
        });

        if (empty($student)) {
            abort(404, "This student doesn't exist in our records");
        }

        return Inertia::render('Student/View', [
            'student' => $student,
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

    public function store(StudentRequest $request)
    {

        $validated = $request->validated();

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

    public function edit(StudentRequest $request)
    {

        $validated = $request->validated();

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

    public function delete(StudentRequest $request)
    {
        $validated = $request->validated();

        $this->remove($validated['id']);

        return redirect()->back();
    }

    public function remove($id)
    {
        Student::find($id)->remove();
    }
}
