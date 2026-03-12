<?php

use App\Models\AdminUser;
use App\Models\CompanyHeader;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceType;
use App\Models\Order;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('stores invoice items when unit and quantity are empty strings', function () {
    $adminUser = createInvoiceTestAdminUser();
    $invoiceType = createInvoiceTestInvoiceType();
    $purchaseEntity = createInvoiceTestCompanyHeader();
    $seller = createInvoiceTestSeller();
    $order = createInvoiceTestOrder('INV-STORE-001');

    Sanctum::actingAs($adminUser, [], 'sanctum');

    $response = $this->postJson('/api/invoices', buildInvoicePayload(
        order: $order,
        invoiceType: $invoiceType,
        purchaseEntity: $purchaseEntity,
        seller: $seller,
        cnyItems: [
            ['fee_type_id' => '', 'unit' => '', 'quantity' => '', 'amount' => '0'],
        ],
        usdItems: []
    ));

    $response->assertCreated();

    $invoice = Invoice::query()->latest('id')->firstOrFail();
    $invoiceItem = InvoiceItem::query()->where('invoice_id', $invoice->id)->firstOrFail();

    expect($invoiceItem->currency)->toBe('cny');
    expect($invoiceItem->unit)->toBeNull();
    expect($invoiceItem->quantity)->toBeNull();
    expect((string)$invoiceItem->amount)->toBe('0.00');
});

it('updates invoice items when unit and quantity are empty strings', function () {
    $adminUser = createInvoiceTestAdminUser();
    $invoiceType = createInvoiceTestInvoiceType();
    $purchaseEntity = createInvoiceTestCompanyHeader();
    $seller = createInvoiceTestSeller();
    $order = createInvoiceTestOrder('INV-UPDATE-001');

    $invoice = Invoice::withoutEvents(static function () use ($order, $invoiceType, $purchaseEntity, $seller) {
        return Invoice::query()->create([
            'order_id' => $order->id,
            'invoice_type_id' => $invoiceType->id,
            'purchase_entity_id' => $purchaseEntity->id,
            'purchase_usc_code' => 'PURCHASE-USC',
            'sale_entity_id' => $seller->id,
            'sale_usc_code' => 'SELLER-USC',
            'invoice_type_name' => $invoiceType->name,
            'total_cny_amount' => '0.00',
            'total_usd_amount' => '0.00',
        ]);
    });

    Sanctum::actingAs($adminUser, [], 'sanctum');

    $response = $this->putJson("/api/invoices/{$invoice->id}", buildInvoicePayload(
        order: $order,
        invoiceType: $invoiceType,
        purchaseEntity: $purchaseEntity,
        seller: $seller,
        cnyItems: [
            ['fee_type_id' => '', 'unit' => '', 'quantity' => '', 'amount' => '12.50'],
        ],
        usdItems: []
    ));

    $response->assertOk();

    $invoiceItem = InvoiceItem::query()->where('invoice_id', $invoice->id)->firstOrFail();

    expect($invoiceItem->unit)->toBeNull();
    expect($invoiceItem->quantity)->toBeNull();
    expect((string)$invoiceItem->amount)->toBe('12.50');
});

function createInvoiceTestAdminUser(): AdminUser
{
    return AdminUser::query()->create([
        'name' => '开票测试账号',
        'username' => 'invoice_test_user',
        'password' => 'password',
    ]);
}

function createInvoiceTestInvoiceType(): InvoiceType
{
    return InvoiceType::query()->create([
        'name' => '测试发票类型',
        'tax_rate' => '0.06',
        'type' => 0,
    ]);
}

function createInvoiceTestCompanyHeader(): CompanyHeader
{
    return CompanyHeader::query()->create([
        'company_type_id' => 1,
        'company_name' => '测试购买方',
        'tax_number' => 'PURCHASE-USC',
    ]);
}

function createInvoiceTestSeller(): Seller
{
    return Seller::query()->create([
        'name' => '测试销售方',
        'tax_number' => 'SELLER-USC',
        'phone' => '13800138000',
        'address' => '测试地址',
    ]);
}

function createInvoiceTestOrder(string $jobNo): Order
{
    return Order::query()->create([
        'job_no' => $jobNo,
        'special_fee' => '0.00',
        'usd_exchange_rate' => '1.00',
    ]);
}

function buildInvoicePayload(
    Order $order,
    InvoiceType $invoiceType,
    CompanyHeader $purchaseEntity,
    Seller $seller,
    array $cnyItems,
    array $usdItems
): array {
    return [
        'order_id' => $order->id,
        'invoice_type_id' => $invoiceType->id,
        'purchase_entity_id' => $purchaseEntity->id,
        'purchase_usc_code' => 'PURCHASE-USC',
        'sale_entity_id' => $seller->id,
        'sale_usc_code' => 'SELLER-USC',
        'cny_invoice_items' => json_encode($cnyItems, JSON_UNESCAPED_UNICODE),
        'usd_invoice_items' => json_encode($usdItems, JSON_UNESCAPED_UNICODE),
        'tax_rate' => '',
        'tax_amount' => '',
        'commission' => '',
    ];
}
