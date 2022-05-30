<?php

namespace Database\Seeders;

use App\Const\SkolFaker;
use App\Const\StateLists;
use App\Models\Course;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use SkolFaker;
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->createAuthUsers();
        $teachers = Teacher::factory(2)->create();
        $students = Student::factory(5)->create();
        $courses = [];
        $groups = [];

        foreach ($teachers as $teacher) {
            $courses = array_merge($courses, [...Course::factory(2)->create([
                'teacher_id' => $teacher->id,
            ])->toArray()]);
        }

        foreach ($courses as $course) {
            $groups = array_merge($groups, [...Group::factory(1)->create([
                'title' => $this->group($course['title']),
                'course_id' => $course['id'],
            ])]);
        }
        foreach ($groups as $group) {
            $group->students()->attach(collect($students)->random(3)->map(fn($student) => $student->id));
        }

    }

    public function generatePayments()
    {
        $students = Student::all();

        foreach ($students as $student) {

            $courses = $student->groups()->with('course')->get()->pluck('course')->toArray();

            $key = array_rand($courses);
            $course = $courses[$key];

            Payment::create([
                'student_id' => $student->id,
                'course_id' => $course['id'],
                'amount_payed' => $course['price'],
                'teacher_part' => $course['price'] * $course['teacher_percentage'],
                'state' => StateLists::PAYMENT['PENDING'],
                'paid_at' => now(),
            ]);
        }
    }

    public function createAuthUsers()
    {
        Role::create([
            'id' => 1,
            'title' => 'super admin',
        ]);

        Role::create([
            'id' => 2,
            'title' => 'moderator',
        ]);

        User::create([
            'name' => "Mehdi Jai",
            'email' => "mehdi.jai.mj@gmail.com",
            'email_verified_at' => now(),
            'password' => Hash::make("123456789"),
            'remember_token' => Str::random(10),
            'role_id' => 1,
        ]);

        User::create([
            'name' => "Karim",
            'email' => "karim@gmail.com",
            'email_verified_at' => now(),
            'password' => Hash::make("123456789"),
            'remember_token' => Str::random(10),
            'role_id' => 2,
        ]);
    }
}
