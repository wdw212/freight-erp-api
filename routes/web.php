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
    $orderTypes = OrderType::query()->get();
    $data = [];
    foreach ($orderTypes as $orderType) {
        $data[] = [
            'order_type_id' => $orderType->id,
            'price' => 0
        ];
    }
    dd(json_encode($data));
});
