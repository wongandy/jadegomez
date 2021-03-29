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

        <a href="{{ route('supplier.create') }}" class="btn btn-primary">Create Supplier</a><br><br>
       
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Suppliers</h3>
            </div>
            <div class="card-body">
                <table id="suppliers_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact Number</th>
                            @canany(['edit suppliers', 'delete suppliers'])
                                <th>Action</th>
                            @endcanany
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->contact_number }}</td>
                                <td>
                                    @can('edit suppliers')
                                        <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-info">Edit</a>
                                    @endcan

                                    {{-- @can('delete items') --}}
                                        {{-- <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to delete?')">Delete</button>
                                        </form> --}}
                                    {{-- @endcan --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No supplier yet</td>
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
        $('#suppliers_list').DataTable({
            "order": []
        });
    }); 
    </script>
@stop