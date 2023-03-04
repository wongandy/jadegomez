@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Liquidation Form</h3>
            </div>
            @livewire('liquidation-form')
        </div>
    </div>
</div>
@endsection