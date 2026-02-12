<?php
/**
 * 助手函数
 */

use Illuminate\Support\Carbon;

/**
 * 格式化URl
 * @param string|null $url
 * @return string
 */
function formatUrl(string|null $url): string
{
    if (empty($url)) {
        return '';
    }
    return Storage::url($url);
}

/**
 * 格式化日期
 * @param string|null $date
 * @return string
 */
function formatDate(string|null $date): string
{
    if (empty($date)) {
        return '';
    }
    return Carbon::parse($date)->format('Y-m-d');
}

/**
 * 格式化时间
 * @param $at
 * @param string $format
 * @return string
 */
function formatAt($at, string $format = 'Y-m-d H:i:s'): string
{
    if (empty($at)) {
        return '';
    }
    return Carbon::parse($at)->format($format);
}

/**
 * 格式化图片
 * @param string|null $image
 * @return array|string
 */
function formatFullUrl(string|null $image): array|string
{
    if (empty($image)) {
        return '';
    }
    return [
        'path' => $image,
        'url' => Storage::url($image),
    ];
}

/**
 * 格式化文件大小
 * @param $bytes
 * @return string
 */
function formatFileSize($bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $unitIndex = 0;

    while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
        $bytes /= 1024;
        $unitIndex++;
    }
    return number_format($bytes, 2) . ' ' . $units[$unitIndex];
}

/**
 * 获取url path
 * @param string $url
 * @return string
 */
function getUrlPath(string $url): string
{
    $url = str_starts_with($url, '//') ? "http:$url" : $url;
    $parsedUrl = parse_url($url);
    return $parsedUrl['path'] ?? '';
}

/**
 * 计算税额
 * 公式：税额 = 总金额 - 总金额 / (1 + 税率)
 * @param float|int|string $totalAmount 总金额（支持数字/字符串）
 * @param float|int $taxRate 税率（如13%传13，1%传1，需大于0）
 * @param int $decimal 保留小数位数（默认2位）
 * @return string 计算后的税额（格式化字符串，空值返回''）
 */
function calculateTaxAmount(float|int|string $totalAmount, float|int $taxRate, int $decimal = 2): string
{
    // 空值校验（贴合现有助手函数的空值返回逻辑）
    if (empty($totalAmount) || empty($taxRate) || $taxRate <= 0) {
        return '';
    }

    // 类型转换，确保数值格式正确
    $totalAmount = floatval($totalAmount);
    $taxRate = floatval($taxRate);

    // 核心计算逻辑
    $taxAmount = $totalAmount - ($totalAmount / (1 + $taxRate / 100));

    // 保留指定小数位并格式化（符合财务展示规范）
    return number_format($taxAmount, $decimal);
}

/**
 * 人民币（CNY）兑换美金（USD）
 * @param float|int|string $cnyAmount 人民币金额（支持数字/字符串）
 * @param float|int $exchangeRate 汇率（1CNY可兑换的USD数，默认参考实时汇率≈0.138）
 * @param int $decimal 保留小数位数（默认2位，符合美金展示规范）
 * @return string 兑换后的美金金额（格式化字符串，空值/非法值返回''）
 */
function cnyToUsd(float|int|string $cnyAmount, float|int $exchangeRate = 0.138, int $decimal = 2): string
{
    // 空值/非法值校验
    if (empty($cnyAmount) || $cnyAmount < 0 || $exchangeRate <= 0) {
        return '';
    }

    // 类型转换，确保数值格式正确
    $cnyAmount = floatval($cnyAmount);
    $exchangeRate = floatval($exchangeRate);

    // 核心计算逻辑：美金金额 = 人民币金额 × 汇率
    $usdAmount = $cnyAmount * $exchangeRate;

    // 保留指定小数位并格式化
    return number_format($usdAmount, $decimal);
}
