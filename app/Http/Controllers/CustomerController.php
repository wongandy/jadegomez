<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function dataAjax(Request $request)
    {
        $data = [];

        if($request->q) {
            $search = $request->q;
            $data = Customer::select("id","name","contact_number")
            		->where('name','LIKE',"%$search%")
                    ->orWhere('contact_number','LIKE',"%$search%")
            		->get();
        }
        
        return response()->json($data);
    }
}
