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
                <h3 class="card-title">Create Transfer</h3>
            </div>

            <form class="form-horizontal" id="create_transfer_form" action="{{ route('transfer.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="form-group row">
                        <label for="transfer_number" class="col-sm-2 col-form-label">Delivery Receipt Number</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control-plaintext" name="transfer_number" value="{{ $transfer_number }}" tabindex='-1' readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="branch" class="col-sm-2 col-form-label">Transfer To Branch</label>

                        <div class="col-sm-10">
                            <select name="to_branch_id" class="form-control" id="branch_id" required>
                                <option value="" selected disabled>Please select</option>

                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->address }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="notes" class="col-sm-2 col-form-label">Notes</label>

                        <div class="col-sm-10">
                            <textarea name="notes" id="notes" rows="10" style="width: 100%; max-width: 100%;"></textarea>
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
                                        data-selling-price="{{ $item->selling_price }}"
                                        data-on-hand="{{ $item->on_hand }}"
                                        data-serial-numbers="{{ $item->serial_numbers}}"
                                        value="{{ $item->id == old('item_id') ? old('item_id') : $item->id }}" 
                                        {{ $item->id == old('item_id') ? 'selected' : '' }} 
                                    >{{ $item->name }}
                                    </option>
                                @endforeach

                                {{-- @foreach ($items as $item)
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
                                @endforeach --}}
                            </select>
                            @error('search_item')
                                <div>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <table id="transfers_table" class="table table-bordered table-sm table-hover" hidden>
                        <thead>
                            <tr>
                                <th class="w-25">Item</th>
                                <th>UPC</th>
                                <th>On Hand</th>
                                <th class="w-25">Serial Number</th>
                                <th>Selling Price</th>
                                <th>Qty</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <button type="submit" id="create_transfer_button" class="btn btn-success" disabled>Create Transfer</button>
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
            rowNumber = $('#transfers_table tbody tr').length;
            item = $(this).find(':selected');
            let selectSerialNumbers = "";

            // check if item has already been selected to avoid duplicate selection of item
            if (! itemsSelected.includes(item.data('id'))) {
                itemsSelected.push(item.data('id'));

                $.each(item.data('serial-numbers'), function (key, value) {
                    selectSerialNumbers += "<option>" + value + "</option>";
                });
                
                let name = "<input type='string' class='form-control-plaintext name' name='items[" + rowNumber + "][name]' value='" + item.data('name') + "' tabindex='-1' readonly>";
                let upc = "<input type='string' class='form-control-plaintext' value='" + item.data('upc') + "' tabindex='-1' readonly>";;
                let onHand = "<input type='number' class='form-control-plaintext on_hand' name='items[" + rowNumber + "][on_hand]' value='" + item.data('on-hand') + "' tabindex='-1' readonly>";
                let id = "<input type='hidden' class='item_id' name='items[" + rowNumber + "][item_id]' value='" + item.data('id') + "'>";
                let withSerialNumber = "<input type='hidden' class='with_serial_number' name='items[" + rowNumber + "][with_serial_number]' value='" + item.data('with-serial-number') + "'>";
                let serialNumber = (item.data('with-serial-number')) ? "<select name='items[" + rowNumber + "][serial_number][]' class='form-control serial_number input_serial_numbers' style='width: 100%; min-width: 200px;' multiple='multiple' required>" + selectSerialNumbers + "</select>" : "<input type='hidden' class='serial_number' name='items[" + rowNumber + "][serial_number][]'>";
                let quantity = (item.data('with-serial-number')) ? "<input type='number' class='form-control-plaintext quantity' name='items[" + rowNumber + "][quantity]' tabindex='-1' readonly>" : "<input type='number' class='form-control quantity' name='items[" + rowNumber + "][quantity]' min='1' max='" + item.data('on-hand') + "' required>";
                let costPrice = "<input type='number' class='form-control-plaintext cost_price' name='items[" + rowNumber + "][cost_price]' value='" + item.data('cost-price') + "' tabindex='-1' readonly>";
                let sellingPrice = "<input type='number' class='form-control-plaintext selling_price' name='items[" + rowNumber + "][selling_price]' value='" + item.data('selling-price') + "' tabindex='-1' readonly>";
                let amount = "<input type='number' class='form-control-plaintext amount' name='items[" + rowNumber + "][amount]' tabindex='-1' readonly>";
                let removeButton = "<button type='button' class='btn btn-default remove_item' tabindex='-1'><i class='fas fa-fw fa-times'></i></button>";
                console.log(name);
                $('#transfers_table tbody').append('<tr id="' + rowNumber + '"><td>' + id + withSerialNumber + name + '</td><td>' + upc + '</td><td>' + onHand + '</td><td>' + serialNumber + '</td><td>' + sellingPrice + '</td><td>' + quantity + '</td><td>' + removeButton + '</td></tr>');

                $('.input_serial_numbers').select2({
                    language:{
                        "noResults" : function () { 
                            return '';
                        }
                    }
                });

                if ($('#transfers_table tbody tr').length > 0) {
                    $('#transfers_table').attr('hidden', false);
                    $('#create_transfer_button').attr('disabled', false);
                }
                else {
                    $('#transfers_table').attr('hidden', true);
                    $('#create_transfer_button').attr('disabled', true);
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

        $('#transfers_table').on('click', '.remove_item', function(e){
            let totalAmount = 0;
            let rowNumber = $(this).closest('tr').attr('id');
            itemsSelected.splice(rowNumber, 1);
            $(this).closest('tr').remove();

            $('#transfers_table tbody tr').each(function(i) {
                $(this).attr('id', i);
                $(this).find('.serial_number').attr('name', 'items[' + i + '][serial_number][]');
                $(this).find('.quantity').attr('name', 'items[' + i + '][quantity]');
                $(this).find('.name').attr('name', 'items[' + i + '][name]');
                $(this).find('.on_hand').attr('name', 'items[' + i + '][on_hand]');
                $(this).find('.with_serial_number').attr('name', 'items[' + i + '][with_serial_number]');
                $(this).find('.item_id').attr('name', 'items[' + i + '][item_id]');
                $(this).find('.selling_price').attr('name', 'items[' + i + '][selling_price]');
            });

            if ($('#transfers_table tbody tr').length > 0) {
                $('#transfers_table').attr('hidden', false);
                $('#create_transfer_button').attr('disabled', false);
            }
            else {
                $('#transfers_table').attr('hidden', true);
                $('#create_transfer_button').attr('disabled', true);
            }
        });

        $(document).on('keyup', '.quantity', function() {
            // alert();
            // let totalAmount = 0;
            let rowNumber = $(this).closest('tr').attr('id');
            let onHand = $('input[name="items[' + rowNumber + '][on_hand]"]').val();
            let quantity = $('input[name="items[' + rowNumber + '][quantity]"]').val();
            // let sellingPrice = $('input[name="items[' + rowNumber + '][selling_price]"]').val();
            // let amount = quantity * sellingPrice;
            // let a = 50;

            if (parseInt(quantity) > parseInt(onHand)) {
                alert("Quantity must not be more than on hand quantity");
                $('input[name="items[' + rowNumber + '][quantity]"]').val(quantity.slice(0, -1));
                return false;
            }

            // $('input[name="items[' + rowNumber + '][amount]"]').val(amount);

            // $('#transfers_table tbody tr').each(function() {
            //     if ($(this).find('.amount').val() == '') {
            //         $(this).find('.amount').val(0);
            //     }

            //     totalAmount += parseFloat($(this).find('.amount').val());
            // });

            // $('#gross_total').val(totalAmount);
            // let netTotal = $('#gross_total').val() - $('#discount').val();
            // $('#net_total').val(netTotal);
        });

        $(document).on('submit', '#create_transfer_form', function (e) {
            e.preventDefault();
            
            if (confirm('Are you sure to create transfer?')) {
                $('#create_transfer_button').attr('disabled', true);

                $.ajax({
                    type: "POST",
                    url: "{{ route('transfer.store') }}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: $(this).serialize(),
                    success: function () {
                        location.href = "{{ route('transfer.index') }}";
                    },
                    error: function(xhr, status, error) {
                        $('#errors').html('');
                        $('#errors').html("<p class='text-danger'>Errors found:</p>");
                        $.each(xhr.responseJSON.errors, function (key, item) {
                            $("#errors").append("<li class='text-danger'>"+item+"</li>");
                            $('#create_transfer_button').attr('disabled', false);
                        });
                    }
                });
            }
            else {
                return false;
            }
        });
    });
    </script>
@stop