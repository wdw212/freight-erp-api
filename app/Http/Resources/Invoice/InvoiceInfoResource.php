<?php

namespace App\Http\Resources\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceInfoResource extends JsonResource
{
    private function resolveFinishStatus(): int
    {
        $orderFinishStatus = $this->order?->is_finish;
        if ($orderFinishStatus !== null) {
            return (int)$orderFinishStatus;
        }

        return (int)($this->is_finish ?? 0);
    }

    private function resolveCommission(): string
    {
        $orderCommission = $this->order?->commission;
        if ($orderCommission !== null) {
            return (string)$orderCommission;
        }

        return (string)($this->commission ?? '0');
    }

    private function resolveLockStatus(): int
    {
        $orderLockStatus = $this->order?->is_lock;
        if ($orderLockStatus !== null) {
            return (int)$orderLockStatus;
        }

        return 0;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $invoiceTypeDetail = $this->invoice_type_display;
        $invoiceTypeName = $invoiceTypeDetail['name'] ?? '';
        $invoiceType = [
            'id' => $invoiceTypeDetail['id'],
            'name' => $invoiceTypeName,
            'type' => $this->invoiceType?->type ?? null,
            'tax_rate' => $this->invoiceType?->tax_rate ?? null,
            'remark' => $this->invoiceType?->remark ?? null,
        ];

        $data['invoice_type_id'] = $invoiceTypeDetail['id'];
        $data['invoice_type_name'] = $invoiceTypeName;
        $data['invoice_type'] = $invoiceType;
        $data['invoice_type_detail'] = $invoiceType;
        $data['is_finish'] = $this->resolveFinishStatus();
        $data['commission'] = $this->resolveCommission();
        $data['is_lock'] = $this->resolveLockStatus();
        $data['order'] = $this->whenLoaded('order');

        return $data;
    }
}
