@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <livewire:admin-user-approvals />

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

</div>
@endsection
