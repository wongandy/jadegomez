@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-12">
        @if (session('message'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fas fa-check"></i>{{ session('message') }}
            </div>
        @endif
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Item</h3>
            </div>

            <form class="form-horizontal" id="create_item_form" action="{{ route('item.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>

                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="details" class="col-sm-2 col-form-label">Details</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="details" value="{{ old('details') }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="upc" class="col-sm-2 col-form-label">UPC</label>

                        <div class="col-sm-10">
                            <input type="upc" class="form-control" id="upc" name="upc" value="{{ old('upc') }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="selling_price" class="col-sm-2 col-form-label">Selling Price</label>

                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="selling_price" value="{{ old('selling_price') }}" step=".01">
                            @error('selling_price')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="with_serial_number" class="col-sm-2 col-form-label">With Serial Number</label>
                        
                        <div class="col-sm-10">
                            <select class="form-control" name="with_serial_number">
                                <option value="0" {{ old('with_serial_number') == "0" ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('with_serial_number') == "1" ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('with_serial_number')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Create Item</button>
                    <a href="{{ url()->previous() }}" class="btn btn-default float-right">Go Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>   
    $(document).ready(function() {
        $('#upc').keypress(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
            }
        });

        $("#create_item_form").on('submit', function(){
                // if ($('#status').val() == 'paid' && parseFloat($('#cash_tendered').val()) < parseFloat($('#net_total').val())) {
                //     alert('Amount is not fully paid');
                //     return false;
                // }
                // else {
                //     return confirm('Are you sure to approve sale?');
                // }
                return confirm('Are you sure to create item?');
            });
    });
    </script>
@stop