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
                <h3 class="card-title">Create Item Change</h3>
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
                <h3 class="card-title">Change Items</h3>
            </div>

            <form class="form-horizontal" id="create-defective-form" action="{{ route('change.store') }}" method="POST">
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
                                    <th>Sold Price</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        <div class="col-md-4 col-xs-12 float-right" id="calculation">
                            <div class="form-group row">
                                <label for="refund_total" class="col-sm-4 col-form-label">Return Credit</label>
                                
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="refund_total" name="refund_total" tabindex='-1' readonly autocomplete="off">
                                    <input type="hidden" class="form-control" id="sale_id" name="sale_id" value="{{ $sale->id }}" tabindex='-1'>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                    <br>
                    <div>
                        <hr>
                        <h5>Change Item To</h5>
                        <div class="form-group row">
                        <label for="search_item" class="col-sm-2 col-form-label">Select Item</label>

                        <div class="col-sm-10">
                            <select id="search_item" name="search_item" class="form-control" style="width: 100%;">
                                <option></option>
                                @foreach ($items as $item)
                                    <option data-id="{{ $item->id }}" 
                                        data-name="{{ $item->name }}" 
                                        data-upc="{{ $item->upc }}" 
                                        data-with-serial-number="{{ $item->with_serial_number }}"
                                        data-selling-price="{{ $item->selling_price }}"
                                        @if ($item->on_hand) 
                                            data-on-hand="{{ $item->on_hand }}" 
                                        @else
                                            data-on-hand="0"
                                        @endif
                                        data-serial-numbers="{{ $item->serial_numbers}}"
                                        value="{{ $item->id == old('item_id') ? old('item_id') : $item->id }}" 
                                        {{ $item->id == old('item_id') ? 'selected' : '' }} 
                                    >{{ $item->name }}@if ($item->upc) ({{ $item->upc }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="item_change_table_with_calculations" hidden>
                        <table id="item_change_table" class="table table-bordered table-sm table-hover">
                            <thead>
                                <tr>
                                    <th class="w-25">Item</th>
                                    <th>UPC</th>
                                    <th>On Hand</th>
                                    <th class="w-25">Serial Number</th>
                                    <th>Qty</th>
                                    <th>Selling Price</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                        
                            <tbody></tbody>
                        </table>
                        <br>
                        
                        <div class="col-md-4 col-xs-12 float-right" id="calculation">
                            <div class="form-group row">
                                <label for="gross_total" class="col-sm-4 col-form-label">Net Total</label>
                                
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="gross_total" name="gross_total" tabindex='-1' readonly autocomplete="off">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="discount" class="col-sm-4 col-form-label">Less: Return Credit</label>
                                
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="discount" name="discount" placeholder="Discount" readonly autocomplete="off" step=".01">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="change_total" class="col-sm-4 col-form-label">Customer Additional Payment</label>
                                
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="net_total" name="change_total" tabindex='-1' readonly autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                        <!-- <table id="change_items_table" class="table table-bordered table-sm table-hover">
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
                        <br> -->
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" id="create_change_button" class="btn btn-success" disabled>Change Item</button>
                </div>

                <input type="hidden" class="form-control" id="sale_id" name="sale_id" value="{{ $sale->id }}" tabindex='-1'>
            </form>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            let itemsSelected = [];
            let hasSerialNumbersYetToBeSelected = [];
            let refundTotal = 0;

            $('#search_item').select2({
                placeholder: "Select an item"
            });

            $('#search_item').on('select2:select', function(e) {
                rowNumber = $('#item_change_table tbody tr').length;
                item = $(this).find(':selected');
                let selectSerialNumbers = "";

                // check if item has already been selected to avoid duplicate selection of item
                if (! itemsSelected.includes(item.data('id'))) {
                    itemsSelected.push(item.data('id'));

                    $.each(item.data('serial-numbers'), function (key, value) {
                        selectSerialNumbers += "<option value='" + key + "'>" + value + "</option>";
                    });
                    
                    let name = "<input type='string' class='form-control-plaintext name' name='changes[" + rowNumber + "][name]' value='" + item.data('name') + "' tabindex='-1' readonly>";
                    let upc = "<input type='string' class='form-control-plaintext' value='" + item.data('upc') + "' tabindex='-1' readonly>";
                    let onHand = "<input type='number' class='form-control-plaintext on_hand' name='changes[" + rowNumber + "][on_hand]' value='" + item.data('on-hand') + "' tabindex='-1' readonly>";
                    let id = "<input type='hidden' class='item_id' name='changes[" + rowNumber + "][item_id]' value='" + item.data('id') + "'>";
                    let withSerialNumber = "<input type='hidden' class='with_serial_number' name='changes[" + rowNumber + "][with_serial_number]' value='" + item.data('with-serial-number') + "'>";
                    let serialNumber = (item.data('with-serial-number')) ? "<select name='changes[" + rowNumber + "][item_purchase_id][]' class='form-control item_purchase_id serial_number select_serial_numbers' style='width: 100%; min-width: 200px;' multiple='multiple' required>" + selectSerialNumbers + "</select>" : "";
                    let quantity = (item.data('with-serial-number')) ? "<input type='number' class='form-control-plaintext quantity' name='changes[" + rowNumber + "][quantity]' tabindex='-1' readonly>" : "<input type='number' class='form-control quantity' name='changes[" + rowNumber + "][quantity]' min='1' max='" + item.data('on-hand') + "' required>";
                    let costPrice = "<input type='number' class='form-control-plaintext cost_price' name='changes[" + rowNumber + "][cost_price]' value='" + item.data('cost-price') + "' tabindex='-1' readonly>";
                    let sellingPrice = "<input type='number' class='form-control-plaintext selling_price' name='changes[" + rowNumber + "][selling_price]' value='" + item.data('selling-price') + "' readonly step='.01'>";
                    let amount = "<input type='number' class='form-control-plaintext amount' name='changes[" + rowNumber + "][amount]' tabindex='-1' readonly>";
                    let removeButton = "<button type='button' class='btn btn-default remove_item' tabindex='-1'><i class='fas fa-fw fa-times'></i></button>";
                    
                    $('#item_change_table tbody').append('<tr id="' + rowNumber + '"><td>' + id + withSerialNumber + name + '</td><td>' + upc + '</td><td>' + onHand + '</td><td>' + serialNumber + '</td><td>' + quantity + '</td><td>' + sellingPrice + '</td><td>' + amount + '</td><td>' + removeButton + '</td></tr>');

                    $('.select_serial_numbers').select2({
                        language:{
                            "noResults" : function () { 
                                return '';
                            }
                        }
                    });

                    if ($('#item_change_table tbody tr').length > 0) {
                        $('#item_change_table_with_calculations').attr('hidden', false);
                        $('#create_change_button').attr('disabled', false);
                    }
                    else {
                        $('#item_change_table_with_calculations').attr('hidden', true);
                        $('#create_change_button').attr('disabled', true);
                    }
                }
                else {
                    alert('Item already selected');
                }
            });

            // $(document).on('select2:select select2:unselect', '.serial_number', function() {
            //     let totalSerialNumbers = $(this).select2('data').length;
            //     let rowNumber = $(this).closest('tr').attr('id');
            //     $('input[name="items[' + rowNumber + '][quantity]"]').val(totalSerialNumbers);
            // });

            $(document).on('select2:select select2:unselect', '.serial_number', function() {
                let totalAmount = 0;
                let rowNumber = $(this).closest('tr').attr('id');
                let quantity = $(this).select2('data').length;
                let sellingPrice = $('input[name="changes[' + rowNumber + '][selling_price]"]').val();
                let amount = quantity * sellingPrice;
                $('input[name="changes[' + rowNumber + '][quantity]"]').val(quantity);
                $('input[name="changes[' + rowNumber + '][amount]"]').val(amount);

                $('#item_change_table tbody tr').each(function() {
                    if ($(this).find('.amount').val() == '') {
                        $(this).find('.amount').val(0);
                    }

                    totalAmount += parseFloat($(this).find('.amount').val());
                });

                $('#gross_total').val(totalAmount);
                let netTotal = $('#gross_total').val() - $('#discount').val();
                $('#net_total').val(netTotal);
            });

            $('.select-serial-number').select2();

            $(document).on('select2:unselecting', '.select-serial-number', function (e) {
                hasSerialNumbersYetToBeSelected.push(1);

                // if (hasSerialNumbersYetToBeSelected.length) {
                //     $('#create_replacement_button').attr('disabled', true);
                // }

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
                refundTotal += parseFloat(item.data('sold-price'));
                $('#refund_total').val(refundTotal);
                $('#discount').val(refundTotal);

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
                                                      + '</td><td>' 
                                                            + soldPrice
                                                      + '</td><td id="amount-' + item.data('item-id') + '">' 
                                                      + '</td></tr>');
                    
                    $('#serial-number-' + item.data('item-id')).select2().append("<option value='" + data.id + "' selected>" + data.text + "</option>");
                    $('#qty-' + item.data('item-id')).html(qty);
                    $('#amount-' + item.data('item-id')).html(amount);
                }
                
                $('#proceed-button').attr('disabled', false);
            });

            $(document).on('select2:select', '.replacement-serial-numbers', function (e) {
                hasSerialNumbersYetToBeSelected.pop(1);
                $(this).closest("td").siblings(".return-quantity").text($(this).select2('data').length);

                // if (! hasSerialNumbersYetToBeSelected.length) {
                //     $('#create_replacement_button').attr('disabled', false);
                // }
            });

            $(document).on('select2:unselect', '.replacement-serial-numbers', function (e) {
                hasSerialNumbersYetToBeSelected.push(1);
                $(this).closest("td").siblings(".return-quantity").text($(this).select2('data').length);

                // if (hasSerialNumbersYetToBeSelected.length) {
                //     $('#create_replacement_button').attr('disabled', true);
                // }
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
                let soldPrice = "<input type='number' class='form-control-plaintext' name='items[" + rowNumber + "][sold_price]' value='" + $("input[name='sold_price']",this).val() + "' tabindex='-1' readonly>";
                let name = "<input type='string' class='form-control-plaintext' name='items[" + rowNumber + "][name]' value='" + $("input[name='name']",this).val() + "' tabindex='-1' readonly>";
                let upc = "<input type='string' class='form-control-plaintext' name='items[" + rowNumber + "][upc]' value='" + $("input[name='upc']",this).val() + "' tabindex='-1' readonly>";
                let itemId = "<input type='hidden' class='item_id' name='items[" + rowNumber + "][item_id]' value='" + $("input[name='item_id']",this).val() + "'>";
                let saleId = "<input type='hidden' class='sale_id' name='items[" + rowNumber + "][sale_id]' value='" + $("input[name='sale_id']",this).val() + "'>";
                let total = $("input[name='sold_price']",this).val() * returnQuantity;
                let amount = "<input type='text' class='form-control-plaintext' value='" + total + "' tabindex='-1' readonly>";
                refundTotal += parseFloat(total);
                $('#refund_total').val(refundTotal);
                $('#discount').val(refundTotal);

                $('#returned_items_table tbody').append('<tr data-item-id="' + $("input[name='item_id']",this).val() + '" id=' + itemId + '><td>' 
                                                        + itemId + name + saleId + selectedSerialNumbers
                                                    + '</td><td>' 
                                                        + upc
                                                    + '</td><td>'
                                                    + '</td><td>'
                                                        + qty
                                                    + '</td><td>'
                                                        + soldPrice
                                                    + '</td><td>'
                                                        + amount
                                                    + '</td></tr>');

                for (let i = 0; i < itemPurchaseIds.length; i++) {
                    $('#serial-number-' + $("input[name='item_id']",this).val()).append("<option value='" + itemPurchaseIds[i] + "' selected>" + itemPurchaseIds[i] + "</option>");
                }
            });

            $(document).on('keyup', '.quantity', function(event) {
                let totalAmount = 0;
                let rowNumber = $(this).closest('tr').attr('id');
                let itemId = $('input[name="changes[' + rowNumber + '][item_id]"]').val();
                let onHand = $('input[name="changes[' + rowNumber + '][on_hand]"]').val();
                let quantity = $('input[name="changes[' + rowNumber + '][quantity]"]').val();
                let sellingPrice = $('input[name="changes[' + rowNumber + '][selling_price]"]').val();
                let amount = quantity * sellingPrice;
                $('#create_change_button').attr('disabled', true);

                if (parseInt(quantity) > parseInt(onHand)) {
                    alert("Quantity must not be more than on hand quantity");
                    $('input[name="changes[' + rowNumber + '][quantity]"]').val(quantity.slice(0, -1));
                    return false;
                }

                $.ajax({
                    type: 'GET',
                    url: '/getItemsWithOutSerialNumberForReplacement/' + itemId + '/' + $(this).val(),
                    success: function (item) {
                        $('input[name="changes[' + rowNumber + '][item_purchase_id][]"]').remove();

                        let changesSelected = '';

                        $.each(item.purchases, function (key, item_purchase) {
                            changesSelected += '<input type="hidden" class="item_purchase_id" name="changes[' + rowNumber + '][item_purchase_id][]" value="' + item_purchase.id + '">';
                        });

                        $('input[name="changes[' + rowNumber + '][quantity]"]').after(changesSelected);
                        $('#create_change_button').attr('disabled', false);
                        // console.log(replacementsSelected);
                    }
                });


                $('input[name="changes[' + rowNumber + '][amount]"]').val(amount);

                $('#item_change_table tbody tr').each(function() {
                    if ($(this).find('.amount').val() == '') {
                        $(this).find('.amount').val(0);
                    }

                    totalAmount += parseFloat($(this).find('.amount').val());
                });

                $('#gross_total').val(totalAmount);
                let netTotal = $('#gross_total').val() - $('#discount').val();
                $('#net_total').val(netTotal);
            });
            
            $('#item_change_table').on('click', '.remove_item', function(e){
                let rowNumber = $(this).closest('tr').attr('id');
                let totalAmount = 0;
                itemsSelected.splice(rowNumber, 1);
                $(this).closest('tr').remove();

                $('#item_change_table tbody tr').each(function(i) {
                    console.log('here');
                    console.log('test: ' + i);
                    $(this).attr('id', i);
                    $(this).find('.serial_number').attr('name', 'changes[' + i + '][item_purchase_id][]');
                    $(this).find('.item_purchase_id').attr('name', 'changes[' + i + '][item_purchase_id][]');
                    $(this).find('.quantity').attr('name', 'changes[' + i + '][quantity]');
                    $(this).find('.name').attr('name', 'changes[' + i + '][name]');
                    $(this).find('.on_hand').attr('name', 'changes[' + i + '][on_hand]');
                    $(this).find('.cost_price').attr('name', 'changes[' + i + '][cost_price]');
                    $(this).find('.with_serial_number').attr('name', 'changes[' + i + '][with_serial_number]');
                    $(this).find('.item_id').attr('name', 'changes[' + i + '][item_id]');
                    $(this).find('.selling_price').attr('name', 'changes[' + i + '][selling_price]');
                    $(this).find('.amount').attr('name', 'changes[' + i + '][amount]');

                    if ($(this).find('.amount').val() == '') {
                        $(this).find('.amount').val(0);
                    }

                    totalAmount += parseFloat($(this).find('.amount').val());
                });

                $('#gross_total').val(totalAmount);
                let netTotal = $('#gross_total').val() - $('#discount').val();
                $('#net_total').val(netTotal);
                
                if ($('#item_change_table tbody tr').length > 0) {
                    $('#item_change_table_with_calculations').attr('hidden', false);
                    $('#create_change_button').attr('disabled', false);
                }
                else {
                    $('#item_change_table_with_calculations').attr('hidden', true);
                    $('#create_change_button').attr('disabled', true);
                }
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
                if (confirm('Are you sure to create item change?')) {
                    $('.serial-numbers').attr('disabled', false);
                }
                else {
                    return false;
                }
            });
        });
    </script>
@stop