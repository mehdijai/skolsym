<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Http\Requests\TeacherRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\Student;
use App\Models\Teacher;
use App\QueryFilter\Filters\TeachersFilter;
use App\QueryFilter\Searches\TeachersSearch;
use DateTime;
use Illuminate\Pipeline\Pipeline;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index()
    {
        $query = Teacher::query()->withCount('courses');

        $teachers = app(Pipeline::class)
            ->send($query)
            ->through([
                TeachersSearch::class,
                TeachersFilter::class,
            ])
            ->thenReturn()
            ->latest()
            ->get()
            ->append('month_revenue');

        return Inertia::render('Teacher/Show', [
            'teachers' => $teachers,
            'states' => array_merge(['', 'archived'], array_values(StateLists::TEACHER)),
        ]);
    }

    public function view($id)
    {
        $teacher = Teacher::with([
            'courses' => function ($query) {
                $query->with('teacher');
                $query->withCount('groups');
                $query->selectSub('100', 'revenue');
            },
        ])->find($id);

        if (empty($teacher)) {
            abort(404, "This teacher doesn't exist in our records");
        }

        return Inertia::render('Teacher/View', [
            'teacher' => $teacher,
            'teachers' => Teacher::select(['id', 'name'])->withCount('courses')->where('id', '!=', $id)->get(),
            'groups' => Group::query()
                ->with('course')
                ->whereRelation('course.teacher', 'id', $id)
                ->withCount('students')
                ->get()
                ->append('month_revenue'),
            'students' => Student::query()
                ->with([
                    'payments' => fn($q) => $q->currentMonth()->latest(),
                ])
                ->with('groups.course.payments')
                ->whereRelation('groups.course.teacher', 'id', $id)
                ->get()
                ->append('month_paid'),

        ]);
    }

    public function create()
    {
        return Inertia::render('Teacher/Create', [
            'states' => StateLists::TEACHER,
        ]);
    }

    public function store(TeacherRequest $request)
    {
        Teacher::create($request->validated());
        return redirect()->route('teachers.index');
    }

    public function update($id)
    {
        return Inertia::render('Teacher/Create', [
            'states' => StateLists::TEACHER,
            'teacher' => Teacher::findOrFail($id),
        ]);
    }

    public function edit(TeacherRequest $request)
    {

        $validated = $request->validated();

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

        return redirect()->back();
    }

    public function delete(TeacherRequest $request)
    {

        $validated = $request->validated();

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
