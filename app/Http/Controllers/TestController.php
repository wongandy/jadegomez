<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        return view('test.index');
    }

    public function create()
    {
        return view('test.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'age' => 'required'
        ]);
            dd($request->all());
        return view('test.index');
    }
}
