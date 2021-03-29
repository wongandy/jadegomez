@extends('adminlte::page')
@section('plugins.Momentjs', true)
@section('plugins.Daterangepicker', true)
@section('plugins.Select2', true)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Test</h3>
            </div>

            <form class="form-horizontal" action="{{ route('test.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="form-group row here">
                        <label for="name" class="col-sm-2 col-form-label">Name:</label>

                        <div class="col-sm-10">
                            <input type="text" name="name" class="form-control" id="name">
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- <div class="form-group row"> --}}
                        @if (old('age'))
                            @foreach (old('age') as $key => $val)
                                <div class="form-group row">
                                    <label for="age" class="col-sm-2 col-form-label">Age:</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="age[]" class="form-control" id="age" value="{{ $val }}">
                                    </div>
                                </div>
                            @endforeach
                            {{ var_dump(old('age')) }}
                        @endif
                    {{-- </div> --}}
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Create Test</button>
                    <button type="button" class="btn btn-success" id="add">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
    $(document).ready(function() {
        $('#add').on('click', function () {
            $('.here').append('<div class="form-group row"><label for="age" class="col-sm-2 col-form-label">Age:</label><div class="col-sm-10"><input type="text" name="age[]" class="form-control" id="age"></div></div>');
        });
    }); 
    </script>
@stop