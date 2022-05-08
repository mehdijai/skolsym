<?php

namespace Database\Seeders;

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

        // $teacher = Teacher::create([
        //     'name' => 'Mehdi Jai',
        //     'email' => 'mehdi.jai.mj@gmail.com',
        //     'phone' => '0612113830',
        // ]);

        // $course = Course::create([
        //     'title' => 'Web development',
        //     'teacher_id' => $teacher->id,
        // ]);

        // $group = Group::create([
        //     'title' => 'GPA',
        //     'course_id' => $course->id,
        // ]);

        // $student = Student::create([
        //     'name' => 'Mehdi Jai',
        //     'email' => 'mehdi.jai.mj@gmail.com',
        //     'phone' => '0612113830',
        //     'age' => 23,
        //     'grade' => 'bac+3',
        // ]);

        // GroupStudent::create([
        //     'student_id' => $student->id,
        //     'group_id' => $group->id,
        // ]);
    }
}
