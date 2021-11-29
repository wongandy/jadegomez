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

        @if (! $today_sales->whereBetween('end_of_day_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->where('branch_id', auth()->user()->branch_id)->count())
            <form action="{{ route('sale.endofday') }}" method="POST" style="display: inline">
                @csrf
                <button type="submit" class="btn btn-primary float-right" onclick="return confirm('Are you sure to end day now?');">End Day</button>
            </form>
        @endif
        <br><br>
        
        <div class="card card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#today_sales" role="tab" aria-controls="andy" aria-selected="true">Today's Sales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#all_sales" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">All Sales</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane fade show active" id="today_sales" role="tabpanel" aria-labelledby="today_sales">
                        <table id="today_sales_list" class="table table-bordered table-striped">
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
                                @forelse ($today_sales as $sale)
                                    <tr>
                                        <td>{{ $sale->created_at }}</td>
                                        <td>{{ $sale->sale_number }}</td>
                                        <td>{!! $sale->details !!}</td>
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
                                                {{-- @if ($sale->status != 'void' && $sale->status != 'paid' && $sale->status != 'unpaid') --}}
                                                @if ($sale->status != 'void')
                                                    <form action="{{ route('sale.void', $sale->id) }}" class="void_sale_form" method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method("PUT")
                                                        <button type="submit" class="btn btn-danger"><i class="fas fa-fw fa-times"></i> Void</button>
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
                    <div class="tab-pane fade show" id="all_sales" role="tabpanel" aria-labelledby="all_sales">
                        <table id="all_sales_list" class="table table-bordered table-striped" style="width: 100%">
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
        $(document).on('submit', '.void_sale_form', function () {
            if (confirm('Are you sure to void?')) {
                $(this).find(":submit").attr('disabled', true);
            }
            else {
                return false;
            }
        });

        $('#today_sales_list').DataTable({
            "order": []
        });

        $('#all_sales_list').DataTable({
            "order": [],
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('sale.getAllSales') }}",
            columns: [
                {data: 'created_at', name: 'sales.created_at'},
                {data: 'sale_number', name: 'sales.sale_number'},
                {data: 'details', name: 'sales.details'},
                {data: 'status', name: 'sales.status'},
                {data: 'customer.name', name: 'customer.name'},
                {data: 'approvedByUser', name: 'approvedByUser.name'},
                {data: 'user.name', name: 'user.name'},
                {data: 'action', name: 'action'},
            ]
        });
    }); 
    </script>
@stop