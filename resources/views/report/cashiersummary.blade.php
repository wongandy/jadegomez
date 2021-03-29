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
  {{-- <div class="row">
    <div class="col">
      <b>Date:</b> {{ $sale->updated_at }}<br>
      <b>Customer:</b> {{ $sale->customer->name }}<br>
      <b>Contact Number:</b> {{ $sale->customer->contact_number }}
    </div>
    <div class="col text-right">
        <b>Delivery Receipt No:</b> {{ $sale->sale_number }}<br>
        <b>Cashier:</b> {{ $sale->user->name }}<br>
    </div>
  </div>
  <br> --}}
  
  <div class="row">
    <div class="col text-center">
      <h4>Cashier Summary Report</h4>
      <h5>{{ auth()->user()->branch->name }}</h5>
      <h5>{{ auth()->user()->branch->address }}</h5>  
      <h6>From {{ $from }} To {{ $to }}</h6>  
    </div>
  </div>
  <br>
  <br>

  @foreach ($reports as $date => $cashiers)
    @if ($cashiers->count())
      <div class="row">
        <div class="col-12">
            @php
              $grand_total = 0;
            @endphp

            <h4 class="text-center">{{ $date }}</h4>

            @foreach ($cashiers as $name => $sales)
              @php
                $net_total = 0;
              @endphp
              
              <h6>Cashier: {{ $name }}</h6>
              <br>

              <table class="table table-sm table-striped">
                <thead>
                  <tr>
                    <th>Receipt Number</th>
                    <th>Sale Created</th>
                    <th>Status</th>
                    <th>Customer</th>
                    <th class="text-right">Total</th>
                  </tr>
                </thead>

                <tbody>
                  

                  @foreach ($sales as $sale)
                    @php
                      $net_total += $sale->net_total;
                    @endphp
                    <tr>
                      <td>{{ $sale->sale_number }}</td>
                      <td>{{ date('Y-m-d h:i A', strtotime($sale->created_at)) }}</td>
                      <td>{{ $sale->status }}</td>
                      <td>{{ $sale->customer_name }}</td>
                      <td class="text-right">@money($sale->net_total)</td>
                    </tr>
                  @endforeach
                    <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <th>Total</th>
                      <th class="text-right">@money($net_total)</th>
                    </tr>
                </tbody>
              </table>
              <br>
              @php
                $grand_total += $net_total;
              @endphp
            @endforeach
            <h5 class="text-center">Grand Total - @money($grand_total)</h5>
            <br>

            <hr>
        </div>
      </div>
    @endif
  @endforeach
</div>

<script type="text/javascript"> 
  window.addEventListener("load", window.print());
</script>
</body>
</html>
