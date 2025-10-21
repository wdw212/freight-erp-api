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
 * @param string $image
 * @return array|string
 */
function formatFullUrl(string $image): array|string
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
