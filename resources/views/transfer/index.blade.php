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
        
    @can('create transfers')
        <a href="{{ route('transfer.create') }}"  class="btn btn-primary">Create Transfer</a><br><br>
    @endcan

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transfers</h3>
            </div>

            <div class="card-body">
                <table id="transfers_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Transfer Number</th>
                            <th>Details</th>
                            <th>Item</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Received By</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transfers as $transfer)
                            <tr>
                                <td>{{ $transfer->created_at }}</td>
                                <td>{{ $transfer->transfer_number }}</td>
                                @if ($transfer->sending_branch_id == auth()->user()->branch_id) 
                                    <td style="color: red">
                                        Sent To {{ $transfer->receivingBranch->address }} 
                                    </td>
                                @else
                                    <td style="color: green">
                                        Received From {{ $transfer->sendingBranch->address }} 
                                    </td>
                                @endif
                                <td>
                                    @foreach ($transfer->items as $item)
                                        {{ $item->quantity }} x {{ $item->name }}
                                        
                                        @if ($item->serial_number) 
                                            <br>
                                            {{ $item->serial_number }} 
                                        @endif

                                        <br><br>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($transfer->status == 'void')
                                        <span class="badge badge-danger">{{ $transfer->status}}</span>
                                    @elseif ($transfer->status == 'pending')
                                        <span class="badge badge-warning">{{ $transfer->status}}</span>
                                    @elseif ($transfer->status == 'received')
                                    <span class="badge badge-success">{{ $transfer->status}}</span>
                                    @endif
                                </td>
                                <td>{{ $transfer->notes }}</td>
                                <td>
                                    @if ($transfer->receivedByUser) 
                                        {{ $transfer->receivedByUser->name }} 
                                    @endif
                                </td>
                                <td>{{ $transfer->user->name }}</td>
                                <td>
                                    @if ($transfer->receiving_branch_id == auth()->user()->branch_id && $transfer->status != 'received') 
                                        <form action="{{ route('transfer.updatestatus', $transfer) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-info" type="btn btn-info" onclick="return confirm('Are you sure to receive transfer?');">Receive</button>
                                        </form>
                                    @endif
                                    @can('delete transfers')
                                        @if ($transfer->status != 'received' && $transfer->status != 'void' && $transfer->sending_branch_id == auth()->user()->branch_id)
                                            <form action="{{ route('transfer.void', $transfer->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method("PUT")
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to void?')"><i class="fas fa-fw fa-times"></i> Void</button>
                                            </form>
                                        @endif
                                        {{-- <a href="{{ route('purchase.delete', $purchase->id) }}" class="btn btn-danger">Void</a> --}}
                                    @endcan

                                    @if ($transfer->sending_branch_id == auth()->user()->branch_id && $transfer->status != 'void')
                                        <a target="_blank" href="{{ route('transfer.print', $transfer->id) }}" class="btn btn-info" style="display: inline-block;"><i class="fas fa-fw fa-print"></i> Print DR</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">No transfers yet</td>
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
        $('#transfers_list').DataTable({
            "order": []
        });
    }); 
    </script>
@stop