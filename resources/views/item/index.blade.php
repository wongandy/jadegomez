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
            <div class="card-body">
                <table id="items_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Details</th>
                            <th>UPC</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>With Serial Number</th>
                            @canany(['edit items', 'delete items'])
                                <th>Action</th>
                            @endcanany
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->details }}</td>
                                <td>{{ $item->upc }}</td>
                                <td>{{ $item->dynamic_cost_price }}</td>
                                <td>{{ $item->selling_price }}</td>
                                <td>{{ $item->with_serial_number ? 'Yes' : 'No' }}</td>
                                <td>
                                    @can('edit items')
                                        <a href="{{ route('item.edit', $item->id) }}" class="btn btn-info">Edit</a>
                                    @endcan

                                    {{-- @can('delete items') --}}
                                        {{-- <form action="{{ route('item.destroy', $item->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to delete?')">Delete</button>
                                        </form> --}}
                                    {{-- @endcan --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No item yet</td>
                            </tr>
                        @endforelse
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
            "order": []
        });
    }); 
    </script>
@stop