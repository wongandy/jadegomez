@extends('adminlte::page')
@section('plugins.Momentjs', true)
@section('plugins.Daterangepicker', true)
@section('plugins.Select2', true)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Activity Log</h3>
            </div>

            <form class="form-horizontal" id="create_sale_form">
                @csrf

                <div class="card-body">
                    <div class="form-group row">
                        <label for="user_id" class="col-sm-2 col-form-label">User</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="user_id" id="user_id" required>
                                <option value="0" selected disabled>Select User</option>
                                
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
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

                    <div id="sales_table_with_calculations">
                        <table id="logs_table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Activity</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                        
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <input type="hidden" name="from" id="from">
                <input type="hidden" name="to" id="to">
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
                displaylog();
            }

            function displaylog() {
                $.ajax({
                    url: "{{ route('log.displaylog') }}",
                    data: {
                        user_id: $('#user_id').val(), 
                        from: $('#from').val(), 
                        to: $('#to').val() 
                    },
                    success: function (data) {
                        let html;

                        if (data.length) {
                            $.each(data, function(i, item) {
                                html += "<tr><td>" + data[i].created_at + "</td><td>" + data[i].log_name + "</td><td>" + data[i].description + "</td></tr>";
                            });
                        }
                        else {
                            html = "<tr><td colspan='4'>No Log Found</tr>";
                        }
                        
                        $("#logs_table tbody").html(html);
                    },
                    error: function () {
                        console.log('e');
                    }
                });
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

            $('#user_id').on('change', displaylog);
            cb(start, end);
        });
    </script>
@stop