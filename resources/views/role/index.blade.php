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

        <a href="{{ route('role.create') }}" class="btn btn-primary">Create Role</a><br><br>
       
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Roles</h3>
            </div>
            <div class="card-body">
                <table id="roles_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Role</th>
                            @canany(['edit roles', 'delete roles'])
                                <th>Action</th>
                            @endcanany
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td> 
                                    @if ($role->id != 1)
                                        @can('edit roles')
                                            <a href="{{ route('role.edit', $role->id) }}" class="btn btn-info">Edit</a>
                                        @endcan
                                        {{-- @can('delete roles')
                                            <form action="{{ route('role.destroy', $role->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method("DELETE")
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to delete?')">Delete</button>
                                            </form>
                                        @endcan --}}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No roles yet</td>
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
        $('#roles_list').DataTable({
            "order": []
        });
    }); 
    </script>
@stop