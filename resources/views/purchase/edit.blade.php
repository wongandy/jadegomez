@extends('layouts.app')

@section('content')
<div>
    <p>Edit Branch</p>
    <form action="{{ route('branch.update', $branch->id) }}" method="POST">
        @csrf
        @method("PUT")
        <label for="address">Name</label>
        <input type="text" name="address" value="{{ $branch->address }}">
        @error('address')
        <div>{{ $message }}</div>
        @enderror
        <br>
        <label for="contact_number">Contact Number</label>
        <input type="text" name="contact_number" value="{{ $branch->contact_number }}">
        @error('contact_number')
        <div>{{ $message }}</div>
        @enderror
        <br>
        <button type="submit">Edit Branch</button>
    </form>
</div>
@endsection