<?php

namespace App\Http\Requests;

use App\Const\StateLists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
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
            case "payments.store":
                return [
                    'student_id' => 'required|numeric|exists:students,id',
                    'courses' => 'required|array',
                    'state' => ['sometimes', 'nullable', Rule::in(StateLists::PAYMENT)]
                ];
            case "payments.pay":
                return [
                    'id' => 'required|numeric|exists:payments,id',
                    'price' => 'required|numeric',
                    'teacher_percentage' => 'required|numeric',
                ];
            case "payments.edit":
                return [
                    'id' => 'required|numeric|exists:payments,id',
                    'student_id' => 'required|numeric|exists:students,id',
                    'course_id' => 'required|numeric|exists:courses,id',
                    'state' => ['sometimes', 'nullable', Rule::in(StateLists::PAYMENT)],
                    'archived' => 'sometimes|boolean',
                ];
            case "payments.delete":
                return [
                    
                ];
        }

        return [];
    }
}
