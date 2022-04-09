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

        <a href="{{ route('item.create') }}" class="btn btn-primary">Create Item</a><br><br>
       
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Items</h3>
            </div>
            <div class="card-body table-responsive">
                <table id="items_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>On Hand</th>
                            <th>UPC</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
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
@stop

@section('js')
    <script>
    $(document).ready(function() {
        $('#items_list').DataTable({
            "order": [0, 'asc'],
            "processing": true,
            "serverSide": true,
            "ajax":  "{{ route('item.getAllItems') }}",
            "columns": [
                {data: 'name'},
                {data: 'on_hand'},
                {data: 'upc'},
                {data: 'dynamic_cost_price'},
                {data: 'selling_price'},
                {data: 'action'}
            ]
        });
    }); 
    </script>
@stop