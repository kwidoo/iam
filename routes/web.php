<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('index'); // Serves index.blade.php
})->where('any', '^(?!api).*$'); // Excludes 'api' routes
