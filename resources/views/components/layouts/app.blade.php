@extends('layouts.app')

@section('content')
    {{-- Forward the Livewire slot into the existing layout's content section --}}
    {{ $slot ?? '' }}
@endsection
