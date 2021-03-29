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
    <div class="col">
      <b>Date:</b> {{ $transfer->created_at }}<br>
      <b>To Branch:</b> {{ $transfer->receivingBranch->address }}<br>
      <b>Contact Number:</b> {{ $transfer->receivingBranch->contact_number }}
    </div>
    <div class="col text-right">
        <b>Delivery Receipt No:</b> {{ $transfer->transfer_number }}<br>
        <b>Cashier:</b> {{ $transfer->user->name }}<br>
    </div>
  </div>
  <br>
  
  <div class="row">
    <div class="col text-center">
      <h5>{{ auth()->user()->branch->name }}</h5>
      <small>{{ auth()->user()->branch->address }}</small><br>
      <small>Contact Number {{ auth()->user()->branch->contact_number }}</small>    
    </div>
  </div>
  <br>

  <div class="row">
    <div class="col-12">
      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>Item</th>
            <th>Qty</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($transfer->items as $item)
            <tr>
              <td>{{ $item->name }}</td>
              <td>{{ $item->quantity }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="row">
    <div class="col-8">
      <p>
        <small>This document is not valid for claiming input tax. For WARRANTY purposes only.</small><br>
        <small>Printed on <?php echo date('Y-m-d h:i:sa'); ?></small>
      </p>
      
      @if ($transfer->items->contains('with_serial_number', 1))
        <div>
          @foreach ($transfer->items as $item)
            @if ($item->serial_number)
              <small><b>{{ $item->name }}</b></small><br>
              <small style="text-transform:uppercase">{{ $item->serial_number }}</small>
              <br>
            @endif
          @endforeach
        </div>
      @endif

    </div>
  </div>
</div>

<script type="text/javascript">
  window.addEventListener("load", function () {
    window.print();
    setTimeout ("closePrintView()", 1000);
  });

  function closePrintView() {
    window.location.href = "{{ route('transfer.index') }}";
  }
</script>
</body>
</html>
