<?php

namespace App\Http\Requests;

use App\Const\StateLists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class TeacherRequest extends FormRequest
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
            case "teachers.store":
                return [
                    'name' => 'required|max:50|min:4',
                    'email' => 'required|email|unique:teachers',
                    'phone' => 'required|string',
                    'state' => ['required', Rule::in(StateLists::TEACHER)],
                ];
            case "teachers.edit":
                return [
                    'id' => 'required|numeric|exists:teachers,id',
                    'name' => 'required|max:50|min:4',
                    'email' => 'required|email',
                    'phone' => 'required|string',
                    'state' => ['required', Rule::in(StateLists::TEACHER)],
                    'archived' => 'required|boolean',
                ];
            case "teachers.delete":
                return [
                    'id' => 'required|numeric|exists:teachers,id',
                    'assign_to' => function ($value, $attribute) {
                        if ($value == null) {
                            return [
                                'nullable',
                            ];
                        } else {
                            return [
                                'numeric',
                                Rule::exists(Teacher::class, 'id'),
                            ];
                        }
                    },
                ];
        }

        return [];

    }
}
