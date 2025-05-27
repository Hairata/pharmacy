@extends('layouts.app')

@section('content')
    @livewire('pharmacists.pharmacist-form', ['pharmacistId' => $pharmacist->id])
@endsection
