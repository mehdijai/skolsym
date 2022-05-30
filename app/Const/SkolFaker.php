<?php
namespace App\Const;

trait SkolFaker
{

    private $subjects = [
        'Mathematics',
        'Algebra',
        'Geometry',
        'Science',
        'Geography',
        'History',
        'English',
        'Spanish',
        'German',
        'French',
        'Latin',
        'Greek',
        'Arabic',
        'Computer Science',
        'Art',
        'Economics',
        'Music',
        'Drama',
        'Physical Education',
    ];

    public function subject()
    {
        $key = array_rand($this->subjects);
        return $this->subjects[$key];
    }

    public function group($course)
    {
        $title = explode(' ', $course);
        $prefix = join('', array_map(fn($chunk) => $chunk[0], $title));

        return strtoupper($prefix . '_' . $this->generateRandomString());
    }

    public function generateRandomString($length = 3)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
