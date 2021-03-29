@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('plugins.Select2', true)

@section('css')
<style>

input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
</style>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Purchase</h3>
            </div>

            <form class="form-horizontal" id="create_purchase_form" action="{{ route('purchase.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="form-group row">
                        <label for="purchase_number" class="col-sm-2 col-form-label">Purchase Number</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control-plaintext" name="purchase_number" value="{{ $purchase_number }}" tabindex='-1' readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="supplier" class="col-sm-2 col-form-label">Supplier</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control-plaintext" name="supplier" value="{{ $supplier->name }}" tabindex='-1' readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="search_item" class="col-sm-2 col-form-label">Select Item</label>
                        
                        <div class="col-sm-10">
                            <select id="search_item" class="form-control select2" name="search_item" style="width: 100%;">
                                <option></option>
                                @foreach ($items as $item)
                                    <option data-id="{{ $item->id }}" 
                                        data-name="{{ $item->name }}" 
                                        data-upc="{{ $item->upc }}" 
                                        data-with-serial-number="{{ $item->with_serial_number }}" 
                                        data-price="{{ $item->price }}"
                                        @if ($item->purchases->where('supplier_id', $supplier->id)->first()) 
                                            data-cost-price="{{ $item->purchases->where('supplier_id', $supplier->id)->last()->pivot->cost_price }}" 
                                        @endif
                                        value="{{ $item->id == old('item_id') ? old('item_id') : $item->id }}" 
                                        {{ $item->id == old('item_id') ? 'selected' : '' }} 
                                    >{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('search_item')
                                <div>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <ul id="errors"></ul>

                    <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">

                    <table id="purchases_table" class="table table-bordered" hidden>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>UPC</th>
                                <th>Serial Number</th>
                                <th>Qty</th>
                                <th>Cost Price</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <button type="submit" id="create_purchase_button" class="btn btn-success" disabled>Create Purchase</button>
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
        let item;
        let itemsSelected = [];
        let rowNumber;

        $('#search_item').select2({
            placeholder: "Select an item"
        });
        
        $('#search_item').on('select2:select', function(e) {
            rowNumber = $('#purchases_table tbody tr').length;
            item = $(this).find(':selected');
            
            // check if item has already been selected to avoid duplicate selection of item
            if (! itemsSelected.includes(item.data('id'))) {
                itemsSelected.push(item.data('id'));
                
                let name = item.data('name');
                let upc = item.data('upc');
                let id = "<input type='hidden' class='item_id' name='items[" + rowNumber + "][item_id]' value='" + item.data('id') + "'>";
                let withSerialNumber = "<input type='hidden' class='with_serial_number' name='items[" + rowNumber + "][with_serial_number]' value='" + item.data('with-serial-number') + "'>";
                let serialNumber = (item.data('with-serial-number')) ? "<select name='items[" + rowNumber + "][serial_number][]' class='form-control serial_number input_serial_numbers' style='width: 100%; min-width: 200px;' multiple required></select>" : "";

                // let quantity = (item.data('with-serial-number')) ? "<input type='number' class='form-control-plaintext quantity' name='items[" + rowNumber + "][quantity]' tabindex='-1' readonly>" : "<input type='number' class='form-control quantity' name='items[" + rowNumber + "][quantity]' min='1' required>";
                let quantity = (item.data('with-serial-number')) ? "<input type='number' class='form-control-plaintext quantity' name='items[" + rowNumber + "][quantity]' tabindex='-1' readonly>" : "<input type='number' class='form-control quantity' name='items[" + rowNumber + "][quantity]' min='1' required>";
                // let costPrice = "<input type='number' value='" + $(e.params.data.element).data('cost-price') + "' step=0.25 min=0>";
                let costPrice = "<input type='number' class='form-control cost_price' name='items[" + rowNumber + "][cost_price]' value='" + item.data('cost-price') + "' required step=0.25 min=0>";
                let removeButton = "<button type='button' class='btn btn-default remove_item' tabindex='-1'><i class='fas fa-fw fa-times'></i></button>";
                
                $('#purchases_table tbody').append('<tr id="' + rowNumber + '"><td>' + id + withSerialNumber + name + '</td><td>' + upc + '</td><td>' + serialNumber + '</td><td>' + quantity + '</td><td>' + costPrice + '</td><td>' + removeButton + '</td></tr>');
            
                $('.input_serial_numbers').select2({
                    tags: true,
                    language:{
                        "noResults" : function () { 
                            return '';
                        }
                    }
                });

                if ($('#purchases_table tbody tr').length > 0) {
                    $('#purchases_table').attr('hidden', false);
                    $('#create_purchase_button').attr('disabled', false);
                }
                else {
                    $('#purchases_table').attr('hidden', true);
                    $('#create_purchase_button').attr('disabled', true);
                }
            }
            else {
                alert('Item already selected');
            }
        });

        $(document).on('select2:select select2:unselect', '.serial_number', function() {
            let totalSerialNumbers = $(this).select2('data').length;
            let rowNumber = $(this).closest('tr').attr('id');
            $('input[name="items[' + rowNumber + '][quantity]"]').val(totalSerialNumbers);
        });

        $('#purchases_table').on('click', '.remove_item', function(e){
            let rowNumber = $(this).closest('tr').attr('id');
            itemsSelected.splice(rowNumber, 1);
            $(this).closest('tr').remove();

            $('#purchases_table tbody tr').each(function(i) {
                $(this).attr('id', i);
                $(this).find('.serial_number').attr('name', 'items[' + i + '][serial_number][]');
                $(this).find('.quantity').attr('name', 'items[' + i + '][quantity]');
                $(this).find('.cost_price').attr('name', 'items[' + i + '][cost_price]');
                $(this).find('.with_serial_number').attr('name', 'items[' + i + '][with_serial_number]');
                $(this).find('.item_id').attr('name', 'items[' + i + '][item_id]');
                // $(this).find('.total-cost-price').attr('name', 'item[' + i + '][total-cost-price]');
            });

            if ($('#purchases_table tbody tr').length > 0) {
                $('#purchases_table').attr('hidden', false);
                $('#create_purchase_button').attr('disabled', false);
            }
            else {
                $('#purchases_table').attr('hidden', true);
                $('#create_purchase_button').attr('disabled', true);
            }
        });

        $(document).on('submit', '#create_purchase_form', function (e) {
            e.preventDefault();
            
            if (confirm('Are you sure to create purchase?')) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('purchase.store') }}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: $(this).serialize(),
                    success: function () {
                        location.href = "{{ route('purchase.index') }}";
                    },
                    error: function(xhr, status, error) {
                        $('#errors').html('');
                        $('#errors').html("<p class='text-danger'>Errors found:</p>");
                        $.each(xhr.responseJSON.errors, function (key, item) {
                            $("#errors").append("<li class='text-danger'>"+item+"</li>");
                        });
                    }
                });
            }
        });
    });
    </script>
@stop