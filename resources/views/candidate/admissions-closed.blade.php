@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">
@endpush
@section('content')
<div class="container text-center" style="margin-top: 80px;">
    <h3>Admissions are currently closed</h3>
    <p>Please check back when the next admission session opens.</p>
</div>
@endsection