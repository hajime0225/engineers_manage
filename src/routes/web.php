<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return 'ハローワールド from Laravel!';
});

Route::get('/hello', function () {
    return 'ハローワールド from Laravel on /hello!';
});
