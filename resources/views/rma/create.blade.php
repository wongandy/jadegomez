@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">RMA Tracker</h3>
            </div>

            <form class="form-horizontal" id="create_sale_form">
                <div class="card-body">
                    <div class="form-group row">
                        <label for="serial_number" class="col-sm-2 col-form-label">Serial Number</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="serial_number" id="serial_number" autofocus autocomplete="off">
                        </div>
                    </div>

                    <div id="sales_table_with_calculations" class="table-responsive">
                        <table id="sales_table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date Sale Created</th>
                                    <th>Sale Number</th>
                                    <th>Branch</th>
                                    <th>Item</th>
                                    <th>Serial Number</th>
                                    <th>Customer</th>
                                    <th>Contact Number</th>
                                    <th>Created By</th>
                                    <th>Approved By</th>
                                </tr>
                            </thead>
                        
                            <tbody>
                                <tr>
                                    <td colspan="9">No Item Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $(document).on('keypress',function(e) {
                if(e.which == 13) {
                    e.preventDefault();
                }
            });

            $(document).on('keyup', '#serial_number', function () {
                $.ajax({
                    url: "{{ route('itempurchase.rmatrack') }}",
                    data: { serial_number: $(this).val() },
                    success: function (data) {
                        let html;

                        if (data.length) {
                            $.each(data, function(i, item) {
                                html += "<tr><td>" + data[i].item_sale.sale.created_at + "</td><td>" + data[i].item_sale.sale.sale_number + "</td><td>" + data[i].item_sale.sale.branch.address + "</td><td>" + data[i].item_sale.item.name + "</td><td>" + data[i].serial_number + "</td><td>" + data[i].item_sale.sale.customer.name + "</td><td>" + data[i].item_sale.sale.customer.contact_number + "</td><td>" + data[i].item_sale.sale.user.name + "</td><td>" + data[i].item_sale.sale.approved_by_user.name + "</td></tr>";
                            });
                        }
                        else {
                            html = "<tr><td colspan='9'>No Item Found</tr>";
                        }
                        
                        $("#sales_table tbody").html(html);
                    }
                });
            });
        });
    </script>
@stop