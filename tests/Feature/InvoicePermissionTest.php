<?php

use App\Models\AdminUser;
use App\Models\CompanyHeader;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceType;
use App\Models\Order;
use App\Models\Role;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('blocks business users from filling invoice numbers when updating unlocked invoice requests', function () {
    $businessUser = createInvoicePermissionUser('业务', 'BUSINESS');
    [$invoice, $invoiceType, $purchaseEntity, $seller, $order] = createInvoicePermissionFixture('INV-PERM-BUSINESS-001');

    Sanctum::actingAs($businessUser, [], 'sanctum');

    $response = $this->putJson("/api/invoices/{$invoice->id}", buildInvoicePermissionPayload(
        invoice: $invoice,
        order: $order,
        invoiceType: $invoiceType,
        purchaseEntity: $purchaseEntity,
        seller: $seller,
        overrides: [
            'cny_invoice_no' => 'BUSINESS-NO-001',
        ]
    ));

    $response->assertStatus(403)
        ->assertJson([
            'message' => '仅财务或超管可以填写发票号并确认开票',
        ]);

    expect((string)$invoice->fresh()->cny_invoice_no)->toBe('');
    expect($invoice->fresh()->confirm_at)->toBeNull();
});

it('blocks business users from updating locked invoice requests', function () {
    $businessUser = createInvoicePermissionUser('业务', 'BUSINESS');
    [$invoice, $invoiceType, $purchaseEntity, $seller, $order] = createInvoicePermissionFixture(
        'INV-PERM-BUSINESS-002',
        ['cny_invoice_no' => 'LOCKED-001']
    );

    Sanctum::actingAs($businessUser, [], 'sanctum');

    $response = $this->putJson("/api/invoices/{$invoice->id}", buildInvoicePermissionPayload(
        invoice: $invoice,
        order: $order,
        invoiceType: $invoiceType,
        purchaseEntity: $purchaseEntity,
        seller: $seller,
        overrides: [
            'remark' => '业务员尝试修改',
        ]
    ));

    $response->assertStatus(403)
        ->assertJson([
            'message' => '发票号已填写或已确认开票，业务员不可修改申请开票',
        ]);

    expect((string)$invoice->fresh()->remark)->toBe('初始备注');
});

it('allows finance users to fill invoice numbers and confirm without changing business fields', function () {
    $financeUser = createInvoicePermissionUser('财务', 'FINANCE');
    [$invoice, $invoiceType, $purchaseEntity, $seller, $order] = createInvoicePermissionFixture('INV-PERM-FINANCE-001');
    $otherOrder = createInvoicePermissionOrder('INV-PERM-FINANCE-OTHER');
    $otherInvoiceType = createInvoicePermissionInvoiceType('备选发票类型');
    $otherPurchaseEntity = createInvoicePermissionCompanyHeader('备选购买方', 'PURCHASE-ALT-USC');
    $otherSeller = createInvoicePermissionSeller('备选销售方', 'SELLER-ALT-USC');

    Sanctum::actingAs($financeUser, [], 'sanctum');

    $response = $this->putJson("/api/invoices/{$invoice->id}", buildInvoicePermissionPayload(
        invoice: $invoice,
        order: $otherOrder,
        invoiceType: $otherInvoiceType,
        purchaseEntity: $otherPurchaseEntity,
        seller: $otherSeller,
        cnyItems: [
            [
                'id' => $invoice->invoiceItems()->firstOrFail()->id,
                'fee_type_id' => '',
                'unit' => '',
                'quantity' => '',
                'amount' => '999.99',
            ],
        ],
        overrides: [
            'email' => 'finance@changed.test',
            'remark' => '财务试图改备注',
            'purchase_usc_code' => 'HACKED-USC',
            'sale_usc_code' => 'HACKED-SELLER-USC',
            'cny_invoice_no' => 'FIN-001',
            'confirm_invoice' => 1,
        ]
    ));

    $response->assertOk();

    $invoice->refresh();
    $invoiceItem = $invoice->invoiceItems()->firstOrFail();

    expect((string)$invoice->cny_invoice_no)->toBe('FIN-001');
    expect($invoice->confirm_at)->not->toBeNull();
    expect((int)$invoice->order_id)->toBe($order->id);
    expect((int)$invoice->invoice_type_id)->toBe($invoiceType->id);
    expect((int)$invoice->purchase_entity_id)->toBe($purchaseEntity->id);
    expect((int)$invoice->sale_entity_id)->toBe($seller->id);
    expect((string)$invoice->email)->toBe('origin@example.com');
    expect((string)$invoice->remark)->toBe('初始备注');
    expect((string)$invoice->purchase_usc_code)->toBe('PURCHASE-USC');
    expect((string)$invoice->sale_usc_code)->toBe('SELLER-USC');
    expect((string)$invoice->total_cny_amount)->toBe('100.00');
    expect((string)$invoiceItem->amount)->toBe('100.00');
});

it('allows super admins to update locked invoice requests', function () {
    $superAdmin = createInvoicePermissionUser('超管', 'SUPER_ADMIN');
    [$invoice, $invoiceType, $purchaseEntity, $seller, $order] = createInvoicePermissionFixture(
        'INV-PERM-SUPER-001',
        [
            'cny_invoice_no' => 'LOCKED-ADMIN-001',
            'confirm_at' => now(),
        ]
    );

    Sanctum::actingAs($superAdmin, [], 'sanctum');

    $response = $this->putJson("/api/invoices/{$invoice->id}", buildInvoicePermissionPayload(
        invoice: $invoice,
        order: $order,
        invoiceType: $invoiceType,
        purchaseEntity: $purchaseEntity,
        seller: $seller,
        overrides: [
            'remark' => '超管修改备注',
            'cny_invoice_no' => 'LOCKED-ADMIN-002',
        ]
    ));

    $response->assertOk();

    $invoice->refresh();

    expect((string)$invoice->remark)->toBe('超管修改备注');
    expect((string)$invoice->cny_invoice_no)->toBe('LOCKED-ADMIN-002');
});

function createInvoicePermissionUser(string $roleName, string $roleCode): AdminUser
{
    $role = Role::query()->firstOrCreate(
        ['name' => $roleName, 'guard_name' => 'sanctum'],
        ['code' => $roleCode]
    );

    $role->code = $roleCode;
    $role->save();

    $user = AdminUser::query()->create([
        'name' => $roleName . '权限测试账号',
        'username' => strtolower($roleCode) . '_invoice_permission',
        'password' => 'password',
    ]);
    $user->assignRole($role);

    return $user;
}

function createInvoicePermissionFixture(string $jobNo, array $invoiceOverrides = []): array
{
    $invoiceType = createInvoicePermissionInvoiceType('测试发票类型');
    $purchaseEntity = createInvoicePermissionCompanyHeader('测试购买方', 'PURCHASE-USC');
    $seller = createInvoicePermissionSeller('测试销售方', 'SELLER-USC');
    $order = createInvoicePermissionOrder($jobNo);

    $invoice = Invoice::query()->create(array_merge([
        'order_id' => $order->id,
        'invoice_type_id' => $invoiceType->id,
        'invoice_type_name' => $invoiceType->name,
        'purchase_entity_id' => $purchaseEntity->id,
        'purchase_usc_code' => 'PURCHASE-USC',
        'sale_entity_id' => $seller->id,
        'sale_usc_code' => 'SELLER-USC',
        'email' => 'origin@example.com',
        'remark' => '初始备注',
        'total_cny_amount' => '100.00',
        'total_usd_amount' => '0.00',
    ], $invoiceOverrides));

    InvoiceItem::query()->create([
        'invoice_id' => $invoice->id,
        'currency' => 'cny',
        'fee_type_id' => null,
        'amount' => '100.00',
    ]);

    return [$invoice->fresh(), $invoiceType, $purchaseEntity, $seller, $order];
}

function createInvoicePermissionInvoiceType(string $name): InvoiceType
{
    return InvoiceType::query()->create([
        'name' => $name,
        'tax_rate' => '0.06',
        'type' => 0,
    ]);
}

function createInvoicePermissionCompanyHeader(string $name, string $taxNumber): CompanyHeader
{
    return CompanyHeader::query()->create([
        'company_type_id' => 1,
        'company_name' => $name,
        'tax_number' => $taxNumber,
    ]);
}

function createInvoicePermissionSeller(string $name, string $taxNumber): Seller
{
    return Seller::query()->create([
        'name' => $name,
        'tax_number' => $taxNumber,
        'phone' => '13800138000',
        'address' => '测试地址',
    ]);
}

function createInvoicePermissionOrder(string $jobNo): Order
{
    return Order::query()->create([
        'job_no' => $jobNo,
        'special_fee' => '0.00',
        'usd_exchange_rate' => '1.00',
    ]);
}

function buildInvoicePermissionPayload(
    Invoice $invoice,
    Order $order,
    InvoiceType $invoiceType,
    CompanyHeader $purchaseEntity,
    Seller $seller,
    ?array $cnyItems = null,
    ?array $usdItems = null,
    array $overrides = []
): array {
    $defaultCnyItems = $cnyItems ?? $invoice->cnyInvoiceItems()
        ->get()
        ->map(function (InvoiceItem $invoiceItem) {
            return [
                'id' => $invoiceItem->id,
                'fee_type_id' => $invoiceItem->fee_type_id ?? '',
                'unit' => $invoiceItem->unit ?? '',
                'quantity' => $invoiceItem->quantity ?? '',
                'amount' => (string)$invoiceItem->amount,
            ];
        })
        ->all();

    $defaultUsdItems = $usdItems ?? $invoice->usdInvoiceItems()
        ->get()
        ->map(function (InvoiceItem $invoiceItem) {
            return [
                'id' => $invoiceItem->id,
                'fee_type_id' => $invoiceItem->fee_type_id ?? '',
                'unit' => $invoiceItem->unit ?? '',
                'quantity' => $invoiceItem->quantity ?? '',
                'amount' => (string)$invoiceItem->amount,
            ];
        })
        ->all();

    return array_merge([
        'id' => $invoice->id,
        'order_id' => $order->id,
        'invoice_type_id' => $invoiceType->id,
        'purchase_entity_id' => $purchaseEntity->id,
        'purchase_usc_code' => 'PURCHASE-USC',
        'sale_entity_id' => $seller->id,
        'sale_usc_code' => 'SELLER-USC',
        'email' => $invoice->email,
        'remark' => $invoice->remark,
        'invoice_date' => $invoice->invoice_date,
        'is_finish' => (int)($invoice->is_finish ?? 0),
        'commission' => $invoice->commission ?? '',
        'tax_rate' => $invoice->tax_rate ?? '',
        'tax_amount' => $invoice->tax_amount ?? '',
        'cny_invoice_no' => $invoice->cny_invoice_no ?? '',
        'usd_invoice_no' => $invoice->usd_invoice_no ?? '',
        'cny_remark' => $invoice->cny_remark ?? '',
        'usd_remark' => $invoice->usd_remark ?? '',
        'cny_invoice_items' => json_encode($defaultCnyItems, JSON_UNESCAPED_UNICODE),
        'usd_invoice_items' => json_encode($defaultUsdItems, JSON_UNESCAPED_UNICODE),
        'confirm_invoice' => 0,
    ], $overrides);
}
