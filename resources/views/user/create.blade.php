@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create User</h3>
            </div>
            
            <form class="form-horizontal" action="{{ route('user.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-sm-2 col-form-label">Email</label>

                        <div class="col-sm-10">
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="role_id" class="col-sm-2 col-form-label">Role</label>
                        
                        <div class="col-sm-10">
                            <select class="form-control" name="role_id">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id == old('role_id') ? old('role_id') : $role->id }}" {{ $role->id == old('role_id') ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>

                            @error('role_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="branch_id" class="col-sm-2 col-form-label">Branch</label>
                        
                        <div class="col-sm-10">
                            <select class="form-control" name="branch_id">
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id == old('branch_id') ? old('branch_id') : $branch->id }}" {{ $branch->id == old('branch_id') ? 'selected' : '' }}>{{ $branch->address }}</option>
                                @endforeach
                            </select>
                            
                            @error('branch_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-sm-2 col-form-label">Password</label>

                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="password" value="{{ old('password') }}">
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password_confirmation" class="col-sm-2 col-form-label">Confirm Password</label>

                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="password_confirmation" value="{{ old('password_confirmation') }}">
                            @error('password_confirmation')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Create User</button>
                    <a href="{{ url()->previous() }}" class="btn btn-default float-right">Go Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection