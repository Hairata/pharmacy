@extends('layouts.app')

@section('content')
    @livewire('sales.sale-form', ['saleId' => $sale->id])
@endsection
