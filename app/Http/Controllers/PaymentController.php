<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Models\Payment;
use App\QueryFilter\Filters\PaymentsFilter;
use App\QueryFilter\Searches\PaymentsSearch;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {

        $query = Payment::query()->with('course.teacher', 'student');

        $payments = app(Pipeline::class)
            ->send($query)
            ->through([
                PaymentsSearch::class,
                PaymentsFilter::class
            ])
            ->thenReturn()
            ->latest()
            ->get();

        return Inertia::render('Payment/Show', [
            'payments' => $payments,
            'states' => array_merge(['', 'archived'], array_values(StateLists::PAYMENT)),
        ]);
    }

    public function create()
    {

    }

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
                'teacher_part' => $course['price'] * $course['teacher_percentage'],
                'state' => StateLists::PAYMENT['PAID'],
                'paid_at' => now(),
            ]);
        }

        return redirect()->back();
    }

    public function update($id)
    {

    }

    public function edit(Request $request)
    {

    }

    public function delete($id)
    {

    }

    public function pay(Request $request)
    {
        $payment = Payment::findOrFail($request->id);
        $payment->state = "paid";
        $payment->amount_payed = $request->price;
        $payment->teacher_part = $request->price * $request->teacher_percentage;
        $payment->paid_at = now();
        $payment->save();

        return redirect()->back();
    }
}
