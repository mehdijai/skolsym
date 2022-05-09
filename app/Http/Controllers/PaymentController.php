<?php

namespace App\Http\Controllers;

use App\Const\StateLists;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        $allowed = ['course.teacher', 'student'];

        $query = Payment::query()->with('course.teacher', 'student');

        if (request('search')) {
            if (strpos(request('search'), ':') !== false) {
                $filter = explode(":", request('search'));
                if (in_array($filter[0], $allowed)) {
                    $query->whereRelation($filter[0], 'id', '=', $filter[1]);
                } else if ($filter[0] === 'payment') {
                    $query->where('id', $filter[1]);
                }
            } else {
            }
            $query->where(function ($query) {
                $query->whereRelation('course', 'title', 'LIKE', '%' . request('search') . '%')
                    ->orWhereRelation('course.teacher', 'name', 'LIKE', '%' . request('search') . '%')
                    ->orWhereRelation('student', 'name', 'LIKE', '%' . request('search') . '%')
                    ->orWhere('ref', 'LIKE', '%' . request('search') . '%')
                    ->orWhere('amount_payed', 'LIKE', '%' . request('search') . '%')
                    ->orWhere('paid_at', 'LIKE', '%' . request('search') . '%');
            });
        }

        if (in_array(request('filter'), StateLists::PAYMENT)) {
            $query->where('state', request('filter'));
        }

        if (request('filter') == 'archived') {
            $query->where('archived', true);
        }

        return Inertia::render('Payment/Show', [
            'payments' => $query->get(),
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

    }
}
