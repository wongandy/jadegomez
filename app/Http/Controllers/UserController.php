<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view users');
        
        $users = User::with('roles', 'branch')->orderBy('id', 'DESC')->get();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create users');
        $roles = Role::get();
        $branches = Branch::get();
        return view('user.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $this->authorize('create users');

        $this->validate($request, [
            'name' => 'required',
            'branch_id' => 'required',
            'email' => 'required|unique:users',
            'role_id' => 'required',
            'password' => 'required|confirmed'
        ]);

        User::create([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ])->roles()->attach($request->role_id);

        return redirect()->route('user.index')->with('message', 'Create user successful!');
    }

    public function edit(User $user)
    {
        $this->authorize('edit users');
        $roles = Role::get();
        $branches = Branch::get();
        return view('user.edit', compact('user', 'roles', 'branches'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('edit users');

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id . ',id',
            'branch_id' => 'required',
            'role_id' => 'required'
        ]);

        $inputs = [
            'name' => $request->name,
            'branch_id' => $request->branch_id,
            'email' => $request->email
        ];

        // if password field is not blank then its being updated
        if ($request->password) {
            $inputs['password'] = bcrypt($request->password);
        }

        $user->update($inputs);

        $user->roles()->sync($request->role_id);
        return redirect()->route('user.index')->with('message', 'Edit user successful!');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete users');
        $user->delete();
        return redirect()->route('user.index')->with('message', 'Deleted user!');
    }
}
