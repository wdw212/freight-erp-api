<?php

namespace App\Http\Resources\Transaction;

use App\Enums\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $category
 * @property mixed $title
 * @property mixed $account
 * @property mixed $invoice_no
 * @property mixed $serial_number
 * @property mixed $income_cny
 * @property mixed $expense_cny
 * @property mixed $income_usd
 * @property mixed $expense_usd
 * @property mixed $remark
 * @property mixed $exchange_rate
 * @property mixed $created_at
 * @property mixed $seller
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'seller' => $this->seller,
            'category' => TransactionCategory::from($this->category)->getName(),
            'title' => $this->title,
            'account' => $this->account,
            'invoice_no' => $this->invoice_no,
            'serial_number' => $this->serial_number,
            'income_cny' => $this->income_cny,
            'expense_cny' => $this->expense_cny,
            'income_usd' => $this->income_usd,
            'expense_usd' => $this->expense_usd,
            'remark' => $this->remark,
            'exchange_rate' => $this->exchange_rate,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
