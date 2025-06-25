<?php
/**
 * 助手函数
 */

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
    return \Carbon\Carbon::parse($date)->format('Y-m-d');
}
