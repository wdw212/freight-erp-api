<?php

namespace App\Observers;

use App\Models\CompanyHeader;
use App\Models\Invoice;
use App\Models\OrderReceipt;
use App\Models\Seller;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    private function resolveOriginalSnapshotName(mixed $snapshotPayload): string
    {
        if (is_string($snapshotPayload)) {
            $snapshotPayload = json_decode($snapshotPayload, true);
        }

        if (!is_array($snapshotPayload)) {
            return '';
        }

        return trim((string)($snapshotPayload['name'] ?? ''));
    }

    public function created(Invoice $invoice): void
    {
        Log::info('--发票创建成功--');
        Log::info('--待确认开票，不自动插入应收款--');
    }

    /**
     * @param Invoice $invoice
     * @return void
     */
    public function saving(Invoice $invoice): void
    {
        // 处理销售方
        $saleEntity = Seller::query()->where('id', $invoice->sale_entity_id)->first();
        $originalSaleEntityId = (string)($invoice->getOriginal('sale_entity_id') ?? '');
        $originalSaleEntityName = $this->resolveOriginalSnapshotName($invoice->getOriginal('sale_entity'));
        $saleEntityName = ($saleEntity?->name ?? '');
        if ((string)$invoice->sale_entity_id === $originalSaleEntityId && !empty($originalSaleEntityName)) {
            $saleEntityName = $originalSaleEntityName;
        }
        $invoice->sale_entity = [
            'name' => $saleEntityName,
            'usc_code' => $invoice->sale_usc_code,
        ];
        // 处理购买方
        $purchaseEntity = CompanyHeader::query()->where('id', $invoice->purchase_entity_id)->first();
        $originalPurchaseEntityId = (string)($invoice->getOriginal('purchase_entity_id') ?? '');
        $originalPurchaseEntityName = $this->resolveOriginalSnapshotName($invoice->getOriginal('purchase_entity'));
        $purchaseEntityName = ($purchaseEntity?->company_name ?? '');
        if ((string)$invoice->purchase_entity_id === $originalPurchaseEntityId && !empty($originalPurchaseEntityName)) {
            $purchaseEntityName = $originalPurchaseEntityName;
        }
        $invoice->purchase_entity = [
            'name' => $purchaseEntityName,
            'usc_code' => $invoice->purchase_usc_code,
        ];

        // 计算税额：避免把空字符串写入 decimal 字段
        $taxAmount = calculateTaxAmount($invoice->total_cny_amount, $invoice->tax_rate);
        $invoice->tax_amount = $taxAmount === '' ? '0' : $taxAmount;
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        // 删除关联发票详情
        $invoice->invoiceItems()->delete();

        $linkedOrderReceipt = $this->findLinkedOrderReceipt($invoice);
        if ($linkedOrderReceipt) {
            $linkedOrderReceipt->delete();
        }

        $this->syncOrderReceiptTotals($invoice);
        $this->syncOrderInvoiceLockStatus($invoice);
        $this->syncOrderSpecialFee($invoice);
    }

    private function findLinkedOrderReceipt(Invoice $invoice): ?OrderReceipt
    {
        $purchaseEntityId = empty($invoice->purchase_entity_id) ? null : (int)$invoice->purchase_entity_id;
        $purchaseEntityName = trim((string)($invoice->purchase_entity['name'] ?? ''));

        $candidates = OrderReceipt::query()
            ->where('order_id', $invoice->order_id)
            ->when($purchaseEntityId !== null, function ($query) use ($purchaseEntityId) {
                $query->where('company_header_id', $purchaseEntityId);
            })
            ->orderByDesc('id')
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        $matchingCandidates = $candidates->filter(function (OrderReceipt $orderReceipt) use ($purchaseEntityId, $purchaseEntityName) {
            if ($purchaseEntityId !== null && (int)$orderReceipt->company_header_id === $purchaseEntityId) {
                return true;
            }

            return $purchaseEntityName !== ''
                && trim((string)($orderReceipt->company_header_name ?? '')) === $purchaseEntityName;
        });

        if ($matchingCandidates->isEmpty()) {
            $matchingCandidates = $candidates;
        }

        $exactAmountMatch = $matchingCandidates->first(function (OrderReceipt $orderReceipt) use ($invoice) {
            return $this->normalizeAmount($orderReceipt->cny_amount) === $this->normalizeAmount($invoice->total_cny_amount)
                && $this->normalizeAmount($orderReceipt->usd_amount) === $this->normalizeAmount($invoice->total_usd_amount);
        });

        if ($exactAmountMatch) {
            return $exactAmountMatch;
        }

        $zeroAmountAutoReceipt = $matchingCandidates->first(function (OrderReceipt $orderReceipt) {
            return $this->normalizeAmount($orderReceipt->cny_amount) === '0.00'
                && $this->normalizeAmount($orderReceipt->usd_amount) === '0.00'
                && $this->isBlankLinkedReceipt($orderReceipt);
        });

        if ($zeroAmountAutoReceipt) {
            return $zeroAmountAutoReceipt;
        }

        if ($invoice->created_at) {
            $linkedByCreatedAt = $matchingCandidates
                ->filter(function (OrderReceipt $orderReceipt) use ($invoice) {
                    if (!$orderReceipt->created_at) {
                        return false;
                    }

                    return abs($orderReceipt->created_at->getTimestamp() - $invoice->created_at->getTimestamp()) <= 60;
                })
                ->sortBy(function (OrderReceipt $orderReceipt) use ($invoice) {
                    return abs($orderReceipt->created_at->getTimestamp() - $invoice->created_at->getTimestamp());
                })
                ->first();

            if ($linkedByCreatedAt) {
                return $linkedByCreatedAt;
            }
        }

        return null;
    }

    private function isBlankLinkedReceipt(OrderReceipt $orderReceipt): bool
    {
        return trim((string)($orderReceipt->no_invoice_remark ?? '')) === ''
            && trim((string)($orderReceipt->cny_invoice_number ?? '')) === ''
            && trim((string)($orderReceipt->usd_invoice_number ?? '')) === ''
            && trim((string)($orderReceipt->remark ?? '')) === '';
    }

    private function normalizeAmount(mixed $amount): string
    {
        return number_format((float)($amount ?? 0), 2, '.', '');
    }

    private function syncOrderReceiptTotals(Invoice $invoice): void
    {
        $order = $invoice->order()->first();
        if (!$order) {
            return;
        }

        $order->receipt_total_cny_amount = $order->orderReceipts()->sum('cny_amount');
        $order->receipt_total_usd_amount = $order->orderReceipts()->sum('usd_amount');
        $order->save();
    }

    private function syncOrderInvoiceLockStatus(Invoice $invoice): void
    {
        $order = $invoice->order()->first();
        if (!$order) {
            return;
        }

        $hasLockedInvoice = Invoice::query()
            ->where('order_id', $order->id)
            ->where(function ($query) {
                $query->where(function ($invoiceQuery) {
                    $invoiceQuery->whereNotNull('cny_invoice_no')
                        ->where('cny_invoice_no', '<>', '');
                })->orWhere(function ($invoiceQuery) {
                    $invoiceQuery->whereNotNull('usd_invoice_no')
                        ->where('usd_invoice_no', '<>', '');
                });
            })
            ->exists();

        $order->is_lock = $hasLockedInvoice ? 1 : 0;
        $order->save();
    }

    private function syncOrderSpecialFee(Invoice $invoice): void
    {
        $order = $invoice->order()->first();
        if (!$order) {
            return;
        }

        $specialFee = Invoice::query()
            ->where('order_id', $order->id)
            ->whereNotNull('confirm_at')
            ->where('is_finish', 1)
            ->sum('total_cny_amount');

        $order->special_fee = number_format((float)$specialFee, 2, '.', '');
        $order->save();
    }
}
