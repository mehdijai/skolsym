<?php

namespace App\Http\Requests;

use App\Const\StateLists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class GroupRequest extends FormRequest
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
            case "groups.store":
                return [
                    'course_id' => 'required|numeric|exists:courses,id',
                    'title' => 'required|string',
                    'students' => 'array',
                ];
            case "groups.edit":
                return [
                    'id' => 'required|numeric|exists:groups,id',
                    'title' => 'required|string',
                    'course_id' => 'sometimes|required|numeric|exists:courses,id',
                    'state' => ['sometimes', 'nullable', Rule::in(StateLists::GROUP)],
                    'archived' => 'sometimes|boolean',
                    'students' => 'array',
                ];
            case "groups.delete":
                return [
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
                ];
        }

        return [];
    }
}
