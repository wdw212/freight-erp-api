<?php

use App\Models\CompanyHeader;
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
//    $data = [
//        [
//            'content' => '内容1'
//        ],
//        [
//            'content' => '内容2'
//        ],
//        [
//            'id' => 3,
//            'content' => '内容3'
//        ],
//    ];
//
//    echo json_encode($data, JSON_UNESCAPED_UNICODE);

    $data['company_name'] = '宁波凌亚国际物流有限公司宁波分公司111';
    $builder = CompanyHeader::query()
        ->where('company_name', $data['company_name']);
    $businessUserIds = $builder->clone()->pluck('business_user_ids')->toArray();
    $businessUserIds = array_unique(Arr::collapse($businessUserIds));
    $test = [1, 10, 11];

    foreach ($test as $item) {
        if (in_array($item, $businessUserIds)) {
            throw new \App\Exceptions\InvalidRequestException('重复数据，请重试！');
        }
    }
});
