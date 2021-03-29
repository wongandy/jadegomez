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

        @can('create sales')
            <a href="{{ route('sale.create') }}"  class="btn btn-primary">Create Sale</a>
        @endcan
        <br><br>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sales</h3>

                @if (! $sales->where('end_of_day_at', date('Y-m-d'))->where('branch_id', auth()->user()->branch_id)->count())
                    <form action="{{ route('sale.endofday') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary float-right" onclick="return confirm('Are you sure to end day now?');">End Day</button>
                    </form>
                @endif
            </div>

            <div class="card-body">
                <table id="sales_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Sale Number</th>
                            <th>Item</th>
                            <th>Status</th>
                            <th>Customer</th>
                            <th>Approved By</th>
                            <th>Created By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr>
                                <td>{{ $sale->created_at }}</td>
                                <td>{{ $sale->sale_number }}</td>
                                <td>
                                    @foreach ($sale->items as $item)
                                        {{ $item->quantity }} x {{ $item->name }} @if ($item->sold_price) at @money($item->sold_price) @endif
                                        
                                        @if ($item->serial_number) 
                                            <br>
                                            {{ $item->serial_number }} 
                                        @endif

                                        <br><br>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($sale->status == 'void')
                                        <span class="badge badge-danger">{{ $sale->status}}</span>
                                    @elseif ($sale->status == 'for approval' || $sale->status == 'unpaid')
                                        <span class="badge badge-warning">{{ $sale->status}}</span>
                                    @else
                                        <span class="badge badge-success">{{ $sale->status}}</span>
                                    @endif
                                </td>
                                <td>{{ $sale->customer->name }}</td>
                                <td>
                                    @if ($sale->approvedByUser)
                                        {{ $sale->approvedByUser->name }}
                                    @endif
                                </td>
                                <td>{{ $sale->user->name }}</td>
                                <td>
                                    @can('delete sales')
                                        {{-- @if ($purchase->status != 'void' && (! $purchase->items()->where('status', '!=', 'available')->count())) --}}
                                        @if ($sale->status != 'void' && $sale->status != 'paid' && $sale->status != 'unpaid')
                                            <form action="{{ route('sale.void', $sale->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method("PUT")
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to void?')"><i class="fas fa-fw fa-times"></i> Void</button>
                                            </form>
                                        @endif
                                    @endcan

                                    @can('approve sales')
                                        @if ($sale->status == 'for approval' || $sale->status == 'unpaid')
                                            <a href="{{ route('sale.review', $sale->id) }}" class="btn btn-info"><i class="fas fa-fw fa-binoculars"></i> Review</a>
                                        @endif

                                    @endcan

                                    @can('print unlimited sale DR')
                                        @if ($sale->status == 'paid' || $sale->status == 'unpaid')
                                            <a href="{{ route('sale.print', $sale->id) }}" class="btn btn-info"><i class="fas fa-fw fa-print"></i> Print DR</a>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No sales yet</td>
                            </tr>
                        @endforelse
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
        $('#sales_list').DataTable({
            "order": []
        });
    }); 
    </script>
@stop