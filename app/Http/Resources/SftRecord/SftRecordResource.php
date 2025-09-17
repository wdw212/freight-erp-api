<?php

namespace App\Http\Resources\SftRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $type
 * @property mixed $name
 * @property mixed $url
 * @property mixed $is_confirm
 * @property mixed $confirmUser
 * @property mixed $generate_information
 * @property mixed $remark
 * @property mixed $created_at
 * @property mixed $type_content
 * @property mixed $code
 * @property mixed $address
 * @property mixed $country
 * @property mixed $aeo_company_code
 * @property mixed $contact_name
 * @property mixed $contact_phone
 * @property mixed $keyword
 */
class SftRecordResource extends JsonResource
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
            'type' => $this->type,
            'type_content' => $this->type_content,
            'name' => $this->name,
            'url' => $this->url,
            'is_confirm' => $this->is_confirm,
            'confirm_user' => $this->confirmUser,
            'generate_information' => $this->generate_information,
            'remark' => $this->remark,
            'code' => $this->code,
            'address' => $this->address,
            'country' => $this->country,
            'aeo_company_code' => $this->aeo_company_code,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'keyword' => $this->keyword,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
