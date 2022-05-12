<?php

namespace App\Http\Requests;

use App\Const\StateLists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
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
            case "courses.store":
                return [
                    'title' => 'required|string',
                    'period' => 'required|numeric',
                    'price' => 'required|numeric',
                    'payment_type' => ['required', Rule::in(StateLists::PAYMENT_TYPE)],
                    'teacher_id' => 'required|numeric|exists:teachers,id',
                    'teacher_percentage' => 'required|numeric',
                    'state' => ['sometimes', 'nullable', Rule::in(StateLists::COURSE)],
                ];
            case "courses.edit":
                return [
                    'id' => 'required|numeric|exists:courses,id',
                    'title' => 'required|string',
                    'period' => 'required|numeric',
                    'price' => 'required|numeric',
                    'payment_type' => ['required', Rule::in(StateLists::PAYMENT_TYPE)],
                    'teacher_id' => 'sometimes|required|numeric|exists:teachers,id',
                    'state' => ['sometimes', 'nullable', Rule::in(StateLists::COURSE)],
                    'archived' => 'sometimes|boolean',
                    'teacher_percentage' => 'required|numeric',
                ];
            case "courses.delete":
                return [
                    'id' => 'required|numeric|exists:courses,id',
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
