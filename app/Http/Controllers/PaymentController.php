<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\QueryFilter\Filters\PaymentsFilter;
use App\QueryFilter\Searches\PaymentsSearch;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->model = new Payment();
    }

    public function index()
    {

        $query = Payment::query()->with('course.teacher', 'student');

        $payments = app(Pipeline::class)
            ->send($query)
            ->through([
                PaymentsSearch::class,
                PaymentsFilter::class,
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

    public function store(PaymentRequest $request)
    {

        $validated = $request->validated();

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

    public function pay(PaymentRequest $request)
    {
        $validated = $request->validated();
        $payment = Payment::findOrFail($validated['id']);
        $payment->state = "paid";
        $payment->amount_payed = $validated['price'];
        $payment->teacher_part = $validated['price'] * $validated['teacher_percentage'];
        $payment->paid_at = now();
        $payment->save();

        return redirect()->back();
    }
}
