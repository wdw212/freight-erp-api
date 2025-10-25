<?php

use App\Models\CompanyHeader;
use App\Models\Container;
use App\Models\Order;
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
            'fee_type_id' => 1,
            'currency' => 'cny',
            'quantity' => 1,
            'price' => 1,
            'remark' => '备注',
        ],
        [
            'fee_type_id' => 1,
            'currency' => 'cny',
            'quantity' => 1,
            'price' => 1,
            'remark' => '备注',
        ],
    ];

    $string = json_encode($data, JSON_UNESCAPED_UNICODE);
    dd($string);
});

Route::get('/test3', static function () {
    $containerTypeStats = Container::query()
        ->where('order_id', 42)
        ->with('containerType')
        ->groupBy('container_type_id')
        ->selectRaw('container_type_id, count(*) as count')
        ->get()
        ->map(function ($item) {
            return [
                'type_id' => $item->container_type_id,
                'type_name' => $item->containerType->name,
                'count' => $item->count,
            ];
        });

    $containerType = '';
    foreach ($containerTypeStats as $containerTypeStat) {
        $containerType = $containerTypeStat['count'] . '*' . $containerTypeStat['type_name'] . ';';
    }
    dd($containerType);
});

Route::get('/test4', static function () {
});
