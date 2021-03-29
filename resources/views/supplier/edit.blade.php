@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Supplier</h3>
            </div>

            <form class="form-horizontal" action="{{ route('supplier.update', $supplier->id) }}" method="POST">
                @csrf
                @method("PUT")

                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="{{ $supplier->name }}">
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="contact_number" class="col-sm-2 col-form-label">Contact Number</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="contact_number" value="{{ $supplier->contact_number }}">
                            @error('contact_number')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Edit Supplier</button>
                    <a href="{{ route('supplier.index') }}" class="btn btn-default float-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection