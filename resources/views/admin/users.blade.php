@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pending Users for Approval</h1>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <livewire:admin-user-approvals />

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

</div>
@endsection
