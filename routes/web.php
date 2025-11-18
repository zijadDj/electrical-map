<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('map');
});

Route::get('/map', function (){
   return view('map');
});
