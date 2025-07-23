<?php

use App\Models\OrderType;
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return view('welcome');
});

Route::get('/test', static function () {
    $result = Concurrency::run([
        'task-1' => fn() => 1 + 1,
        'task-2' => fn() => 2 + 2,
    ]);
    dd($result);
});

Route::get('/test2', static function () {
    $data = [
        [
            'content' => '内容1'
        ],
        [
            'content' => '内容2'
        ],
        [
            'id' => 3,
            'content' => '内容3'
        ],
    ];

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
});
