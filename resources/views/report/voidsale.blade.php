<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Jade Gomez Computer Trading</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body>
<div class="wrapper">
  <div class="row">
    <div class="col text-center">
      <h4>Void Transaction Summary Report</h4>
      <h5>{{ auth()->user()->branch->name }}</h5>
      <h5>{{ auth()->user()->branch->address }}</h5>    
      <h6>From {{ $from }} To {{ $to }}</h6>  
    </div>
  </div>
  <br>
  <br>

    <div class="row">
        <div class="col-12">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                      <th>Receipt Number</th>
                      <th>Sale Created</th>
                      <th>User</th>
                      <th class="text-right">Price</th>
                      <th class="text-right">Discount</th>
                      <th class="text-right">Total</th>
                      <th class="text-right">Customer</th>
                      <th class="text-right">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($sales as $sale)
                    <tr>
                        <th>{{ $sale->sale_number }}</th>
                        <th>{{ $sale->created_at->toDateString() }} {{date('h:i A', strtotime($sale->created_at)) }}</th>
                        <th>{{ $sale->user->name }}</th>
                        <th class="text-right"></th>
                        <th class="text-right">@money($sale->discount)</th>
                        <th class="text-right">@money($sale->net_total)</th>
                        <th class="text-right">{{ $sale->customer->name }}</th>
                        <th class="text-right">{{ $sale->status }}</th>
                        {{-- <td>{{ $sale->net_total }}</td> --}}
                    </tr>
                      @foreach ($sale->items as $item)
                        <tr>
                          <td class="text-right">{{ $item->quantity }} x {{ $item->name }}</td>
                          <td>{{ $item->upc }}</td>
                          <td></td>
                          <td class="text-right">@money($item->sold_price)</td>
                          <td></td>
                          <td class="text-right">@money($item->quantity * $item->sold_price)</td>
                          <td></td>
                        </tr>
                      @endforeach
                    @endforeach
                    {{-- <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th>Total</th>
                        <th>{{ $net_total }}</th>
                    </tr> --}}
                </tbody>
            </table>
            <br>
        </div>
    </div>
</div>

<script type="text/javascript"> 
  window.addEventListener("load", window.print());
</script>
</body>
</html>
