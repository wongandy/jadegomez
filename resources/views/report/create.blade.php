@extends('adminlte::page')
@section('plugins.Momentjs', true)
@section('plugins.Daterangepicker', true)
@section('plugins.Select2', true)
{{-- @if (count($errors) > 0) 
    {{ dd($errors) }}
@endif --}}
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Report</h3>
            </div>
            {{-- <ul id="errors">
            </ul> --}}
            <form id="testform" class="form-horizontal" action="{{ route('report.print') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="form-group row">
                        <label for="report_type" class="col-sm-2 col-form-label">Report Type</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="report_type" id="report_type" required>
                                <option value="" selected disabled>Select Type</option>
                                <option value="1" {{ old('report_type') == "1" ? 'selected' : '' }}>Cashier Summary Report</option>
                                <option value="2" {{ old('report_type') == "2" ? 'selected' : '' }}>Sale Detail Report</option>
                                <option value="3" {{ old('report_type') == "3" ? 'selected' : '' }}>Void Sale Summary Report</option>
                            </select>
                            @error('report_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="date_range" class="col-sm-2 col-form-label">Date range:</label>

                        <div class="col-sm-10 input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                              </div>
                            <input type="text" name="date_range" class="form-control" id="date_range" value="{{ old('date_range') }}">
                            @error('date_range')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Create Report</button>
                    <input type="hidden" name="from" id="from">
                    <input type="hidden" name="to" id="to">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
    $(document).ready(function() {
        var start = moment();
        var end = moment();

        function cb(start, end) {
            $('#from').val(start.format('YYYY-MM-DD'));
            $('#to').val(end.format('YYYY-MM-DD'));
            console.log('from: ' + $('#from').val());
            console.log('to: ' + $('#to').val());
        }

        $('#date_range').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);
        
        $(document).on('keyup', '#serial_number', function () {
            $.ajax({
                url: "{{ route('itempurchase.rmatrack') }}",
                data: { serial_number: $(this).val() },
                success: function (data) {
                    let html;

                    if (data.length) {
                        $.each(data, function(i, item) {
                            html += "<tr><td>" + new Date(data[i].item_sale.sale.created_at).toLocaleString() + "</td><td>" + data[i].item_sale.sale.sale_number + "</td><td>" + data[i].item_sale.sale.branch.address + "</td><td>" + data[i].item_sale.item.name + "</td><td>" + data[i].serial_number + "</td><td>" + data[i].item_sale.sale.customer.name + "</td><td>" + data[i].item_sale.sale.customer.contact_number + "</td><td>" + data[i].item_sale.sale.user.name + "</td><td>" + data[i].item_sale.sale.approved_by_user.name + "</td></tr>";
                        });
                    }
                    else {
                        html = "<tr><td colspan='9'>No Item Found</tr>";
                    }
                    
                    $("#sales_table tbody").html(html);
                }
            });
        });
    }); 
    </script>
@stop