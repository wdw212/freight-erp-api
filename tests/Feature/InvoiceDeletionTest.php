<?php

use App\Models\AdminUser;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderReceipt;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('blocks finance users from deleting invoices', function () {
    $financeUser = createAdminUserWithRole('财务', 'FINANCE');
    $order = createOrderForInvoice('FINANCE-READONLY-001');
    $invoice = createInvoiceForOrder($order, '100.00', '0.00');
    $receiptId = (int)OrderReceipt::query()
        ->where('order_id', $order->id)
        ->latest('id')
        ->value('id');

    Sanctum::actingAs($financeUser, [], 'sanctum');

    $response = $this->deleteJson("/api/invoices/{$invoice->id}");

    $response->assertStatus(403)
        ->assertJson([
            'message' => '仅超管可以删除开票管理信息',
        ]);

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
    ]);
    $this->assertDatabaseHas('order_receipts', [
        'id' => $receiptId,
    ]);
});

it('allows super admins to delete invoices and linked receipt rows', function () {
    $superAdmin = createAdminUserWithRole('超管', 'SUPER_ADMIN');
    $order = createOrderForInvoice('SUPER-DELETE-001');
    $invoice = createInvoiceForOrder($order, '256.80', '12.50');
    $receiptId = (int)OrderReceipt::query()
        ->where('order_id', $order->id)
        ->latest('id')
        ->value('id');

    InvoiceItem::query()->create([
        'invoice_id' => $invoice->id,
        'currency' => 'cny',
        'amount' => '256.80',
    ]);

    $order->update([
        'receipt_total_cny_amount' => '256.80',
        'receipt_total_usd_amount' => '12.50',
    ]);

    Sanctum::actingAs($superAdmin, [], 'sanctum');

    $response = $this->deleteJson("/api/invoices/{$invoice->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('invoices', [
        'id' => $invoice->id,
    ]);
    $this->assertDatabaseMissing('invoice_items', [
        'invoice_id' => $invoice->id,
    ]);
    $this->assertDatabaseMissing('order_receipts', [
        'id' => $receiptId,
    ]);

    expect((float)$order->fresh()->receipt_total_cny_amount)->toBe(0.0);
    expect((float)$order->fresh()->receipt_total_usd_amount)->toBe(0.0);
});

it('removes zero-amount linked receipts created before invoice totals were updated', function () {
    $superAdmin = createAdminUserWithRole('超管', 'SUPER_ADMIN');
    $order = createOrderForInvoice('SUPER-DELETE-002');
    $invoice = createInvoiceForOrder($order, '0.00', '0.00');
    $receiptId = (int)OrderReceipt::query()
        ->where('order_id', $order->id)
        ->latest('id')
        ->value('id');

    $invoice->update([
        'total_cny_amount' => '99.90',
        'total_usd_amount' => '0.00',
    ]);

    Sanctum::actingAs($superAdmin, [], 'sanctum');

    $response = $this->deleteJson("/api/invoices/{$invoice->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('order_receipts', [
        'id' => $receiptId,
    ]);
});

function createAdminUserWithRole(string $roleName, string $roleCode): AdminUser
{
    $role = Role::query()->firstOrCreate(
        ['name' => $roleName, 'guard_name' => 'sanctum'],
        ['code' => $roleCode]
    );

    $role->code = $roleCode;
    $role->save();

    $user = AdminUser::query()->create([
        'name' => $roleName . '测试账号',
        'username' => strtolower($roleCode) . '_tester',
        'password' => 'password',
    ]);
    $user->assignRole($role);

    return $user;
}

function createOrderForInvoice(string $jobNo): Order
{
    return Order::query()->create([
        'job_no' => $jobNo,
        'special_fee' => '0.00',
        'usd_exchange_rate' => '1.00',
    ]);
}

function createInvoiceForOrder(Order $order, string $totalCnyAmount, string $totalUsdAmount): Invoice
{
    return Invoice::query()->create([
        'order_id' => $order->id,
        'invoice_type_id' => 1,
        'purchase_entity_id' => 1,
        'purchase_usc_code' => 'PURCHASE-USC',
        'sale_entity_id' => 1,
        'sale_usc_code' => 'SALE-USC',
        'total_cny_amount' => $totalCnyAmount,
        'total_usd_amount' => $totalUsdAmount,
    ]);
}
