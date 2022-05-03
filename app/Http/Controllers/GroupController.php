<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Models\Course;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Student;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GroupController extends Controller
{
    public function index()
    {
        $allowed = ['course', 'course.teacher', 'students'];

        $query = Group::query()->with('course')->withCount('students');

        if (request('search')) {
            if (strpos(request('search'), ':') !== false) {
                $filter = explode(":", request('search'));
                if (in_array($filter[0], $allowed)) {
                    $query->whereRelation($filter[0], $filter[0] == 'students' ? 'student_id' : 'id', '=', $filter[1]);
                }else if($filter[0] === 'group'){
                    $query->where('id', $filter[1]);
                }
            } else {
                $query->where(function ($query) {
                    $query->whereRelation('course', 'title', 'LIKE', '%' . request('search') . '%')
                        ->orWhereRelation('course.teacher', 'name', 'LIKE', '%' . request('search') . '%')
                        ->orWhere('title', 'LIKE', '%' . request('search') . '%');
                });
            }
        }

        if (in_array(request('filter'), StateLists::GROUP)) {
            $query->where('state', request('filter'));
        }

        if (request('filter') == 'archived') {
            $query->where('archived', true);
        }

        return Inertia::render('Group/Show', [
            'groups' => $query->get(),
            'states' => array_merge(['', 'archived'], array_values(StateLists::GROUP)),
        ]);
    }

    public function create()
    {
        return Inertia::render('Group/Create', [
            'states' => StateLists::GROUP,
            'courses' => Course::where('archived', false)->get(),
            'student' => request('student') ? Student::find(request('student')) : null,
            'withCourse' => request('course') ?? null,
            'withStudent' => request('student') ?? null,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|numeric|exists:courses,id',
            'student_id' => 'sometimes|numeric|exists:students,id',
            'title' => 'required|string',
        ]);

        $validated = $validator->validate();

        $group = new Group();
        $group->course_id = $validated['course_id'];
        $group->title = $validated['title'];
        $group->save();

        if ($validated['student_id'] !== null) {
            $group->students()->attach($validated['student_id']);
        }

        return redirect()->route('groups.index');
    }

    public function update($id)
    {
        return Inertia::render('Group/Create', [
            'states' => StateLists::GROUP,
            'group' => Group::findOrFail($id),
            'courses' => Course::where('archived', false)->get(),
        ]);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:groups,id',
            'title' => 'required|string',
            'course_id' => 'sometimes|required|numeric|exists:courses,id',
            'state' => ['sometimes', 'nullable', Rule::in(StateLists::GROUP)],
            'archived' => 'sometimes|boolean',
        ]);

        $validated = $validator->validated();

        $group = Group::find($validated['id']);

        $group->title = $validated["title"];
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

        return redirect()->route('groups.index');
    }

    public function archive($id)
    {

        $group = Group::find($id);

        if ($group->archived == true) {

            $group->archived = false;
            $group->archived_at = null;
        } else {
            $group->archived = true;
            $group->archived_at = new DateTime();
        }

        $group->save();

        return redirect()->back();
    }

    public function delete(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:groups,id',
            'assign_to' => function ($value, $attribute) {
                if ($value == null) {
                    return [
                        'nullable',
                    ];
                } else {
                    return [
                        'numeric',
                        Rule::exists(Group::class, 'id'),
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

        if ($validated['assign_to'] != null) {
            GroupStudent::where('group_id', $validated['did'])
                ->update(['group_id' => $validated['id']]);
        }

        $group = Group::find($validated['id']);

        $group->state = StateLists::GROUP['REMOVED'];
        $group->archived = true;
        $group->archived_at = new DateTime();

        $group->save();

        return redirect()->back();
    }
}
