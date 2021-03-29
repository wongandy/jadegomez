<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index()
    {
        $this->authorize('view branches');
        $branches = Branch::orderBy('id','DESC')->get();
        return view('branch.index', compact('branches'));
    }

    public function create()
    {
        $this->authorize('create branches');

        return view('branch.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create branches');

        $this->validate($request, [
            'name' => 'required',
            'address' => 'required'
        ]);
        
        Branch::create($request->only('name', 'address', 'contact_number'));
        return redirect()->route('branch.index')->with('message', 'Created branch!');
    }

    public function show(Branch $branch)
    {
        //
    }

    public function edit(Branch $branch)
    {
        $this->authorize('edit branches');

        return view('branch.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $this->authorize('edit branches');

        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'contact_number' => 'required'
        ]);
        
        $branch->update($request->only('name', 'address', 'contact_number'));
        return redirect()->route('branch.index')->with('message', 'Updated branch!');
    }

    public function destroy(Branch $branch)
    {
        $this->authorize('delete branches');

        $branch->delete();
        return redirect()->back()->with('message', 'Deleted branch!');
    }
}
