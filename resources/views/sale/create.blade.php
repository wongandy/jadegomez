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
                <h3 class="card-title">Create Sale</h3>
            </div>

            <form class="form-horizontal" id="create_sale_form" action="{{ route('sale.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="form-group row">
                        <label for="sale_number" class="col-sm-2 col-form-label">Sale Number</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control-plaintext" name="sale_number" value="{{ $sale_number }}" tabindex='-1' readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="customer" class="col-sm-2 col-form-label">Customer</label>
                        
                        <div class="col-sm-10">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="customer" id="existing" value="existing" checked>
                                <label class="form-check-label" for="existing">
                                    Existing
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="customer" id="new" value="new">
                                <label class="form-check-label" for="new">
                                    New Customer
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="customer_id" class="col-sm-2 col-form-label">Select Customer</label>
                        
                        <div class="col-sm-10">
                            <select id="customer_id" class="form-control" name="customer_id" style="width: 100%;" required>
                                <option value=""></option>
                                {{-- @foreach ($customers as $customer)
                                    <option value="{{ $customer->name }}" data-contact-number="{{ $customer->contact_number }}">{{ $customer->name }} @if ($customer->contact_number) ({{ $customer->contact_number }}) @endif</option>
                                @endforeach --}}
                            </select>
                            @error('customer_id')
                                <div>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="customer_name" class="col-sm-2 col-form-label">Customer Name</label>
                        
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="customer_name" id="customer_name" value="" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="contact_number" class="col-sm-2 col-form-label">Contact Number</label>
                        
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="contact_number" id="contact_number" value="" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="search_item" class="col-sm-2 col-form-label">Select Item</label>

                        {{-- <label for="item_id">Item</label> --}}
                        <div class="col-sm-10">
                            <select id="search_item" name="search_item" class="form-control" style="width: 100%;">
                                <option></option>
                                @foreach ($items as $item)
                                    <option data-id="{{ $item->id }}" 
                                        data-name="{{ $item->name }}" 
                                        data-upc="{{ $item->upc }}" 
                                        data-with-serial-number="{{ $item->with_serial_number }}" 
                                        data-price="{{ $item->price }}"
                                        data-selling-price="{{ $item->selling_price }}"
                                        data-on-hand="{{ $item->on_hand }}"
                                        data-serial-numbers="{{ $item->serial_numbers}}"
                                        data-cost-price="{{ $item->cost_price }}"
                                        {{-- @if ($item->purchases->where('supplier_id', $supplier->id)->first()) 
                                            data-cost-price="{{ $item->purchases->where('supplier_id', $supplier->id)->last()->pivot->cost_price }}" 
                                        @endif --}}
                                        value="{{ $item->id == old('item_id') ? old('item_id') : $item->id }}" 
                                        {{ $item->id == old('item_id') ? 'selected' : '' }} 
                                    >{{ $item->name }}@if ($item->upc) ({{ $item->upc }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('search_item')
                                <div>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <ul id="errors">@if (count($errors)) {{ $errors }} @endif</ul>

                    <div id="sales_table_with_calculations" hidden>
                        <table id="sales_table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>UPC</th>
                                    <th>On Hand</th>
                                    <th>Serial Number</th>
                                    <th>Cost Price</th>
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
                                <label for="gross_total" class="col-sm-4 col-form-label">Gross Total</label>
                                
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="gross_total" name="gross_total" tabindex='-1' readonly autocomplete="off">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="discount" class="col-sm-4 col-form-label">Discount</label>
                                
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="discount" name="discount" placeholder="Discount" autocomplete="off" step=".01">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="net_total" class="col-sm-4 col-form-label">Net Total</label>
                                
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="net_total" name="net_total" tabindex='-1' readonly autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" id="create_sale_button" class="btn btn-success" disabled>Create Sale</button>
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

            $("input[name='customer']").on('change', function () {
                let value = $(this).attr('value');

                if (value == 'new') {
                    $('#customer_id').empty().attr('disabled', true);
                    $('#customer_name').attr('readonly', false).attr('required', true).val(null);
                    $('#contact_number').attr('readonly', false).val(null);
                }
                else {
                    $('#customer_id').empty().attr('disabled', false);
                    $('#customer_name').attr('readonly', true).attr('required', false).val(null);
                    $('#contact_number').attr('readonly', true).val(null);

                    $('#customer_id').select2({
                        placeholder: "Select a customer",
                        minimumInputLength: 1,
                        ajax: {
                            url: "{{ route('customer.dataajax') }}",
                            dataType: 'json',
                            delay: 250,
                            processResults: function (data) {
                                return {
                                    results: $.map(data, function (item) {
                                        return {
                                            text: item.name + ' (' + item.contact_number + ')',
                                            id: item.id,
                                            customer_name: item.name,
                                            contact_number: item.contact_number
                                        }
                                    })
                                };
                            },
                            cache: true
                        }
                    }).on('select2:select', function (e) {
                        var data = e.params.data;
                        $('#customer_name').attr('readonly', true).val(data.customer_name);
                        $('#contact_number').attr('readonly', true).val(data.contact_number);
                    });
                }
            });

            $('#search_item').select2({
                placeholder: "Select an item"
            });

            $('#customer_id').select2({
                placeholder: "Select a customer",
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('customer.dataajax') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name + ' (' + item.contact_number + ')',
                                    id: item.id,
                                    customer_name: item.name,
                                    contact_number: item.contact_number
                                }
                            })
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function (e) {
                var data = e.params.data;
                $('#customer_name').attr('readonly', true).val(data.customer_name);
                $('#contact_number').attr('readonly', true).val(data.contact_number);
            });

            

            // return confirm('Are you sure to create sale?')

            $("#create_sale_form").on('submit', function(){
                // if ($('#status').val() == 'paid' && parseFloat($('#cash_tendered').val()) < parseFloat($('#net_total').val())) {
                //     alert('Amount is not fully paid');
                //     return false;
                // }
                // else {
                //     return confirm('Are you sure to approve sale?');
                // }
                return confirm('Are you sure to create sale?');
            });

            $('#search_item').on('select2:select', function(e) {
                rowNumber = $('#sales_table tbody tr').length;
                item = $(this).find(':selected');
                let selectSerialNumbers = "";

                // check if item has already been selected to avoid duplicate selection of item
                if (! itemsSelected.includes(item.data('id'))) {
                    itemsSelected.push(item.data('id'));

                    $.each(item.data('serial-numbers'), function (key, value) {
                        selectSerialNumbers += "<option>" + value + "</option>";
                    });
                    
                    let name = "<input type='string' class='form-control-plaintext' value='" + item.data('name') + "' tabindex='-1' readonly>";
                    let upc = "<input type='string' class='form-control-plaintext' value='" + item.data('upc') + "' tabindex='-1' readonly>";;
                    let onHand = "<input type='number' class='form-control-plaintext on_hand' name='items[" + rowNumber + "][on_hand]' value='" + item.data('on-hand') + "' tabindex='-1' readonly>";
                    let id = "<input type='hidden' class='item_id' name='items[" + rowNumber + "][item_id]' value='" + item.data('id') + "'>";
                    let withSerialNumber = "<input type='hidden' class='with_serial_number' name='items[" + rowNumber + "][with_serial_number]' value='" + item.data('with-serial-number') + "'>";
                    let serialNumber = (item.data('with-serial-number')) ? "<select name='items[" + rowNumber + "][serial_number][]' class='form-control serial_number select_serial_numbers' style='width: 100%; min-width: 200px;' multiple='multiple' required>" + selectSerialNumbers + "</select>" : "<input type='hidden' class='serial_number' name='items[" + rowNumber + "][serial_number][]'>";
                    let quantity = (item.data('with-serial-number')) ? "<input type='number' class='form-control-plaintext quantity' name='items[" + rowNumber + "][quantity]' tabindex='-1' readonly>" : "<input type='number' class='form-control quantity' name='items[" + rowNumber + "][quantity]' min='1' max='" + item.data('on-hand') + "' required>";
                    let costPrice = "<input type='number' class='form-control-plaintext cost_price' name='items[" + rowNumber + "][cost_price]' value='" + item.data('cost-price') + "' tabindex='-1' readonly>";
                    let sellingPrice = "<input type='number' class='form-control selling_price' name='items[" + rowNumber + "][selling_price]' value='" + item.data('selling-price') + "' step='.01'>";
                    let amount = "<input type='number' class='form-control-plaintext amount' name='items[" + rowNumber + "][amount]' tabindex='-1' readonly>";
                    let removeButton = "<button type='button' class='btn btn-default remove_item' tabindex='-1'><i class='fas fa-fw fa-times'></i></button>";
                    
                    $('#sales_table tbody').append('<tr id="' + rowNumber + '"><td>' + id + withSerialNumber + name + '</td><td>' + upc + '</td><td>' + onHand + '</td><td>' + serialNumber + '</td><td>' + costPrice + '</td><td>' + quantity + '</td><td>' + sellingPrice + '</td><td>' + amount + '</td><td>' + removeButton + '</td></tr>');

                    $('.select_serial_numbers').select2({
                        language:{
                            "noResults" : function () { 
                                return '';
                            }
                        }
                    });

                    if ($('#sales_table tbody tr').length > 0) {
                        $('#sales_table_with_calculations').attr('hidden', false);
                        $('#create_sale_button').attr('disabled', false);
                    }
                    else {
                        $('#sales_table_with_calculations').attr('hidden', true);
                        $('#create_sale_button').attr('disabled', true);
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

            $('#sales_table').on('click', '.remove_item', function(e){
                let totalAmount = 0;
                let id = $(this).attr('id');
                let index = itemsSelected.indexOf(id);
                itemsSelected.splice(index, 1);
                $(this).closest('tr').remove();

                $('#sales_table tbody tr').each(function(i) {
                    $(this).attr('id', i);
                    $(this).find('.serial_number').attr('name', 'items[' + i + '][serial_number][]');
                    $(this).find('.quantity').attr('name', 'items[' + i + '][quantity]');
                    $(this).find('.on_hand').attr('name', 'items[' + i + '][on_hand]');
                    $(this).find('.cost_price').attr('name', 'items[' + i + '][cost_price]');
                    $(this).find('.with_serial_number').attr('name', 'items[' + i + '][with_serial_number]');
                    $(this).find('.item_id').attr('name', 'items[' + i + '][item_id]');
                    $(this).find('.selling_price').attr('name', 'items[' + i + '][selling_price]');
                    $(this).find('.amount').attr('name', 'items[' + i + '][amount]');

                    if ($(this).find('.amount').val() == '') {
                        $(this).find('.amount').val(0);
                    }

                    totalAmount += parseFloat($(this).find('.amount').val());
                });

                $('#gross_total').val(totalAmount);
                let netTotal = $('#gross_total').val() - $('#discount').val();
                $('#net_total').val(netTotal);
                
                if ($('#sales_table tbody tr').length > 0) {
                    $('#sales_table_with_calculations').attr('hidden', false);
                    $('#create_sale_button').attr('disabled', false);
                }
                else {
                    $('#sales_table_with_calculations').attr('hidden', true);
                    $('#create_sale_button').attr('disabled', true);
                }


                // if ($('#sales_table tbody tr').length < 1) {
                //     $('#sales_table, #calculation, #create-sales-button').attr('hidden', true);
                // }
            });

            $(document).on('select2:select select2:unselect', '.serial_number', function() {
                let totalAmount = 0;
                let rowNumber = $(this).closest('tr').attr('id');
                let quantity = $(this).select2('data').length;
                let sellingPrice = $('input[name="items[' + rowNumber + '][selling_price]"]').val();
                let amount = quantity * sellingPrice;

                $('input[name="items[' + rowNumber + '][quantity]"]').val(quantity);
                $('input[name="items[' + rowNumber + '][amount]"]').val(amount);

                $('#sales_table tbody tr').each(function() {
                    if ($(this).find('.amount').val() == '') {
                        $(this).find('.amount').val(0);
                    }

                    totalAmount += parseFloat($(this).find('.amount').val());
                });

                $('#gross_total').val(totalAmount);
                let netTotal = $('#gross_total').val() - $('#discount').val();
                $('#net_total').val(netTotal);
            });

            $(document).on('keyup', '.selling_price, .quantity', function() {
                // alert();
                let totalAmount = 0;
                let rowNumber = $(this).closest('tr').attr('id');
                let onHand = $('input[name="items[' + rowNumber + '][on_hand]"]').val();
                let quantity = $('input[name="items[' + rowNumber + '][quantity]"]').val();
                let sellingPrice = $('input[name="items[' + rowNumber + '][selling_price]"]').val();
                let amount = quantity * sellingPrice;
                // console.log('qty : ' + quantity);
                let a = 50;

                if (parseInt(quantity) > parseInt(onHand)) {
                    alert("Quantity must not be more than on hand quantity");
                    $('input[name="items[' + rowNumber + '][quantity]"]').val(quantity.slice(0, -1));
                    return false;
                }

                $('input[name="items[' + rowNumber + '][amount]"]').val(amount);

                $('#sales_table tbody tr').each(function() {
                    if ($(this).find('.amount').val() == '') {
                        $(this).find('.amount').val(0);
                    }

                    totalAmount += parseFloat($(this).find('.amount').val());
                });

                $('#gross_total').val(totalAmount);
                let netTotal = $('#gross_total').val() - $('#discount').val();
                $('#net_total').val(netTotal);
            });

            $(document).on('keyup', '#discount', function() {
                console.log($(this).val());

                if (parseFloat($(this).val()) > parseFloat($('#gross_total').val())) {
                    alert("Discount must not be more than gross total");
                    $('input[name="discount"]').val($(this).val().slice(0, -1));
                    return false;
                }
                else {
                    let netTotal = $('#gross_total').val() - $('#discount').val();
                    $('#net_total').val(netTotal);
                }
            });
        });
    </script>
@stop