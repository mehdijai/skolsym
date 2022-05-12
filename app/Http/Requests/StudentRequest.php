<?php

namespace App\Http\Requests;

use App\Const\StateLists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch (Route::currentRouteName()) {
            case "students.store":
                return [
                    'name' => 'required|max:50|min:4',
                    'email' => 'required|email|unique:students,email',
                    'phone' => 'required|string',
                    'age' => 'sometimes|numeric',
                    'grade' => 'sometimes|string',
                    'groups' => 'array',
                ];
            case "students.edit":
                return [
                    'id' => 'required|numeric|exists:students,id',
                    'name' => 'required|max:50|min:4',
                    'email' => ['required', 'email', 'unique:students,email,' . $this->id],
                    'phone' => 'required|string',
                    'age' => 'sometimes|numeric',
                    'grade' => 'sometimes|string',
                    'state' => ['sometimes', 'nullable', Rule::in(StateLists::STUDENT)],
                    'archived' => 'sometimes|boolean',
                    'groups' => 'array',
                ];
            case "students.delete":
                return [
                    'id' => 'required|numeric|exists:students,id',
                ];
        }

        return [];
    }
}
