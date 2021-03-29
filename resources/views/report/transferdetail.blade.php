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
      <h4>Transfer Detail Report</h4>
      <h5>{{ auth()->user()->branch->name }}</h5>
      <h5>{{ auth()->user()->branch->address }}</h5>    
      <h6>From {{ $from }} To {{ $to }}</h6>  
    </div>
  </div>
  <br>
  <br>
  @foreach ($reports as $date => $branches)
    @if ($branches->count())
    <div class="row">
        <div class="col-12">
            <h4 class="text-center">{{ $date }}</h4>
            <br>

            @foreach ($branches as $branch => $transfer)
              <h6>To Branch: {{ $branch }}</h6>
              <br>

              <table class="table table-sm table-striped">
                  <thead>
                      <tr>
                        <th>Receipt Number</th>
                        <th>Date Time</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Transfer Created By</th>
                        <th>Received By</th>
                      </tr>
                  </thead>

                  <tbody>
                      {{-- @foreach ($transfers as $transfer) --}}
                      <tr>
                          <th>{{ $transfer->transfer_number }}</th>
                          <th>{{ $transfer->created_at->toDateString() }} {{date('h:i A', strtotime($transfer->updated_at)) }}</th>
                          <th>{{ $transfer->notes }}</th>
                          <th>{{ $transfer->status }}</th>
                          <th>{{ $transfer->user->name }}</th>
                          <th>@if ($transfer->receivedByUser) {{ $transfer->receivedByUser->name }} @endif</th>
                      </tr>
                        @foreach ($transfer->items as $item)
                          <tr>
                            <td class="text-right">{{ $item->quantity }} x {{ $item->name }}</td>
                            <td>{{ $item->upc }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
                        @endforeach

                      {{-- @endforeach --}}
                  </tbody>
              </table>
              <br>
            @endforeach
            <br>
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
