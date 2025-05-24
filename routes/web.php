<?php

use Illuminate\Support\Facades\Route;

Route::get('/haida', function () {
    return view('welcome');
});

Route::get('/users', function () {
    $users = [
        "Ali",
        "Hosam",
        "Haidar",
        "Hida"
    ];
    return view('hello', compact('users'));
});