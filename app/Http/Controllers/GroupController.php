<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Http\Requests\GroupRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Student;
use App\QueryFilter\Filters\GroupsFilter;
use App\QueryFilter\Searches\GroupsSearch;
use App\QueryFilter\Searches\StudentsSearch;
use DateTime;
use Illuminate\Pipeline\Pipeline;
use Inertia\Inertia;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->model = Group::class;
    }

    public function index()
    {
        $query = Group::query()->with('course')->withCount('students');

        $groups = app(Pipeline::class)
            ->send($query)
            ->through([
                GroupsSearch::class,
                GroupsFilter::class,
            ])
            ->thenReturn()
            ->latest()
            ->get()
            ->append('month_revenue')
            ->forget("students");

        return Inertia::render('Group/Show', [
            'groups' => $groups,
            'states' => array_merge(['', 'archived'], array_values(StateLists::GROUP)),
        ]);
    }

    public function view($id)
    {
        $query = Group::query()
            ->with([
                'students' => function ($query) {
                    $query->withCount('groups');

                    if (request('payment')) {
                        $query->where(function ($query) {
                            $query->whereRelation('payments', 'state', request('search'));
                        });
                    }

                    if (in_array(request('filter'), StateLists::STUDENT)) {
                        $query->where('state', request('filter'))
                            ->orWhereRelation('students.payments', 'state', request('filter'));
                    }

                    if (request('filter') == 'archived') {
                        $query->where('archived', true);
                    }

                    app(Pipeline::class)
                        ->send($query)
                        ->through([
                            StudentsSearch::class,
                        ])
                        ->thenReturn()
                        ->latest()
                        ->get();
                },
                'students.payments' => fn($q) => $q->currentMonth()->latest(),
            ])
            ->with('course')
            ->where('id', $id);

        $group = $query->first()->append('month_revenue');
        $group->students()->get()->append('month_paid');

        return Inertia::render('Group/View', [
            'group' => $group,
            'states' => array_merge(['', 'archived'], array_values(StateLists::STUDENT)),
        ]);
    }

    public function create()
    {
        return Inertia::render('Group/Create', [
            'states' => StateLists::GROUP,
            'courses' => Course::where('archived', false)->get(),
            'students' => Student::where('archived', false)
                ->select('id', 'name', 'email')
                ->get(),
            'withCourse' => request('course') ?? null,
            'withStudent' => request('student') ?? null,
        ]);
    }

    public function store(GroupRequest $request)
    {

        $validated = $request->validated();

        $course = Course::select('title')->findOrFail($validated['course_id']);
        $title = explode(' ', $course->title);
        $prefix = join('', array_map(fn($chunk) => $chunk[0], $title));

        $group_title = strtoupper($prefix . '_' . $validated['title']);

        $group = new Group();
        $group->course_id = $validated['course_id'];
        $group->title = $group_title;
        $group->save();

        if ($validated['students'] !== null) {
            $group->students()->attach($validated['students']);
        }

        return redirect()->route('groups.index');
    }

    public function update($id)
    {
        return Inertia::render('Group/Create', [
            'states' => StateLists::GROUP,
            'group' => Group::with(['students' => fn($q) => $q->select('students.id')])->findOrFail($id),
            'courses' => Course::where('archived', false)->get(),
            'students' => Student::where('archived', false)
                ->select('id', 'name', 'email')
                ->get(),
        ]);
    }

    public function edit(GroupRequest $request)
    {
        $validated = $request->validated();

        $group = Group::find($validated['id']);

        $group->title = strtoupper($validated["title"]);
        $group->course_id = $validated["course_id"];

        if (array_key_exists('state', $validated)) {
            $group->state = $validated["state"];
        }

        if (array_key_exists('archived', $validated)) {

            if ($validated["archived"] == true) {
                $group->archived = true;
                $group->archived_at = new DateTime();
            }

            if ($validated["archived"] == false) {
                $group->archived = false;
                $group->archived_at = null;
            }
        }

        $group->save();

        if ($validated['students'] !== null) {
            $group->students()->detach();
            $group->students()->attach($validated['students']);
        }

        return redirect()->route('groups.index');
    }

    public function delete(GroupRequest $request)
    {

        $validated = $request->validated();

        if ($validated['assign_to'] != 'null') {
            GroupStudent::where('group_id', $validated['id'])
                ->update(['group_id' => $validated['assign_to']]);
        } else {
            GroupStudent::where('group_id', $validated['id'])
                ->delete();
        }

        $group = Group::find($validated['id']);

        $group->state = StateLists::GROUP['REMOVED'];
        $group->archived = true;
        $group->archived_at = new DateTime();

        $group->save();

        return redirect()->back();
    }
}
