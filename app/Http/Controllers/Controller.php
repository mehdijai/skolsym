<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

    public function destroy(Request $request)
    {
        if ($this->model == null) {
            return redirect()->back();
        }

        $validated = Validator::make($request->all(), [
            'id' => ['required', Rule::exists($this->model->getTable(), 'id')],
        ])->validate();

        $this->model::findOrFail($validated['id'])->delete();

        return redirect()->back();
    }

    public function httpReferRouteName($request)
    {
        $parts = explode('/', $request->server('HTTP_REFERER'));
        $parts = array_slice($parts, 3);
        
        if (count($parts) < 2) {
            return null;
        }

        return join('.', $parts);
    }
}
