<?php

use Illuminate\Support\Facades\Route;

// api routes.
Route::group(
    [
        'prefix'     => 'api',
        'middleware' => ['api'],
        'namespace'  => 'Haxibiao\Store\Http\Api',
    ],
    __DIR__ . '/routes/api.php'
);

// web routes.
Route::group(
    [
        'middleware' => ['web'],
        'namespace'  => 'Haxibiao\Store\Http\Controllers',
    ],
    __DIR__ . '/routes/web.php'
);
