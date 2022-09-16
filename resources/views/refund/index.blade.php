@extends('adminlte::page')
@section('plugins.Datatables', true)

@section('content')
<div class="row">  
    <div class="col-12">
        @if (session('message'))
            <div class="alert alert-{{ (session('type')) ? session('type') : 'success' }} alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <i class="icon fas fa-check"></i>{{ session('message') }}
            </div>
        @endif

        <div class="card card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#returned_items" role="tab">Returned Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#all_branches_sales" role="tab">All Branches Sales</a>
                    </li>
                </ul>
            </div>
            <div class="card-body table-responsive">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="returned_items" role="tabpanel">
                        <table id="item_returns_list" class="table table-bordered table-striped" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Sale Date</th>
                                    <th>Sale Number</th>
                                    <th>Item</th>
                                    <th>Customer</th>
                                    <th>Sale By</th>
                                    <th>Sale At</th>
                                    <th>RMA Status</th>
                                    <th>RMA Date</th>
                                    <th>RMA Number</th>
                                    <th>Returned Item</th>
                                    <th>RMA By</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade show" id="all_branches_sales" role="tabpanel">
                        <table id="all_branches_sales_list" class="table table-bordered table-striped" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Sale Number</th>
                                    <th>Item</th>
                                    <th>Status</th>
                                    <th>Customer</th>
                                    <th>Created At</th>
                                    <th>Approved By</th>
                                    <th>Created By</th>
                                    <th></th>
                                </tr>
                            </thead>
                            
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
    $(document).ready(function() {
        $(document).on('submit', '.void_refund_form', function () {
            if (confirm('Are you sure to void?')) {
                $(this).find(":submit").attr('disabled', true);
            }
            else {
                return false;
            }
        });

        $('#item_returns_list').DataTable({
            "order": [7, 'desc'],
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('return.getAllReturns') }}",
            columns: [
                {data: 'sale.created_at'},
                {data: 'sale.sale_number'},
                {data: 'sale.details', searchable: false},
                {data: 'sale.customer.name'},
                {data: 'sale.user.name'},
                {data: 'sale.branch.address'},
                {data: 'status'},
                {data: 'created_at'},
                {data: 'refund_number'},
                {data: 'detail', searchable: false},
                {data: 'user.name'},
                {data: 'action', sortable: false},
            ]
        });

        $('#all_branches_sales_list').DataTable({
            "order": [0, 'desc'],
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('sale.getAllBranchesSales') }}",
            columns: [
                {data: 'created_at', name: 'sales.created_at'},
                {data: 'sale_number', name: 'sales.sale_number'},
                {data: 'details', name: 'sales.details'},
                {data: 'status', name: 'sales.status'},
                {data: 'customer.name', name: 'customer.name'},
                {data: 'branch.address', name: 'branch.address'},
                {data: 'approvedByUser', name: 'approvedByUser.name'},
                {data: 'user.name', name: 'user.name'},
                {data: 'action', name: 'action', sortable: false}
            ]
        });
    }); 
    </script>
@stop