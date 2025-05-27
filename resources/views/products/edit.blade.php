@extends('layouts.app')

@section('content')
    @livewire('products.product-form', ['productId' => $product->id])
@endsection
