<?php

namespace App\Enums;

/**
 * 收支账单分类枚举
 * 展示给前端：中文名称
 * 全部分类：工资、社保、公积金、预付款、购汇美金、结汇美金、日常支出、业务提成、车队高开、直客高开、公司高开、收入押金、支出押金、第三方押金、银行手续费、股东分红、注册资金
 */
enum TransactionCategory: int
{
    case WAGES = 1;  // 工资
    case SOCIAL_SECURITY = 2;  // 社保
    case HOUSING_FUND = 3;  // 公积金
    case DAILY_EXPENSES = 4;  // 日常支出
    case DEPOSIT_OUT = 5;  // 支出押金
    case THIRD_PARTY_DEPOSIT = 6;  // 第三方押金
    case BANK_SERVICE_CHARGE = 7;  // 银行手续费
    case SHAREHOLDER_DIVIDEND = 8;  // 股东分红
    case ADVANCE_PAYMENT = 9;  // 预付款
    case BUSINESS_COMMISSION = 10; // 业务提成
    case FLEET_HIGH_INVOICE = 11; // 车队高开
    case DIRECT_CUSTOMER_INVOICE = 12; // 直客高开
    case COMPANY_HIGH_INVOICE = 13; // 公司高开
    case DEPOSIT_IN = 14; // 收入押金
    case USD_PURCHASE = 15; // 购汇美金
    case USD_SETTLEMENT = 16; // 结汇美金
    case REGISTERED_CAPITAL = 17; // 注册资金

    /**
     * 根据整型值获取中文名称
     */
    public function getName(): string
    {
        return match ($this) {
            self::WAGES => '工资',
            self::SOCIAL_SECURITY => '社保',
            self::HOUSING_FUND => '公积金',
            self::DAILY_EXPENSES => '日常支出',
            self::DEPOSIT_OUT => '支出押金',
            self::THIRD_PARTY_DEPOSIT => '第三方押金',
            self::BANK_SERVICE_CHARGE => '银行手续费',
            self::SHAREHOLDER_DIVIDEND => '股东分红',
            self::ADVANCE_PAYMENT => '预付款',
            self::BUSINESS_COMMISSION => '业务提成',
            self::FLEET_HIGH_INVOICE => '车队高开',
            self::DIRECT_CUSTOMER_INVOICE => '直客高开',
            self::COMPANY_HIGH_INVOICE => '公司高开',
            self::DEPOSIT_IN => '收入押金',
            self::USD_PURCHASE => '购汇美金',
            self::USD_SETTLEMENT => '结汇美金',
            self::REGISTERED_CAPITAL => '注册资金',
        };
    }

    /**
     * @return array
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function ($item) {
            return [$item->value => $item->getName()];
        })->all();
    }
}
