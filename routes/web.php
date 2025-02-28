<?php

use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return abort(404);
});
