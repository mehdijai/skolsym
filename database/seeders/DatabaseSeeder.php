<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // todo: Create a teacher

        $teacher = Teacher::create([
            'name' => 'Mehdi Jai',
            'email' => 'mehdi.jai.mj@gmail.com',
            'phone' => '0612113830',
        ]);

        // todo: Create a course

        $course = Course::create([
            'title' => 'Web development',
            'teacher_id' => $teacher->id,
        ]);

        // todo: Create a group

        $group = Group::create([
            'title' => 'GPA',
            'course_id' => $course->id,
        ]);

        // todo: Create a student

        $student = Student::create([
            'name' => 'Mehdi Jai',
            'email' => 'mehdi.jai.mj@gmail.com',
            'phone' => '0612113830',
            'age' => 23,
            'grade' => 'bac+3',
        ]);

        // todo: Assign student to group

        // GroupStudent::create([
        //     'student_id' => $student->id,
        //     'group_id' => $group->id,
        // ]);
    }
}
