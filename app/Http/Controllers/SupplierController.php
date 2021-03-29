<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index()
    {
        $this->authorize('view suppliers');

        $suppliers = Supplier::orderBy('id', 'DESC')->get();
        return view('supplier.index', compact('suppliers'));
    }

    public function create()
    {
        $this->authorize('create suppliers');

        return view('supplier.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create suppliers');

        $this->validate($request, [
            'name' => 'required|unique:suppliers'
        ]);
        
        Supplier::create($request->only('name', 'contact_number'));
        return redirect()->route('supplier.index')->with('message', 'Create supplier successful!');
    }

    public function show(Supplier $supplier)
    {
        //
    }

    public function edit(Supplier $supplier)
    {
        $this->authorize('edit suppliers');

        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorize('edit suppliers');

        $this->validate($request, [
            'name' => [
                'required',
                Rule::unique('suppliers')->ignore($supplier->id)
            ]
        ]);
        
        $supplier->update($request->only('name', 'contact_number'));
        return redirect()->route('supplier.index')->with('message', 'Update supplier successful!');
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorize('delete suppliers');
        
        $supplier->delete();
        return redirect()->back()->with('message', 'Deleted supplier!');
    }
}
