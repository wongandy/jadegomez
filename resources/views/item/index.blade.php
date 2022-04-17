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

        <a href="{{ route('item.create') }}" class="btn btn-primary">Create Item</a><br><br>
       
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Items</h3>
            </div>
            <div class="card-body table-responsive">
                {!! $dataTable->table(['class' => 'table table-bordered table-striped']) !!}
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
@stop

@section('js')

<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="/vendor/datatables/buttons.server-side.js"></script>
    {!! $dataTable->scripts() !!}
@stop