@extends('layouts.app')

@section('content')
    @livewire('clients.client-form', ['clientId' => $client->id])
@endsection
