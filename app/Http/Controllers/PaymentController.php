<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|numeric|exists:students,id',
            'courses' => 'required|array',
        ]);

        $validated = $validator->validated();

        foreach ($validated['courses'] as $course) {
            Payment::create([
                'student_id' => $validated['student_id'],
                'course_id' => $course['id'],
                'amount_payed' => $course['price'],
                'state' => StateLists::PAYMENT['PAID'],
                'paid_at' => now(),
            ]);
        }

        return redirect()->back();
    }
}
