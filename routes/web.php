<?php

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
//            'company_header_id' => 1,
//            'no_invoice_remark' => '',
//            'cny_amount' => 0,
//            'cny_invoice_number' => '',
//            'usd_amount' => 0,
//            'usd_invoice_number' => '',
//            'contact_person' => '',
//            'contact_phone' => '',
//            'remark' => '备注',
//        ],
//        [
//            'company_header_id' => 2,
//            'no_invoice_remark' => '',
//            'cny_amount' => 0,
//            'cny_invoice_number' => '',
//            'usd_amount' => 0,
//            'usd_invoice_number' => '',
//            'contact_person' => '',
//            'contact_phone' => '',
//            'remark' => '备注',
//        ]
//    ];
//    $data = [
//        [
//            'file' => '1744564496_FaL4w3ikb7.jpeg'
//        ],
//        [
//            'file' => '1744564496_FaL4w3ikb7.jpeg'
//        ],
//        [
//            'file' => '1744564496_FaL4w3ikb7.jpeg'
//        ]
//    ];
//    $data = [
//        'seller_id' => 1,
//        'company_header_id' => 1,
//        'contact_person' => '联系人',
//        'contact_phone' => '电话',
//        'remark' => [
//            [
//                'contact_phone' => '联系方式',
//                'fee' => '费用'
//            ]
//        ],
//    ];
});
