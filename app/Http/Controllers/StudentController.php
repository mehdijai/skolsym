<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Models\Student;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function get_students()
    {
        $query = Student::query()->where('state', 'active');

        if (request('archived')) {
            $query->where('archived', true);
        } else {
            $query->where('archived', false);
        }

        if (request('notActive')) {
            $query->orWhere('state', '!=', 'active');
        }

        if (request('groups')) {
            $query->with('groups');
        }

        $students = $query->get();

        return response()->json($students, 200);
    }

    public function get_student($id)
    {
        $query = Student::query();

        if (request('groups')) {
            $query->with('groups');
        }

        $student = $query->find($id);

        return response()->json($student, 200);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|min:4',
            'email' => 'required|email|unique:teachers',
            'phone' => 'required|string',
            'age' => 'sometimes|numeric',
            'grade' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        return response()->json(Student::create($validator->validated()), 200);
    }

    public function edit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:teachers,id',
            'name' => 'required|max:50|min:4',
            'email' => 'required|email',
            'phone' => 'required|string',
            'age' => 'sometimes|numeric',
            'grade' => 'sometimes|string',
            'state' => ['sometimes', 'nullable', Rule::in(StateLists::STUDENT)],
            'archived' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

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

            if ((bool) $validated["archived"] == true && $student->archived == false) {
                $student->archived = true;
                $student->archived_at = new DateTime();
            }

            if ((bool) $validated["archived"] == false && $student->archived == true) {
                $student->archived = false;
                $student->archived_at = null;
            }
        }

        $student->save();

        return response()->json($student, 200);
    }

    public function delete($id)
    {
        $student = Student::find($id);

        $student->state = "removed";
        $student->archived = true;
        $student->archived_at = new DateTime();

        $student->save();

        return response()->json(['message' => 'student deleted'], 200);
    }

    public function assign_to_group(Request $request){

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|numeric|exists:students,id',
            'group_id' => 'required|numeric|exists:groups,id'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $student = Student::find($validated["student_id"]);

        $student->groups()->attach($validated['group_id']);

        return response()->json($student, 200);
    }
    
    public function detach_from_group(Request $request){

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|numeric|exists:students,id',
            'group_id' => 'required|numeric|exists:groups,id'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 401);
        }

        $validated = $validator->validated();

        $student = Student::find($validated["student_id"]);

        $student->groups()->detach($validated['group_id']);

        return response()->json($student, 200);
    }
}
