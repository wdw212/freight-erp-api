<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Concurrency;

Route::get('/', static function () {
    ds('Home page accessed!');
    return view('welcome');
});

Route::get('/test', static function () {
    $result = Concurrency::run([
        'task-1' => fn() => 1 + 1,
        'task-2' => fn() => 2 + 2,
    ]);
    dd($result);
});


