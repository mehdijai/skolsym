<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $model = null;

    public function archive($id)
    {
        if ($this->model == null) {
            return redirect()->back();
        }

        $instance = $this->model::findOrFail($id);

        if ($instance->archived == true) {

            $instance->archived = false;
            $instance->archived_at = null;
        } else {
            $instance->archived = true;
            $instance->archived_at = new DateTime();
        }

        $instance->save();

        return redirect()->back();
    }
}
