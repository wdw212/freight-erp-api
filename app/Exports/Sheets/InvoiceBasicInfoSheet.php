<?php

namespace App\Exports\Sheets;

use App\Models\Invoice;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Sheet 1 — 1-发票基本信息
 * 税务数电发票模板（V251101版）表1，39列(A-AM)
 */
class InvoiceBasicInfoSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    public function __construct(
        private readonly Collection $invoices,
        private readonly string     $exportType,
    ) {
    }

    public function title(): string
    {
        return '1-发票基本信息';
    }

    public function headings(): array
    {
        return [
            '发票流水号',              // A
            '发票类型',                // B
            '特定业务类型',            // C
            '是否含税',                // D
            '受票方自然人标识',        // E
            '购买方名称',              // F
            '购买方纳税人识别号',      // G
            '购买方证件类型',          // H
            '购买方证件号码',          // I
            '购买方国籍（或地区）',    // J
            '购买方地址',              // K
            '购买方所在地区',          // L
            '购买方所在地区',          // M
            '购买方所在地区',          // N
            '购买方所在地区',          // O
            '购买方详细地址',          // P
            '购买方电话',              // Q
            '购买方开户银行',          // R
            '购买方银行账号',          // S
            '是否展示购买方地址电话银行账号', // T
            '是否开具涉税专业服务发票品目',   // U
            '涉税专业服务协议编号',    // V
            '备注',                    // W
            '报废产品销售类型',        // X
            '每千克煤炭发热量',        // Y
            '干基全硫',                // Z
            '干燥无灰基挥发分',        // AA
            '销售方开户行',            // AB
            '销售方银行账号',          // AC
            '是否展示销售方地址电话银行账号', // AD
            '购买方邮箱',              // AE
            '购买方经办人姓名',        // AF
            '购买方经办人证件类型',    // AG
            '购买方经办人证件号码',    // AH
            '经办人国籍(地区)',        // AI
            '经办人自然人纳税人识别号', // AJ
            '放弃享受减按1%征收率原因', // AK
            '收款人',                  // AL
            '复核人',                  // AM
        ];
    }

    private function resolveInvoiceTypeName(): string
    {
        return match ($this->exportType) {
            'normal', 'amount' => '普通发票',
            'vat_special' => '增值税专用发票',
            default => '普通发票',
        };
    }

    private function resolveSpecificBusinessType(): string
    {
        return match ($this->exportType) {
            'vat_special' => '货物运输服务',
            default => '',
        };
    }

    /**
     * 解析是否含税：从 InvoiceType 配置读取
     */
    private function resolveIsTaxIncluded(Invoice $invoice): string
    {
        $isTaxIncluded = $invoice->invoiceType?->is_tax_included;
        if ($isTaxIncluded === null) {
            return '是';
        }
        return $isTaxIncluded ? '是' : '否';
    }

    public function collection(): Collection
    {
        $invoiceTypeName = $this->resolveInvoiceTypeName();
        $specificBusinessType = $this->resolveSpecificBusinessType();

        return $this->invoices->map(function (Invoice $invoice) use (
            $invoiceTypeName,
            $specificBusinessType
        ) {
            $serialNumber = (string)($invoice->export_serial_number ?? '');
            $purchaseName = (string)($invoice->purchase_entity['name'] ?? '');
            $purchaseUscCode = (string)($invoice->purchase_usc_code ?? '');
            $isTaxIncluded = $this->resolveIsTaxIncluded($invoice);
            $remark = $this->buildRemark($invoice);
            $payee = (string)($invoice->invoiceType?->payee ?? '');
            $reviewer = (string)($invoice->invoiceType?->reviewer ?? '');
            $displaySalesInfo = (string)($invoice->invoiceType?->display_sales_info_items ?? '');
            $email = (string)($invoice->email ?? '');

            return [
                $serialNumber,          // A 发票流水号
                $invoiceTypeName,       // B 发票类型
                $specificBusinessType,  // C 特定业务类型
                $isTaxIncluded,         // D 是否含税
                '',                     // E 受票方自然人标识
                $purchaseName,          // F 购买方名称
                $purchaseUscCode,       // G 购买方纳税人识别号
                '',  // H
                '',  // I
                '',  // J
                '',  // K
                '',  // L
                '',  // M
                '',  // N
                '',  // O
                '',  // P
                '',  // Q
                '',  // R
                '',  // S
                '',  // T
                '',  // U
                '',  // V
                $remark,                // W 备注
                '',  // X
                '',  // Y
                '',  // Z
                '',  // AA
                '',  // AB
                '',  // AC
                $displaySalesInfo,      // AD 是否展示销售方信息
                $email,                 // AE 购买方邮箱
                '',  // AF
                '',  // AG
                '',  // AH
                '',  // AI
                '',  // AJ
                '',  // AK
                $payee,                 // AL 收款人
                $reviewer,              // AM 复核人
            ];
        });
    }

    /**
     * 备注：读取前端表单对应的备注字段
     * - 普通/专用发票导出 → cny_remark（人民币备注）
     * - 美金发票导出     → usd_remark（美金备注）
     */
    private function buildRemark(Invoice $invoice): string
    {
        if ($this->exportType === 'amount') {
            return trim((string)($invoice->usd_remark ?? ''));
        }
        return trim((string)($invoice->cny_remark ?? ''));
    }
}
