<?php

use App\Support\InvoiceTypeValueResolver;

test('解析纯数字发票类型ID', function () {
    expect(InvoiceTypeValueResolver::resolveInvoiceTypeId(12))->toBe(12);
    expect(InvoiceTypeValueResolver::resolveInvoiceTypeId(' 12 '))->toBe(12);
    expect(InvoiceTypeValueResolver::resolveInvoiceTypeId('__snap_12'))->toBe(12);
});

test('解析对象或数组格式的发票类型ID', function () {
    expect(InvoiceTypeValueResolver::resolveInvoiceTypeId(['id' => '18']))->toBe(18);
    expect(InvoiceTypeValueResolver::resolveInvoiceTypeId(['value' => 9]))->toBe(9);
    expect(InvoiceTypeValueResolver::resolveInvoiceTypeId(['key' => '14']))->toBe(14);
    expect(InvoiceTypeValueResolver::resolveInvoiceTypeId((object)['id' => 6]))->toBe(6);
});

test('解析下拉文本并通过回调映射到ID', function () {
    $resolved = InvoiceTypeValueResolver::resolveInvoiceTypeId(
        '普---电子普通发票',
        static fn(string $name): ?int => $name === '普---电子普通发票' ? 25 : null
    );

    expect($resolved)->toBe(25);
});

test('无效值返回空', function () {
    expect(InvoiceTypeValueResolver::resolveInvoiceTypeId(['name' => 'xx']))->toBeNull();
    expect(InvoiceTypeValueResolver::resolveInvoiceTypeId('   '))->toBeNull();
    expect(InvoiceTypeValueResolver::resolveInvoiceTypeId(0))->toBeNull();
});
