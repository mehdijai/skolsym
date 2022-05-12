<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Models\Course;
use App\Models\Group;
use App\Models\Teacher;
use App\QueryFilter\Searches\CoursesSearch;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $query = Course::query()->with('teacher')->withCount('groups');

        if (in_array(request('filter'), StateLists::COURSE)) {
            $query->where('state', request('filter'));
        }

        if (request('filter') == 'archived') {
            $query->where('archived', true);
        }

        $courses = app(Pipeline::class)
            ->send($query)
            ->through([
                CoursesSearch::class,
            ])
            ->thenReturn()
            ->latest()
            ->get()
            ->append('month_revenue');

        return Inertia::render('Course/Show', [
            'courses' => $courses,
            'states' => array_merge(['', 'archived'], array_values(StateLists::COURSE)),
        ]);
    }

    public function create()
    {
        return Inertia::render('Course/Create', [
            'states' => StateLists::COURSE,
            'payment_types' => StateLists::PAYMENT_TYPE,
            'teachers' => Teacher::where('archived', false)->get(),
            'courses' => Course::where('archived', false)->get(),
            'withTeacher' => request('teacher') ?? null,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'period' => 'required|numeric',
            'price' => 'required|numeric',
            'payment_type' => ['required', Rule::in(StateLists::PAYMENT_TYPE)],
            'teacher_id' => 'required|numeric|exists:teachers,id',
            'teacher_percentage' => 'required|numeric',
        ]);

        Course::create($validator->validated());

        return redirect()->route('courses.index');
    }

    public function update($id)
    {
        return Inertia::render('Course/Create', [
            'states' => StateLists::COURSE,
            'course' => Course::findOrFail($id),
            'payment_types' => StateLists::PAYMENT_TYPE,
            'teachers' => Teacher::where('archived', false)->get(),
        ]);
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
            'teacher_percentage' => 'required|numeric',
        ]);

        $validated = $validator->validated();

        $course = Course::find($validated['id']);

        $course->title = $validated["title"];
        $course->period = $validated["period"];
        $course->price = $validated["price"];
        $course->teacher_percentage = $validated["teacher_percentage"];
        $course->payment_type = $validated["payment_type"];
        $course->teacher_id = $validated["teacher_id"];

        if (array_key_exists('state', $validated)) {
            $course->state = $validated["state"];
        }

        if (array_key_exists('archived', $validated)) {

            if ($validated["archived"] == true) {
                $course->archived = true;
                $course->archived_at = new DateTime();
            }

            if ($validated["archived"] == false) {
                $course->archived = false;
                $course->archived_at = null;
            }
        }

        $course->save();

        return redirect()->route('courses.index');
    }

    public function archive($id)
    {

        $course = Course::find($id);

        if ($course->archived == true) {

            $course->archived = false;
            $course->archived_at = null;
        } else {
            $course->archived = true;
            $course->archived_at = new DateTime();
        }

        $course->save();

        return redirect()->back();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:courses,id',
            'assign_to' => function ($value, $attribute) {
                if ($value == null) {
                    return [
                        'nullable',
                    ];
                } else {
                    return [
                        'numeric',
                        Rule::exists(Course::class, 'id'),
                    ];
                }
            },
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validate();

        $this->remove($validated['id'], $validated['assign_to'], false);

        return redirect()->back();
    }

    public function remove($id, $assign_groups_to = null, $hold = false)
    {
        $groups = Group::where('course_id', $id)->get();

        foreach ($groups as $group) {

            $group->state = $hold ? StateLists::GROUP['HOLD'] : StateLists::GROUP['REMOVED'];
            $group->archived = true;
            $group->archived_at = new DateTime();

            if ($assign_groups_to != null) {
                $group->course_id = $assign_groups_to;
            }

            $group->save();
        }

        Course::query()->where('id', $id)->update([
            'state' => $hold ? StateLists::COURSE['HOLD'] : StateLists::COURSE['REMOVED'],
            'archived' => true,
            'archived_at' => new DateTime(),
        ]);
    }
}
