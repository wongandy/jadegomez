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
                <h3 class="card-title">Create Item Defective</h3>
            </div>

            <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
                <div class="form-group row">
                    <label for="sale_number" class="col-sm-2 col-form-label">Sale Number</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control-plaintext" name="sale_number" value="{{ $sale->sale_number }}" tabindex='-1' readonly>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="sale_number" class="col-sm-2 col-form-label">Created At</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control-plaintext" name="sale_created_at" value="{{ $sale->created_at }}" tabindex='-1' readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="sale_number" class="col-sm-2 col-form-label">Created By</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control-plaintext" name="sale_created_by" value="{{ $sale->user->name }}" tabindex='-1' readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="sale_number" class="col-sm-2 col-form-label">Approved By</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control-plaintext" name="sale_approved_by" value="{{ $sale->approvedByUser->name }}" tabindex='-1' readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="customer_name" class="col-sm-2 col-form-label">Customer Name</label>
                    
                    <div class="col-sm-10">
                        <input type="text" class="form-control-plaintext" name="customer_name" id="customer_name" value="{{ $sale->customer->name }}" tabindex='-1' readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="contact_number" class="col-sm-2 col-form-label">Contact Number</label>
                    
                    <div class="col-sm-10">
                        <input type="text" class="form-control-plaintext" name="contact_number" id="contact_number" value="{{ $sale->customer->contact_number }}" tabindex='-1' readonly>
                    </div>
                </div>

                <div>
                    <table id="sales_table" class="table table-bordered table-sm table-hover">
                        <thead>
                            <tr>
                                <th class="w-25">Item</th>
                                <th>UPC</th>
                                <th class="w-25">Serial Number</th>
                                <th>Qty</th>
                                <th>Sold Price</th>
                                <th>Serial Number To Replace</th>
                                <th>Qty To Replace</th>
                            </tr>
                        </thead>
                    
                        <tbody>
                            @foreach($sale->item as $i)
                                <tr data-sale-item-id="{{ $i->id }}">
                                    <td>
                                        {{ $i->name }}
                                    </td>
                                    <td>
                                       {{ $i->upc }}
                                    </td>
                                    <td>
                                        @if ($i->with_serial_number)
                                            {{ $i->allSoldItems->implode('serial_number', ', ') }}   
                                        @endif
                                    </td>
                                    <td>
                                        {{ $i->quantity }}
                                    </td>
                                    <td>
                                        {{ number_format($i->sold_price, 2, '.', ',') }}
                                    <td>
                                        @if ($i->with_serial_number)
                                            <select class='form-control select-serial-number' style='width: 100%; min-width: 200px;' multiple='multiple'>
                                                @foreach ($i->remainingSoldItems as $remainingSoldItem)
                                                    <option data-item-id="{{ $i->id }}"
                                                            data-sale-id="{{ $sale->id }}"
                                                            data-name="{{ $i->name }}" 
                                                            data-upc="{{ $i->upc }}" 
                                                            data-sold-price="{{ $i->sold_price }}"
                                                            data-with-serial-number="{{ $i->with_serial_number }}" 
                                                            value="{{ $remainingSoldItem->item_purchase_id }}" 
                                                            selected disabled>{{ $remainingSoldItem->serial_number }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </td>
                                    <td>
                                        @if (! $i->with_serial_number)
                                            <form class="item-without-serial-numbers" novalidate>
                                                <input type="hidden" name="name" value="{{ $i->name }}">
                                                <input type="hidden" name="upc" value="{{ $i->upc }}">
                                                <input type="hidden" name="item_id" value="{{ $i->id }}">
                                                <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                                                <input type="hidden" name="item_purchase_ids" value="{{ $i->remainingSoldItems->implode('item_purchase_id', ', ') }}">
                                                <input type="hidden" name="sold_quantity" value="{{ $i->quantity }}">
                                                <input type="hidden" name="sold_price" value="{{ $i->sold_price }}">
                                                <input type="number" name="return_quantity" class="form-control quantity-to-replace" min="0" max="{{ $i->remainingSoldItems->count() }}" value="0" onKeyDown="if (event.key != 'ArrowUp' && event.key != 'ArrowDown' && event.key != 'Enter') return false;">
                                                <input type="submit" name="selected_quantity" hidden>
                                            </form>
                                        @else
                                            <input type="number" class="form-control-plaintext quantity-to-return-{{ $i->id }}" readonly>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <br>
                </div>
            </div>

            <div class="card-footer">
                <button id="proceed-button" class="btn btn-success" disabled>Click to proceed below</button>
            </div>
        </div>

        <div class="card" id="returned_items_table_wrapper" hidden>
            <div class="card-header">
                <h3 class="card-title">Defective Items</h3>
            </div>

            <form class="form-horizontal" id="create-defective-form" action="{{ route('defective.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div>
                        <table id="returned_items_table" class="table table-bordered table-sm table-hover">
                            <thead>
                                <tr>
                                    <th class="w-25">Item</th>
                                    <th>UPC</th>
                                    <th>Serial Number</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <br>
                    </div>

                    <div>
                        <hr>
                        <h5>Replace Item</h5>
                        <table id="replace_items_table" class="table table-bordered table-sm table-hover">
                            <thead>
                                <tr>
                                    <th class="w-25">Item</th>
                                    <th>UPC</th>
                                    <th>Serial Number</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" id="create_replacement_button" class="btn btn-success" disabled>Replace Item</button>
                </div>

                <input type="hidden" class="form-control" id="sale_id" name="sale_id" value="{{ $sale->id }}" tabindex='-1'>
            </form>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

    <script>
        $(document).ready(function () {
            let hasSerialNumbersYetToBeSelected = [];

            $('.select-serial-number').select2();

            $(document).on('select2:unselecting', '.select-serial-number', function (e) {
                hasSerialNumbersYetToBeSelected.push(1);

                if (hasSerialNumbersYetToBeSelected.length) {
                    $('#create_replacement_button').attr('disabled', true);
                }

                // prevent dropdown from opening when clicking x to deselect
                e.params.args.originalEvent.stopPropagation();
                let rowNumber = $('#returned_items_table tbody tr').length;
                let item = $(this).find(':selected');
                let serialNumbersRemoved = $(this).find('option').length - ($(this).select2('data').length - 1);
                let qty = "<input type='string' class='form-control-plaintext' tabindex='-1' value='" + serialNumbersRemoved + "'readonly>";
                $('.quantity-to-return-' + item.data('item-id')).val(serialNumbersRemoved);
                let data = e.params.args.data;
                let name = "<input type='string' class='form-control-plaintext' name='items[" + rowNumber + "][name]' value='" + item.data('name') + "' tabindex='-1' readonly>";
                let saleId = "<input type='hidden' class='form-control-plaintext' name='items[" + rowNumber + "][sale_id]' value='" + item.data('sale-id') + "' tabindex='-1' readonly>";
                let upc = "<input type='string' class='form-control-plaintext' name='items[" + rowNumber + "][upc]' value='" + item.data('upc') + "' tabindex='-1' readonly>";
                let itemId = "<input type='hidden' class='item_id' name='items[" + rowNumber + "][item_id]' value='" + item.data('item-id') + "'>";
                let soldPrice = "<input type='number' class='form-control-plaintext' name='items[" + rowNumber + "][sold_price]' value='" + item.data('sold-price') + "' readonly>";
                let amount = "<input type='text' class='form-control-plaintext' value='" + serialNumbersRemoved * item.data('sold-price') + "' readonly>";
                let withSerialNumber = "<input type='hidden' class='with_serial_number' name='items[" + rowNumber + "][with_serial_number]' value='" + item.data('with-serial-number') + "'>";
                let selectedSerialNumbers = "<select name='items[" + rowNumber + "][item_purchase_id][]' class='form-control serial-numbers' id='serial-number-" + item.data("item-id") + "' style='width: 100%; min-width: 200px;' multiple='multiple' disabled></select>";

                // if item already exist in the returned items table
                if ($('#' + item.data('item-id')).length) {
                    $('#serial-number-' + item.data('item-id')).append("<option value='" + data.id + "' selected>" + data.text + "</option>");
                    $('#qty-' + item.data('item-id')).html(qty);
                    $('#amount-' + item.data('item-id')).html(amount);
                }
                else {
                    $('#returned_items_table tbody').append('<tr data-item-id=' + item.data('item-id') + ' id=' + item.data('item-id') + '><td>' 
                                                            + itemId + saleId + name + withSerialNumber 
                                                      + '</td><td>' 
                                                            + upc
                                                      + '</td><td>' 
                                                            + selectedSerialNumbers                                                    
                                                      + '</td><td id="qty-' + item.data('item-id') + '">'
                                                      + '</td></tr>');
                    
                    $('#serial-number-' + item.data('item-id')).select2().append("<option value='" + data.id + "' selected>" + data.text + "</option>");
                    $('#qty-' + item.data('item-id')).html(qty);
                    $('#amount-' + item.data('item-id')).html(amount);
                }
                
                $.ajax({
                    type: 'GET',
                    url: '/getItemsWithSerialNumberForReplacement/' + item.data('item-id'),
                    success: function (data) {
                        let serialNumbers = '';
                        
                        if (! data || (data.remainingQuantity < serialNumbersRemoved)) {
                            $('.select-serial-number').prop('disabled', true);
                            $('.quantity-to-replace').prop('disabled', true);
                            $('#proceed-button').attr('disabled', true);
                            return alert('Not enough item on hand to replace the item with. Please add more and try again.');
                        }
                        else {
                            $('#proceed-button').attr('disabled', false);

                            if ($('#select2-' + data.id).length) {
                                $('#select2-' + data.id).select2({
                                    maximumSelectionLength: serialNumbersRemoved
                                });

                                $('#select2-' + data.id).attr('data-required-number-of-serial-numbers-to-select', serialNumbersRemoved);
                            }
                            else {
                                $.each(JSON.parse(data.serial_numbers), function (key, value) {
                                    serialNumbers += "<option value='" + key + "'>" + value + "</option>";
                                });

                                $('#replace_items_table tbody').append('<tr>' +
                                                                            '<td>' + 
                                                                                data.name + 
                                                                            '</td>' +
                                                                            '<td>' + 
                                                                                data.upc + 
                                                                            '</td>' +
                                                                            '<td>' + 
                                                                                '<select name="replacements[' + item.data('item-id') + '][item_purchase_id][]" id="select2-' + data.id + '" class="replacement-serial-numbers" data-required-number-of-serial-numbers-to-select="' + serialNumbersRemoved + '" multiple required>' + serialNumbers + '</select>' +
                                                                                '<input type="hidden" name="replacements[' + item.data('item-id') + '][item_id]" value="' + item.data('item-id') + '">' +
                                                                            '</td>' +
                                                                            '<td class="return-quantity"></td>' +
                                                                        '</tr>');

                                $('#select2-' + data.id).select2({
                                    maximumSelectionLength: serialNumbersRemoved
                                });

                                if (hasSerialNumbersYetToBeSelected.length) {
                                    
                                }
                            }
                        }
                    }
                });
            });

            $(document).on('select2:select', '.replacement-serial-numbers', function (e) {
                hasSerialNumbersYetToBeSelected.pop(1);
                $(this).closest("td").siblings(".return-quantity").text($(this).select2('data').length);

                if (! hasSerialNumbersYetToBeSelected.length) {
                    $('#create_replacement_button').attr('disabled', false);
                }
            });

            $(document).on('select2:unselect', '.replacement-serial-numbers', function (e) {
                hasSerialNumbersYetToBeSelected.push(1);
                $(this).closest("td").siblings(".return-quantity").text($(this).select2('data').length);

                if (hasSerialNumbersYetToBeSelected.length) {
                    $('#create_replacement_button').attr('disabled', true);
                }
            });


            $(document).on('submit', '.item-without-serial-numbers', function (e) {
                e.preventDefault();
                let returnQuantity = $("input[name='return_quantity']",this).val();
                let qty = "<input type='string' class='form-control-plaintext' tabindex='-1' value='" + returnQuantity + "'readonly>";

                if (returnQuantity == 0) {
                    alert("Please input quantity to return");
                    return false;
                }
                
                $('#proceed-button').attr('disabled', false);
                // $('#create_replacement_button').attr('disabled', false);
                let rowNumber = $('#returned_items_table tbody tr').length;
                $("input[name='return_quantity']",this).attr('disabled', true);
                let itemPurchaseIds = $("input[name='item_purchase_ids']",this).val().split(', ').splice(0, returnQuantity);     
                let selectedSerialNumbers = "<select name='items[" + rowNumber + "][item_purchase_id][]' class='form-control' id='serial-number-" + $("input[name='item_id']",this).val() + "' multiple hidden></select>";
                let name = "<input type='string' class='form-control-plaintext' name='items[" + rowNumber + "][name]' value='" + $("input[name='name']",this).val() + "' tabindex='-1' readonly>";
                let upc = "<input type='string' class='form-control-plaintext' name='items[" + rowNumber + "][upc]' value='" + $("input[name='upc']",this).val() + "' tabindex='-1' readonly>";
                let itemId = "<input type='hidden' class='item_id' name='items[" + rowNumber + "][item_id]' value='" + $("input[name='item_id']",this).val() + "'>";
                let saleId = "<input type='hidden' class='sale_id' name='items[" + rowNumber + "][sale_id]' value='" + $("input[name='sale_id']",this).val() + "'>";

                $('#returned_items_table tbody').append('<tr data-item-id="' + $("input[name='item_id']",this).val() + '" id=' + itemId + '><td>' 
                                                        + itemId + name + saleId + selectedSerialNumbers
                                                    + '</td><td>' 
                                                        + upc
                                                    + '</td><td>'
                                                    + '</td><td>'
                                                        + qty
                                                    + '</td></tr>');

                for (let i = 0; i < itemPurchaseIds.length; i++) {
                    $('#serial-number-' + $("input[name='item_id']",this).val()).append("<option value='" + itemPurchaseIds[i] + "' selected>" + itemPurchaseIds[i] + "</option>");
                }

                $.ajax({
                    type: 'GET',
                    url: '/getItemsWithOutSerialNumberForReplacement/' + $("input[name='item_id']",this).val() + '/' + returnQuantity,
                    success: function (item) {
                        let replacementsSelected = '';
                        replacementsSelected += '<input type="hidden" name="replacements[' + item.id + '][item_id]" value="' + item.id + '">';

                        $.each(item.purchases, function (key, item_purchase) {
                            replacementsSelected += '<input type="hidden" name="replacements[' + item.id + '][item_purchase_id][]" value="' + item_purchase.id + '">';
                        });

                        if (! item.purchases.length) {
                            $('.select-serial-number').prop('disabled', true);
                            $('.quantity-to-replace').prop('disabled', true);
                            $('#proceed-button').attr('disabled', true);
                            return alert('Not enough item on hand to replace the item with. Please add more and try again.');
                        }
                        else {
                            $('#proceed-button').attr('disabled', false);

                            if (! hasSerialNumbersYetToBeSelected.length) {
                                $('#create_replacement_button').attr('disabled', false);
                            }

                            $('#replace_items_table tbody').append('<tr>' +
                                                                        '<td>' + 
                                                                            item.name + 
                                                                        '</td>' +
                                                                        '<td>' + 
                                                                            item.upc + 
                                                                        '</td>' +
                                                                        '<td>' + 
                                                                        '</td>' +
                                                                        '<td>' + 
                                                                            returnQuantity +
                                                                        '</td>' +
                                                                    '</tr>');

                            $('#create-defective-form').append(replacementsSelected);
                        }
                    }
                });
            });

            $(document).on('click', '#proceed-button', function () {
                var returned_items_table = $('#returned_items_table_wrapper');
                returned_items_table.attr('hidden', false);

                $("#sales_table").find("input,button,textarea,select").attr("disabled", "disabled");

                $('html,body').animate({
                    scrollTop: returned_items_table.offset().top},
                    'slow');

                $(this).attr('disabled', true);
            });

            $(document).on('submit', '#create-defective-form', function (e) {
                if (confirm('Are you sure to create item replacement?')) {
                    $('.serial-numbers').attr('disabled', false);
                }
                else {
                    return false;
                }
            });
        });
    </script>
@stop