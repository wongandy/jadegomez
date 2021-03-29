@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Item</h3>
            </div>

            <form class="form-horizontal" action="{{ route('item.update', $item->id) }}" method="POST">
                @csrf
                @method("PUT")
                
                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="{{ $item->name }}">
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="details" class="col-sm-2 col-form-label">Details</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="details" value="{{ $item->details }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="upc" class="col-sm-2 col-form-label">UPC</label>

                        <div class="col-sm-10">
                            <input type="upc" class="form-control" name="upc" value="{{ $item->upc }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="selling_price" class="col-sm-2 col-form-label">Selling Price</label>

                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="selling_price" value="{{ $item->selling_price }}">
                            @error('selling_price')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row" @if ($item->purchases->count()) hidden @endif>
                        <label for="with_serial_number" class="col-sm-2 col-form-label">With Serial Number</label>
                        
                        <div class="col-sm-10">
                            <select class="form-control" name="with_serial_number">
                                <option value="0" {{ $item->with_serial_number === 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $item->with_serial_number === 1 ? 'selected' : '' }}>Yes</option>
                            </select>

                            @error('with_serial_number')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-info">Edit Item</button>
                    <a href="{{ route('item.index') }}" class="btn btn-default float-right">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection