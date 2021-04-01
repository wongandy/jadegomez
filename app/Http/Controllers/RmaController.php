<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RmaController extends Controller
{
    public function create()
    {
        return view('rma.create');
    }

    public function track()
    {
        return view('rma.create');
    }
}
