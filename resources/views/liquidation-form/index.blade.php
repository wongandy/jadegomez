@extends('adminlte::page')
@section('plugins.Datatables', true)

@section('content')
<div class="row">
    <div class="col-12">
        @if (session('message'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fas fa-check"></i>{{ session('message') }}
            </div>
        @endif
        
    <!-- @can('create transfers') -->
        <a href="{{ route('liquidation.create') }}"  class="btn btn-primary">Create Liquidation Form</a><br><br>
    <!-- @endcan -->

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Liquidation Forms</h3>
            </div>

            <div class="card-body table-responsive">
                <table id="liquidation_forms_table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total Checks</th>
                            <th>Total Expenses</th>
                            <th>Total Cash</th>
                            <th>Total Sales</th>
                            <th>Notes</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
    $(document).ready(function() {
        $('#liquidation_forms_table').DataTable({
            "order": [],
            "order": [0, 'desc'],
            "processing": true,
            "serverSide": true,
            "ajax":  "{{ route('liquidation.getAllRecords') }}",
            "columns": [
                {data: 'created_at'},
                {data: 'total_checks', 'sortable': false, 'searchable': false},
                {data: 'total_expenses', 'sortable': false, 'searchable': false},
                {data: 'total_cashes', 'sortable': false, 'searchable': false},
                {data: 'total_sales', 'sortable': false, 'searchable': false},
                {data: 'notes', 'sortable': false, 'searchable': true},
                {data: 'user.name', 'sortable': false},
            ]
        });
    }); 
    </script>
@stop