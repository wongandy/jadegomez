@extends('adminlte::page')
@section('plugins.Datatables', true)

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sales</h3>
            </div>

            <div class="card-body">
                <table id="reports_list" class="table">
                    <thead>
                        <tr>
                            <th>Receipt Number</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>User</th>
                            <th>Qty</th>
                            <th>Gross Total</th>
                            <th>Discount</th>
                            <th>Net Total</th>
                            <th>Customer</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sales as $sale)
                            <tr>
                                <td>{{ $sale->sale_number }}</td>
                                <td>{{ $sale->updated_at->toDateString() }}</td>
                                <td>{{ date('h:i A', strtotime($sale->updated_at)) }}</td>
                                <td>{{ $sale->user->name }}</td>
                                <td>{{ $sale->items->first()->quantity }}</td>
                                <td>{{ $sale->gross_total }}</td>
                                <td>{{ $sale->discount }}</td>
                                <td>{{ $sale->net_total }}</td>
                                <td>{{ $sale->customer->name }}</td>
                            </tr>
                
                            @foreach ($sale->items as $item)
                            <tr>
                                <td class="text-right">{{ $item->quantity }} x {{ $item->name }}</td>
                                <td>{{ $item->upc }}</td>
                                <td>{{ $item->serial_number }}</td>
                                <td></td>
                                <td>{{ $item->sold_price }}</td>
                                <td>{{ $item->sold_price * $item->quantity }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
    // $(document).ready(function() {
    //     $('#reports_list').DataTable({
    //         "order": [ 0, "DESC" ]
    //     });
    // }); 
    </script>
@stop