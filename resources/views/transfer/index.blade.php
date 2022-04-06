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
        
    @can('create transfers')
        <a href="{{ route('transfer.create') }}"  class="btn btn-primary">Create Transfer</a><br><br>
    @endcan

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transfers</h3>
            </div>

            <div class="card-body table-responsive">
                <table id="transfers_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Transfer Number</th>
                            <th>Information</th>
                            <th>Item</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Received By</th>
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
@endsection

@section('js')
    <script>
    $(document).ready(function() {
        $(document).on('submit', '.void_transfer_form', function () {
            if (confirm('Are you sure to void?')) {
                $(this).find(":submit").attr('disabled', true);
            }
            else {
                return false;
            }
        });

        $(document).on('submit', '.receive_transfer_form', function () {
            if (confirm('Are you sure to receive transfer?')) {
                $(this).find(":submit").attr('disabled', true);
            }
            else {
                return false;
            }
        });


        $('#transfers_list').DataTable({
            "order": [],
            "processing": true,
            "serverSide": true,
            "ajax":  "{{ route('transfer.getAllTransfers') }}",
            "columns": [
                {data: 'created_at'},
                {data: 'transfer_number'},
                {data: 'information'},
                {data: 'details'},
                {data: 'status'},
                {data: 'notes'},
                {data: 'received_by'},
                {data: 'user.name'},
                {data: 'action'}
            ]
        });
    }); 
    </script>
@stop