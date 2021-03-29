@foreach ($cashiers as $cashier)
    <p>{{ $cashier->name }}</p>

    <table>
        <thead>
            <tr>
                <th>Receipt Number</th>
                <th>Date</th>
                <th>Time</th>
                <th>Net Total</th>
                <th>Status</th>
                <th>Customer</th>
            </tr>
        </thead>
        @foreach ($cashier->saleApproved as $sale)
        <tr>
            <td>{{ $sale->sale_number }}</td>
            <td>{{ $sale->updated_at->toDateString() }}</td>
            <td>{{ date('h:i A', strtotime($sale->updated_at)) }}</td>
            <td>{{ $sale->net_total }}</td>
            <td>{{ $sale->status }}</td>
            <td>{{ $sale->customer->name }}</td>
        </tr>
        @endforeach
    </table>
@endforeach