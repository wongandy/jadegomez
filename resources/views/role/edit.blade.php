@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Role</h3>
            </div>

            <form class="form-horizontal" action="{{ route('role.update', $role->id) }}" method="POST">
                @csrf
                @method("PUT")

                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="{{ $role->name }}">

                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr>
                    <h3 class="card-title">Select Permissions</h3>
                    <br>
                    <label><input type="checkbox" id="select_all"> select/deselect all</label>
                    <br>
                    <br>
                    @foreach ($permissions as $permission)
                        <label><input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @if (is_array($permissionsSelected) && in_array($permission->id, $permissionsSelected))) checked @endif> {{ $permission->name }}</label>
                        
                        <br>
                    @endforeach
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Edit Role</button>
                    <a href="{{ route('role.index') }}" class="btn btn-default float-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
    $(document).ready(function() {
        $('#select_all').on('click', function () {
            $('input[type=checkbox]').prop('checked', $(this).prop('checked'));
        });
    });
    </script>
@stop