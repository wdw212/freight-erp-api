<?php

namespace App\Observers;

use App\Models\SocialSecurity;

class SocialSecurityObserver
{
    /**
     * Handle the SocialSecurity "created" event.
     */
    public function saving(SocialSecurity $socialSecurity): void
    {
        $totalSocialSecurity = bcadd($socialSecurity->adjusted_base, $socialSecurity->company_makeup, 2);
        $ratio = [
            'pension_company' => 0.16,         // 养老公司
            'unemployment_company' => 0.005,   // 失业公司
            'injury_company' => 0.0055,        // 工伤公司
            'medical_company' => 0.075,        // 医疗公司
            'serious_illness_company' => 0.01, // 大病公司
            'pension_personal' => 0.08,        // 养老个人
            'unemployment_personal' => 0.005,  // 失业个人
            'medical_personal' => 0.02,        // 医疗个人
        ];
        $pensionCompany = bcmul($totalSocialSecurity, $ratio['pension_company'], 2);
        $unemploymentCompany = bcmul($totalSocialSecurity, $ratio['unemployment_company'], 2);
        $injuryCompany = bcmul($totalSocialSecurity, $ratio['injury_company'], 2);
        $medicalCompany = bcmul($totalSocialSecurity, $ratio['medical_company'], 2);
        $seriousIllnessCompany = bcmul($totalSocialSecurity, $ratio['serious_illness_company'], 2);
        $pensionPersonal = bcmul($totalSocialSecurity, $ratio['pension_personal'], 2);
        $unemploymentPersonal = bcmul($totalSocialSecurity, $ratio['unemployment_personal'], 2);
        $medicalPersonal = bcmul($totalSocialSecurity, $ratio['medical_personal'], 2);
        $companyTotal = bcadd(bcadd(bcadd(bcadd($pensionCompany, $unemploymentCompany, 2), $injuryCompany, 2), $medicalCompany, 2), $seriousIllnessCompany, 2);
        $personalTotal = bcadd(bcadd($pensionPersonal, $unemploymentPersonal, 2), $medicalPersonal, 2);
        $totalAll = bcadd($companyTotal, $personalTotal, 2);
        $socialSecurity->fill([
            'pension_company' => $pensionCompany,
            'unemployment_company' => $unemploymentCompany,
            'injury_company' => $injuryCompany,
            'medical_company' => $medicalCompany,
            'serious_illness_company' => $seriousIllnessCompany,
            'company_total' => $companyTotal,
            'pension_personal' => $pensionPersonal,
            'unemployment_personal' => $unemploymentPersonal,
            'medical_personal' => $medicalPersonal,
            'personal_total' => $personalTotal,
            'total_social_security' => $totalAll,
        ]);
    }
}
