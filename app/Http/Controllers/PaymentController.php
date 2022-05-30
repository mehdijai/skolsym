<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Http\Requests\PaymentRequest;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Student;
use App\QueryFilter\Filters\PaymentsFilter;
use App\QueryFilter\Searches\PaymentsSearch;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->model = new Payment();
    }

    public function index()
    {

        $query = Payment::query()->with('group.course.teacher', 'student');

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
        $students = Student::where('state', '!=', 'removed')->where('archived', false)->get();

        $students->map(function (Student $student) {
            $student->courses = $student->groups()->with('course.teacher')->get()->pluck('course')->toArray();
        });

        return Inertia::render('Payment/Create', [
            'students' => $students,
            'states' => StateLists::PAYMENT,
        ]);
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
                'state' => $validated['state'] ?? StateLists::PAYMENT['PAID'],
                'paid_at' => now(),
            ]);
        }

        if ($this->httpReferRouteName($request) == 'payments.create') {
            return redirect()->route('payments.index');
        }

        return redirect()->back();
    }

    public function update($id)
    {
        $students = Student::where('state', '!=', 'removed')->where('archived', false)->get();

        $students->map(function (Student $student) {
            $student->courses = $student->groups()->with('course.teacher')->get()->pluck('course')->toArray();
        });

        return Inertia::render('Payment/Create', [
            'students' => $students,
            'payment' => Payment::with('student', 'course')->findOrFail($id),
            'states' => StateLists::PAYMENT,
        ]);
    }

    public function edit(PaymentRequest $request)
    {

        $validated = $request->validated();

        $payment = Payment::findOrFail($validated['id']);

        $payment->student_id = $validated['student_id'];
        $payment->state = $validated['state'];

        if ($payment->course_id != $validated['course_id']) {
            $course = Course::findOrFail($validated['course_id']);
            $payment->course_id = $course->id;
            $payment->amount_payed = $course->price;
            $payment->teacher_part = $course->price * $course->teacher_percentage;

            if ($validated['state'] == StateLists::PAYMENT['PAID'] && $payment->stat != StateLists::PAYMENT['PAID']) {
                $payment->paid_at = now();
            }
        }

        $payment->save();

        if (str_contains($this->httpReferRouteName($request), 'payments.update')) {
            return redirect()->route('payments.index');
        }

        return redirect()->back();
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
