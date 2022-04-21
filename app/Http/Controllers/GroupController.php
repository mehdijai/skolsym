<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Models\Group;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    public function get_groups()
    {
        $query = Group::query()->where('state', 'active');

        if (request('archived')) {
            $query->where('archived', true);
        } else {
            $query->where('archived', false);
        }

        if (request('notActive')) {
            $query->orWhere('state', '!=', 'active');
        }

        if (request('students')) {
            $query->with('students');
        }

        $groups = $query->get();

        return response()->json($groups, 200);
    }

    public function get_group($id)
    {
        return response()->json(Group::find($id), 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|numeric|exists:courses,id',
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $course = Group::create($validated);
        return response()->json($course, 200);
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

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $group = Group::find($validated['id']);

        $group->title = $validated["title"];
        $group->course_id = $validated["course_id"];

        if (array_key_exists('state', $validated)) {
            $group->state = $validated["state"];
        }

        if (array_key_exists('archived', $validated)) {

            if ((bool) $validated["archived"] == true && $group->archived == false) {
                $group->archived = true;
                $group->archived_at = new DateTime();
            }

            if ((bool) $validated["archived"] == false && $group->archived == true) {
                $group->archived = false;
                $group->archived_at = null;
            }
        }

        $group->save();

        return response()->json($group, 200);
    }

    public function delete($id)
    {
        Group::query()->where('id', $id)->update([
            'state' => StateLists::GROUP['REMOVED'],
            'archived' => true,
            'archived_at' => new DateTime(),
        ]);
    }

    public function assign_student(Request $request){

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|numeric|exists:students,id',
            'group_id' => 'required|numeric|exists:groups,id'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $group = Group::find($validated["group_id"]);

        $group->students()->attach($validated['student_id']);

        return response()->json($group, 200);
    }

    public function detach_student(Request $request){

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|numeric|exists:students,id',
            'group_id' => 'required|numeric|exists:groups,id'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $group = Group::find($validated["group_id"]);

        $group->students()->detach($validated['student_id']);

        return response()->json($group, 200);
    }
}
