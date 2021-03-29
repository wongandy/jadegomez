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

        @can('create purchases')
            <a href="{{ route('purchase.supplier') }}" class="btn btn-primary">Create Purchase</a><br><br>
        @endcan

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Purchases</h3>
            </div>
            <div class="card-body">
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
                        @forelse ($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->updated_at }}</td>
                                <td>{{ $purchase->purchase_number }}</td>
                                <td>
                                    @foreach ($purchase->items as $item)
                                        {{ $item->quantity }} x {{ $item->name }} @if ($item->show->cost_price) at @money($item->show->cost_price) @endif
                                        
                                        @if ($item->serial_number) 
                                            <br>
                                            {{ $item->serial_number }} 
                                        @endif

                                        <br><br>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($purchase->status == 'void')
                                        <span class="badge badge-danger">{{ $purchase->status}}</span>
                                    @else
                                        <span class="badge badge-success">{{ $purchase->status}}</span>
                                    @endif
                                </td>
                                <td>{{ $purchase->supplier->name }}</td>
                                <td>{{ $purchase->user->name }}</td>
                                <td>
                                    @can('delete purchases')
                                        @if ($purchase->status != 'void' && (! $purchase->items()->where('status', '!=', 'available')->count()))
                                            <form action="{{ route('purchase.void', $purchase->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method("PUT")
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to void?')"><i class="fas fa-fw fa-times"></i> Void</button>
                                            </form>
                                        @endif
                                        {{-- <a href="{{ route('purchase.delete', $purchase->id) }}" class="btn btn-danger">Void</a> --}}
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No purchases yet</td>
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
        $('#purchases_list').DataTable({
            "order": []
        });
    }); 
    </script>
@stop