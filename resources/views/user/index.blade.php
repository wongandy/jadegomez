@extends('adminlte::page')

@section('plugins.Datatables', true)



@section('content')
<div class="row">
    <div class="col-12">
        @if (session('message'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fas fa-check"></i>{{ session('message') }}
            </div>
        @endif
        
        @can('create users')
        <a href="{{ route('user.create') }}" class="btn btn-primary">Create User</a><br><br>
        @endcan
       
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Users</h3>
            </div>
            <div class="card-body">
                <table id="users_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Branch</th>
                            @canany(['edit users', 'delete users'])
                                <th>Action</th>
                            @endcanany
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach ($user->roles as $role)
                                        <span>{{ $role->name }}</span><br>
                                    @endforeach
                                </td>
                                <td>{{ $user->branch->address }}</td>
                                @canany(['edit users', 'delete users'])
                                <td>
                                    @can('edit users')
                                        <a href="{{ route('user.edit', $user->id) }}" class="btn btn-info">Edit</a>
                                    @endcan
                
                                    {{-- @can('delete users')
                                        <form action="{{ route('user.destroy', $user->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to delete?')">Delete</button>
                                        </form> 
                                    @endcan --}}
                                </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No users yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
    $(document).ready(function() {
        $('#users_list').DataTable({
            "order": []
        });
    }); 
    </script>
@stop