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
