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
                } else if ($filter[0] === 'group') {
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

        $groups = $query->get()->append('month_revenue')->forget("students");

        return Inertia::render('Group/Show', [
            'groups' => $groups,
            'states' => array_merge(['', 'archived'], array_values(StateLists::GROUP)),
        ]);
    }

    // public function view($id)
    // {
    //     $group = Group::with(['course.payments' => fn($q) =>
    //         $q->currentMonth()->latest(),
    //     ])
    //         ->find($id)
    //         ->append('month_revenue');

    //     if (!$group) {
    //         abort(404, "This group doesn't exist in our records");
    //     }

    //     $query = Student::query()
    //         ->with(['groups', 'payments' => function ($q) {
    //             $q->currentMonth()->latest();
    //         }])
    //         ->whereRelation('groups', 'group_id', $id);

    //     if (request('search')) {

    //         $query->where(function ($query) {
    //             $query->orWhereRelation('groups.course', 'title', 'LIKE', '%' . request('search') . '%')
    //                 ->orWhereRelation('groups.course.teacher', 'name', 'LIKE', '%' . request('search') . '%')
    //                 ->orWhereRelation('payments', 'state', 'LIKE', '%' . request('search') . '%')
    //                 ->orWhereRelation('payments', 'state', 'LIKE', '%' . request('search') . '%')
    //                 ->orWhere('name', 'LIKE', '%' . request('search') . '%')
    //                 ->orWhere('email', 'LIKE', '%' . request('search') . '%')
    //                 ->orWhere('phone', 'LIKE', '%' . request('search') . '%')
    //                 ->orWhere('age', 'LIKE', '%' . request('search') . '%')
    //                 ->orWhere('grade', 'LIKE', '%' . request('search') . '%');
    //         });
    //     }

    //     if (request('payment')) {
    //         $query->where(function ($query) {
    //             $query->whereRelation('payments', 'state', request('search'));
    //         });
    //     }

    //     if (in_array(request('filter'), StateLists::STUDENT)) {
    //         $query->where('state', request('filter'))
    //             ->orWhereRelation('students.payments', 'state', request('filter'));
    //     }

    //     if (request('filter') == 'archived') {
    //         $query->where('archived', true);
    //     }

    //     $query->with(['payments' => function ($q) {
    //         $q->currentMonth()->with('course', function ($c) {
    //             $c->select('id', 'price');
    //         })->latest()->first();
    //     }]);

    //     return Inertia::render('Group/View', [
    //         'students' => $query->get()->append('month_paid'),
    //         'group' => $group,
    //         'states' => array_merge(['', 'archived'], array_values(StateLists::STUDENT)),
    //     ]);
    // }
    public function view($id)
    {
        $query = Group::query()
            ->with([
                'students' => function ($query) {
                    $query->withCount('groups');
                    if (request('search')) {

                        $query->where(function ($query) {
                            $query->orWhereRelation('groups.course', 'title', 'LIKE', '%' . request('search') . '%')
                                ->orWhereRelation('groups.course.teacher', 'name', 'LIKE', '%' . request('search') . '%')
                                ->orWhereRelation('payments', 'state', 'LIKE', '%' . request('search') . '%')
                                ->orWhereRelation('payments', 'state', 'LIKE', '%' . request('search') . '%')
                                ->orWhere('name', 'LIKE', '%' . request('search') . '%')
                                ->orWhere('email', 'LIKE', '%' . request('search') . '%')
                                ->orWhere('phone', 'LIKE', '%' . request('search') . '%')
                                ->orWhere('age', 'LIKE', '%' . request('search') . '%')
                                ->orWhere('grade', 'LIKE', '%' . request('search') . '%');
                        });
                    }

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|numeric|exists:courses,id',
            'title' => 'required|string',
            'students' => 'array',
        ]);

        $validated = $validator->validate();

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

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:groups,id',
            'title' => 'required|string',
            'course_id' => 'sometimes|required|numeric|exists:courses,id',
            'state' => ['sometimes', 'nullable', Rule::in(StateLists::GROUP)],
            'archived' => 'sometimes|boolean',
            'students' => 'array',
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

        if ($validated['students'] !== null) {
            $group->students()->detach();
            $group->students()->attach($validated['students']);
        }

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
