<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Container\ContainerResource;
use App\Http\Resources\OrderFile\OrderFileInfoResource;
use App\Http\Resources\OrderFile\OrderFileResource;
use App\Http\Resources\OrderPayment\OrderPaymentResource;
use App\Http\Resources\OrderReceipt\OrderReceiptResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $shipping_company_id
 * @property mixed $shipping_company_name
 * @property mixed $order_type_id
 * @property mixed $business_user_id
 * @property mixed $operation_user_id
 * @property mixed $document_user_id
 * @property mixed $commerce_user_id
 * @property mixed $job_no
 * @property mixed $contract_no
 * @property mixed $bl_no
 * @property mixed $origin_port
 * @property mixed $destination_port
 * @property mixed $ship_name
 * @property mixed $ship_no
 * @property mixed $container_type
 * @property mixed $payment_method
 * @property mixed $cutoff_status
 * @property mixed $sailing_schedule
 * @property mixed $bl_status
 * @property mixed $is_delivery
 * @property mixed $sailing_at
 * @property mixed $finish_at
 * @property mixed $arrival_at
 * @property mixed $orderPayments
 * @property mixed $orderFiles
 * @property mixed $orderDelegationHeader
 * @property mixed $containers
 * @property mixed $orderReceipts
 * @property mixed $operate_user_id
 * @property mixed $booking_info
 * @property mixed $is_finish
 * @property mixed $entered_port_wharf_id
 * @property mixed $insurance
 * @property mixed $is_allowed
 * @property mixed $port_open_at
 * @property mixed $port_close_at
 * @property mixed $cutoff_at
 * @property mixed $remark
 * @property mixed $actual_sailing_at
 * @property mixed $actual_arrival_at
 * @property mixed $blInfo
 * @property mixed $orderBlInfo
 * @property mixed $payment_status
 * @property mixed $is_claimed
 * @property mixed $receipt_total_cny_amount
 * @property mixed $receipt_total_usd_amount
 * @property mixed $payment_cny_cashed_status
 * @property mixed $payment_usd_cashed_status
 * @property mixed $receipt_cny_cashed_status
 * @property mixed $receipt_usd_cashed_status
 * @property mixed $special_fee
 * @property mixed $origin_harbor_id
 * @property mixed $destination_harbor_id
 * @property mixed $usd_exchange_rate
 * @property mixed $payment_total_cny_amount
 * @property mixed $payment_total_usd_amount
 * @property mixed $gross_profit_cny
 * @property mixed $gross_profit_usd
 * @property mixed $gross_profit
 * @property mixed $total_profit
 */
class OrderInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shippingCompanyDetail = $this->shipping_company_display;
        $shippingCompanyName = $shippingCompanyDetail['name'] ?? '';
        $enteredPortWharfDetail = $this->entered_port_wharf_display;
        $enteredPortWharfName = $enteredPortWharfDetail['name'] ?? '';

        $orderFiles = collect($this->orderFiles)->map(function ($item) {
            return [
                'id' => $item->id,
                'file' => $item->file,
                'url' => formatUrl($item->file),
                'size' => $item->size,
            ];
        })->values()->all();
        return [
            'id' => $this->id,
            'order_type_id' => $this->order_type_id,
            'shipping_company_id' => $shippingCompanyDetail['id'],
            'shipping_company_name' => $shippingCompanyName,
            'shipping_company' => $shippingCompanyName,
            'shipping_company_detail' => $shippingCompanyDetail,
            'business_user_id' => $this->business_user_id,
            'operate_user_id' => $this->operate_user_id,
            'document_user_id' => $this->document_user_id,
            'commerce_user_id' => $this->commerce_user_id,
            'job_no' => $this->job_no,
            'contract_no' => $this->contract_no,
            'bl_no' => $this->bl_no,
            'origin_port' => $this->origin_port,
            'destination_port' => $this->destination_port,
            'origin_harbor_id' => $this->origin_harbor_id,
            'origin_harbor' => $this->origin_harbor_display,
            'destination_harbor_id' => $this->destination_harbor_id,
            'destination_harbor' => $this->destination_harbor_display,
            'ship_name' => $this->ship_name,
            'ship_no' => $this->ship_no,
            'booking_info' => $this->booking_info,
            'container_type' => $this->container_type,
            'payment_method' => $this->payment_method,
            'cutoff_status' => $this->cutoff_status,
            'sailing_schedule' => $this->sailing_schedule,
            'bl_status' => $this->bl_status,
            'is_delivery' => $this->is_delivery,
            'sailing_at' => $this->sailing_at,
            'arrival_at' => $this->arrival_at,
            'finish_at' => $this->finish_at,
            'cutoff_at' => $this->cutoff_at,
            'order_payments' => OrderPaymentResource::collection($this->orderPayments),
            'order_receipts' => OrderReceiptResource::collection($this->orderReceipts),
            'order_delegation_header' => $this->orderDelegationHeader,
            'order_files' => $orderFiles,
            'containers' => ContainerResource::collection($this->containers),
            'is_finish' => $this->is_finish,
            'entered_port_wharf_id' => $enteredPortWharfDetail['id'],
            'entered_port_wharf_name' => $enteredPortWharfName,
            'entered_port_wharf' => $enteredPortWharfName,
            'entered_port_wharf_detail' => $enteredPortWharfDetail,
            'insurance' => $this->insurance,
            'is_allowed' => $this->is_allowed,
            'port_open_at' => formatAt($this->port_open_at, 'Y-m-d H:i'),
            'port_close_at' => formatAt($this->port_close_at, 'Y-m-d H:i'),
            'remark' => $this->remark,
            'actual_sailing_at' => $this->actual_sailing_at,
            'actual_arrival_at' => $this->actual_arrival_at,
            'bl_info' => $this->formatOrderBlInfo(),
            'is_claimed' => $this->is_claimed,
            'payment_status' => $this->payment_status,
            'receipt_total_cny_amount' => $this->receipt_total_cny_amount,
            'receipt_total_usd_amount' => $this->receipt_total_usd_amount,
            'receipt_cny_cashed_status' => $this->receipt_cny_cashed_status,
            'receipt_usd_cashed_status' => $this->receipt_usd_cashed_status,
            'payment_total_cny_amount' => $this->payment_total_cny_amount,
            'payment_total_usd_amount' => $this->payment_total_usd_amount,
            'payment_cny_cashed_status' => $this->payment_cny_cashed_status,
            'payment_usd_cashed_status' => $this->payment_usd_cashed_status,
            'gross_profit_cny' => $this->gross_profit_cny,
            'gross_profit_usd' => $this->gross_profit_usd,
            'total_profit' => $this->total_profit,
            'special_fee' => $this->special_fee,
            'special_fee_cashed_status' => 0,
            'usd_exchange_rate' => $this->usd_exchange_rate,
        ];
    }

    /**
     * 统一提单收发通展示结构（快照优先）
     * @return array|null
     */
    private function formatOrderBlInfo(): ?array
    {
        if (!$this->orderBlInfo) {
            return null;
        }

        $blInfo = $this->orderBlInfo->toArray();

        $sender = $this->normalizePartySnapshot($blInfo['sender'] ?? null);
        $receiver = $this->normalizePartySnapshot($blInfo['receiver'] ?? null);
        $notifier = $this->normalizePartySnapshot($blInfo['notifier'] ?? null);

        $blInfo['sender'] = $sender;
        $blInfo['receiver'] = $receiver;
        $blInfo['notifier'] = $notifier;

        $blInfo['sender_name'] = $sender['name'] ?? ($blInfo['sender_id'] ?? '');
        $blInfo['receiver_name'] = $receiver['name'] ?? ($blInfo['receiver_id'] ?? '');
        $blInfo['notifier_name'] = $notifier['name'] ?? ($blInfo['notifier_id'] ?? '');

        $blInfo['sender_detail'] = [
            'id' => empty($blInfo['sender_id']) ? null : (int)$blInfo['sender_id'],
            'name' => $blInfo['sender_name'],
            'snapshot' => $sender,
        ];
        $blInfo['receiver_detail'] = [
            'id' => empty($blInfo['receiver_id']) ? null : (int)$blInfo['receiver_id'],
            'name' => $blInfo['receiver_name'],
            'snapshot' => $receiver,
        ];
        $blInfo['notifier_detail'] = [
            'id' => empty($blInfo['notifier_id']) ? null : (int)$blInfo['notifier_id'],
            'name' => $blInfo['notifier_name'],
            'snapshot' => $notifier,
        ];

        return $blInfo;
    }

    /**
     * @param mixed $value
     * @return array
     */
    private function normalizePartySnapshot(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
