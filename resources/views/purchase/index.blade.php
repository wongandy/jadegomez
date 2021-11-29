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

        @can('create purchases')
            <a href="{{ route('purchase.supplier') }}" class="btn btn-primary">Create Purchase</a><br><br>
        @endcan

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Purchases</h3>
            </div>
            <div class="card-body table-responsive">
                <table id="purchases_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Purchase Number</th>
                            <th>Item</th>
                            <th>Status</th>
                            <th>Supplier</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
    $(document).ready(function() {
        $(document).on('submit', '.void_purchase_form', function () {
            if (confirm('Are you sure to void?')) {
                $(this).find(":submit").attr('disabled', true);
            }
            else {
                return false;
            }
        });

        $('#purchases_list').DataTable({
            "order": [],
            "processing": true,
            "serverSide": true,
            "ajax":  "{{ route('purchase.getAllPurchases') }}",
            "columns": [
                {data: 'created_at', name: 'purchases.created_at'},
                {data: 'purchase_number', name: 'purchases.purchase_number'},
                {data: 'details', name: 'purchases.details'},
                {data: 'status', name: 'purchases.status'},
                {data: 'supplier.name', name: 'supplier.name'},
                {data: 'user.name', name: 'user.name'},
                {data: 'action', name: 'user.name'}
            ]
        });
    }); 
    </script>
@stop