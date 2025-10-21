<?php

use App\Models\CompanyHeader;
use App\Models\Container;
use App\Models\Order;
use App\Models\OrderType;
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    $data = \Illuminate\Support\Arr::crossJoin([
        '红色',
        '蓝色',
        '绿色'
    ], [
        '64G',
        '128G',
        '256G'
    ]);

    $relation = [];
    foreach ($data as $item) {
        $relation[] = implode('-', $item);
    }

    dd($relation);
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
            'no' => '内容1',
            'seal_number' => '封号',
            'container_type_id' => 1,
            'serial_number' => '序列号',
            'pre_pull_wharf_id' => 1,
            'wharf_id' => 1,
            'drop_off_wharf_id' => 1,
            'is_entered_port' => 1,
            'driver' => '司机信息',
            'fleet_id' => 1,
            'cargo_weight' => 100,
            'loading_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'container_items' => [
                [
                    'bl_no' => '213123',
                    'quantity' => 1,
                    'gross_weight' => '100',
                    'volume' => 10,
                    'remark' => '备注'
                ],
            ],
            'container_loading_addresses' => [
                [
                    'loading_address_id' => 1,
                    'loading_address' => '装柜地址',
                    'address' => '地址',
                    'contact_name' => '联系人',
                    'phone' => '联系方式',
                    'remark' => '备注'
                ]
            ]
        ],
    ];

    $string = json_encode($data, JSON_UNESCAPED_UNICODE);
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
