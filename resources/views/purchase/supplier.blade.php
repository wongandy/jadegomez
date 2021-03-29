@extends('adminlte::page')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Select Supplier</h3>
            </div>

            <form class="form-horizontal" action="{{ route('purchase.supplier') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="form-group row">
                        <label for="with_serial_number" class="col-sm-2 col-form-label">Select Supplier</label>
                        
                        <div class="col-sm-10">
                            <select class="form-control" name="supplier_id" required>
                                <option value="" selected disabled>Please select</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>

                            @error('supplier_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Select Supplier</button>
                    <a href="{{ route('purchase.index') }}" class="btn btn-default float-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop