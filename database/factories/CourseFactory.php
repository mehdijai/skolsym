<?php

namespace Database\Factories;

use App\Const\SkolFaker;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    use SkolFaker;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->subject(),
            'teacher_percentage' => rand(0, 10) / 10,
            'price' => rand(300, 800),
        ];
    }
}
