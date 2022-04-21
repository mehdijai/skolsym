<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use DateTime;

class CourseController extends Controller
{
    public function delete($id)
    {
        if (is_array($id)) {
            Group::whereIn('id', $id)->update([
                'archived' => true,
                'archived_at' => new DateTime(),
            ]);
            Course::whereIn('id', $id)->update([
                'archived' => true,
                'archived_at' => new DateTime(),
            ]);
        } else {
            Group::where('id', $id)->update([
                'archived' => true,
                'archived_at' => new DateTime(),
            ]);
            Course::find($id)->update([
                'archived' => true,
                'archived_at' => new DateTime(),
            ]);
        }
    }
}
