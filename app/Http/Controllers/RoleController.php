<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('view roles');

        $roles = Role::orderBy('id', 'DESC')->get();
        return view('role.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('create roles');

        $permissions = Permission::get();
        return view('role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create roles');

        $this->validate($request, [
            'name' => 'required|unique:roles'
        ]);

        Role::create($request->only('name'))->permissions()->attach($request->permissions);
        return redirect()->route('role.index')->with('message', 'Create role successful!');
    }

    public function show($id)
    {
        //
    }

    public function edit(Role $role)
    {
        if ($role->id == 1) {
            abort(403);
        }

        $this->authorize('edit roles');

        $permissionsSelected = $role->permissions->pluck('id')->all();
        $permissions = Permission::get();
        return view('role.edit', compact('role', 'permissions', 'permissionsSelected'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('edit roles');

        $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $role->id . ',id'
        ]);
        
        $role->update($request->only('name'));
        $role->permissions()->sync($request->permissions);
        return redirect()->route('role.index')->with('message', 'Edit role successful!');
    }

    public function destroy(Role $role)
    {
        if ($role->id == 1) {
            abort(403);
        }
        
        $this->authorize('delete roles');

        $role->delete();
        return redirect()->back()->with('message', 'Deleted role!');
    }
}
