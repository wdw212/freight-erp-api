<?php

namespace App\Http\Requests;

use App\Models\InvoiceType;
use App\Support\InvoiceTypeValueResolver;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'invoice_type_id' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'invoice_type_id.required' => '发票类型不能为空!',
            'invoice_type_id.integer' => '发票类型格式不正确!',
            'invoice_type_id.min' => '发票类型格式不正确!',
        ];
    }

    /**
     * 兼容不同前端下拉回传结构，统一归一化为 invoice_type_id
     */
    protected function prepareForValidation(): void
    {
        $invoiceTypeValue = $this->input('invoice_type_id');
        if ($invoiceTypeValue === null) {
            $invoiceTypeValue = $this->input('invoice_type');
        }
        if ($invoiceTypeValue === null) {
            $invoiceTypeValue = $this->input('invoice_type_detail');
        }
        if ($invoiceTypeValue === null) {
            $invoiceTypeValue = $this->input('invoice_type_name');
        }

        $invoiceTypeId = InvoiceTypeValueResolver::resolveInvoiceTypeId(
            $invoiceTypeValue,
            static fn(string $name): ?int => InvoiceType::query()
                ->where('name', $name)
                ->value('id')
        );

        if ($invoiceTypeId !== null) {
            $this->merge(['invoice_type_id' => $invoiceTypeId]);
        }
    }
}
