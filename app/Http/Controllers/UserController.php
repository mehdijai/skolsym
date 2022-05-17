<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct()
    {
        if (!Gate::allows('isAdmin')) {
            abort(403);
        }
    }
    public function index()
    {

    }
    public function create()
    {

    }
    public function store(Request $request)
    {

    }
    public function update()
    {

    }
    public function edit(Request $request)
    {

    }
    public function delete($id)
    {

    }
}
