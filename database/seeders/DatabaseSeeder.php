<?php

namespace Database\Seeders;

use App\Const\StateLists;
use App\Models\Course;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        User::create([
            'name' => "Mehdi Jai",
            'email' => "mehdi.jai.mj@gmail.com",
            'email_verified_at' => now(),
            'password' => Hash::make("123456789"),
            'remember_token' => Str::random(10),
        ]);

        $teacher = Teacher::create([
            'name' => 'Mehdi Jai',
            'email' => 'mehdi.jai.mj@gmail.com',
            'phone' => '0612113830',
        ]);

        $course = Course::create([
            'title' => 'Web development',
            'teacher_id' => $teacher->id,
            'teacher_percentage' => 0.3,
            'price' => 500,
        ]);

        $group = Group::create([
            'title' => 'WD_GPA',
            'course_id' => $course->id,
        ]);

        $student = Student::create([
            'name' => 'Mehdi Jai',
            'email' => 'mehdi.jai.mj@gmail.com',
            'phone' => '0612113830',
            'age' => 23,
            'grade' => 'bac+3',
        ]);

        $student->groups()->attach($group->id);

        $payment = Payment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'amount_payed' => $course->price,
            'state' => StateLists::PAYMENT['PAID'],
            'paid_at' => now(),
        ]);
    }
}
