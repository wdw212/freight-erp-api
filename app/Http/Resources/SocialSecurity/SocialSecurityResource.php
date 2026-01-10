<?php

namespace App\Http\Resources\SocialSecurity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $name
 * @property mixed $id_card
 * @property mixed $phone
 * @property mixed $person_type
 * @property mixed $adjusted_base
 * @property mixed $company_makeup
 * @property mixed $total_social_security
 * @property mixed $pension_company
 * @property mixed $unemployment_company
 * @property mixed $injury_company
 * @property mixed $medical_company
 * @property mixed $serious_illness_company
 * @property mixed $company_total
 * @property mixed $pension_personal
 * @property mixed $unemployment_personal
 * @property mixed $medical_personal
 * @property mixed $personal_total
 * @property mixed $created_at
 */
class SocialSecurityResource extends JsonResource
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
            'name' => $this->name,
            'id_card' => $this->id_card,
            'phone' => $this->phone,
            'person_type' => $this->person_type,
            'adjusted_base' => $this->adjusted_base,
            'company_makeup' => $this->company_makeup,
            'total_social_security' => $this->total_social_security,
            'pension_company' => $this->pension_company,
            'unemployment_company' => $this->unemployment_company,
            'injury_company' => $this->injury_company,
            'medical_company' => $this->medical_company,
            'serious_illness_company' => $this->serious_illness_company,
            'company_total' => $this->company_total,
            'pension_personal' => $this->pension_personal,
            'unemployment_personal' => $this->unemployment_personal,
            'medical_personal' => $this->medical_personal,
            'personal_total' => $this->personal_total,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
