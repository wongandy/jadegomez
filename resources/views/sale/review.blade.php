@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Review Sale</h3>
            </div>

            <form class="form-horizontal" id="review_sale_form" action="{{ route('sale.updatestatus', $sale) }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="form-group row">
                        <label for="date" class="col-sm-2 col-form-label">Date</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control-plaintext" id="date" value="{{ $sale->updated_at->format('Y-m-d') }}" disabled>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="sale_number" class="col-sm-2 col-form-label">Sale Number</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control-plaintext" id="sale_number" value="{{ $sale->sale_number }}" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="customer_id" class="col-sm-2 col-form-label">Customer</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control-plaintext" id="customer_id" value="{{ $sale->customer->name }}" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="contact_number" class="col-sm-2 col-form-label">Contact Number</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control-plaintext" id="contact_number" value="{{ $sale->customer->contact_number }}" disabled>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>UPC</th>
                                <th>Serial Number</th>
                                <th>Qty</th>
                                <th>Selling Price</th>
                                <th>Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($sale->items as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->upc }}</td>
                                    <td>{{ $item->serial_number }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->selling_price }}</td>
                                    <td>{{ $item->quantity * $item->selling_price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <br>

                    <div class="col-md-4 col-xs-12 float-right">
                        <div class="form-group row">
                            <label for="gross_total" class="col-sm-4 col-form-label">Gross Total</label>
                            
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $sale->gross_total }}" tabindex='-1' disabled>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="discount" class="col-sm-4 col-form-label">Discount</label>
                            
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $sale->discount }}" tabindex='-1' disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="net_total" class="col-sm-4 col-form-label">Net Total</label>
                            
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="net_total"  value="{{ $sale->net_total }}" tabindex='-1' disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="net_total" class="col-sm-4 col-form-label">Cash Tendered</label>
                            
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="cash_tendered" name="cash_tendered" autocomplete="off" step='.01' disabled required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="net_total" class="col-sm-4 col-form-label">Change</label>
                            
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="change" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label">Status</label>
                            
                            <div class="col-sm-8">
                                <select name="status" class="form-control" id="status" required>
                                    <option value="" @if ($sale->status != 'paid' || $sale->status != 'unpaid') selected @endif disabled>Please select</option>
                                    <option value="paid" @if ($sale->status == 'paid') selected @endif>Paid</option>
                                    <option value="unpaid" @if ($sale->status == 'unpaid') selected @endif>Unpaid</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success" id="approve_sale_button">Approve Sale</button>
                    <a href="{{ route('sale.index') }}" class="btn btn-default float-right">Go Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#cash_tendered').on('keyup', function() {
            if (parseFloat($(this).val()) > parseFloat($('#net_total').val())) {
                $('#change').val((parseFloat($(this).val()).toFixed(2) - parseFloat($('#net_total').val()).toFixed(2)));
            }
            else {
                $('#change').val('');
            }
        });

        $("#review_sale_form").on('submit', function(){
            if ($('#status').val() == 'paid' && parseFloat($('#cash_tendered').val()) < parseFloat($('#net_total').val())) {
                alert('Amount is not fully paid');
                return false;
            }
            else {
                return confirm('Are you sure to approve sale?');
            }
        });

        $('#status').on('change', function () {
            if ($(this).val() == 'paid') {
                $('#cash_tendered').attr('disabled', false);
            }
            else {
                $('#change').val('');
                $('#cash_tendered').val('').attr('disabled', true);
            }
        });
    });
</script>
@stop