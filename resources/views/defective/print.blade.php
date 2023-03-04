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
  <style>
    body {
      font-family: 'Courier New', Courier, monospace;
      font-size: 19px;
      padding: 35px 38px;
      line-height: 1;
    }
  </style>
</head>
<body>
<div class="wrapper font-weight-bold">
  <div class="row">
    <div class="col">
      <b>Customer:</b> {{ $defective->sale->customer->name }}<br>
      <b>Contact Number:</b> {{ $defective->sale->customer->contact_number }}
    </div>
    <div class="col text-right">
        <b>Date:</b> {{ date('Y-m-d h:i:s a', strtotime($defective->created_at)) }}<br>
        <b>Referenced DR No:</b> {{ $defective->sale->sale_number }}<br>
        <b>CDR No:</b> {{ $defective->defective_number }}<br>
        <b>CDR Type:</b> Defective Item<br>
        <b>Cashier:</b> {{ $defective->user->name }}<br>
    </div>
  </div>
  <br>
  
  <div class="row">
    <div class="col text-center">
      <h3>{{ auth()->user()->branch->name }}</h3>
      <small>{{ $defective->branch->address }}</small><br>
      <small>Contact Number {{ $defective->branch->contact_number }}</small>    
    </div>
  </div>
  <br>

  <b>Returned the following item/s as defective:</b><br><br>
  <div class="row">
    <div class="col-12">
      <table class="table table-sm table-striped table-bordered">
        <thead>
          <tr>
            <th>Item</th>
            <th>Qty</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($defective->items as $item)
            <tr>
              <td>{{ $item->name }}</td>
              <td>{{ $item->quantity }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <hr>
  <b>Replaced with the following item/s:</b><br><br>
  <div class="row">
    <div class="col-12">
      <table class="table table-sm table-striped table-bordered">
        <thead>
          <tr>
            <th>Item</th>
            <th>Qty</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($defective->items2 as $item)
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
    <div class="col">
      <p>
        <small>This document is not valid for claiming input tax.</small><br>
        <small>Printed on <?php echo date('Y-m-d h:i:sa'); ?></small>
      </p>
    </div>
  </div>
  
  <div class="row">
    <div class="col">
      @if ($defective->items->contains('with_serial_number', 1))
        <div>
          <small><u><b>Returned item/s with serial number:</b></u></small><br>
          @foreach ($defective->items as $item)
            @if ($item->serial_number)
              <small><b>{{ $item->name }}</b></small><br>
              <small style="text-transform:uppercase">{{ $item->serial_number }}</small>
              <br>
            @endif
          @endforeach
        </div>
        <br>
      @endif

      @if ($defective->items2->contains('with_serial_number', 1))
        <div>
          <small><u><b>Replaced item/s with serial number:</b></u></small><br>
          @foreach ($defective->items2 as $item)
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
    window.location.href = "{{ route('defective.index') }}";
  }
</script>
</body>
</html>
