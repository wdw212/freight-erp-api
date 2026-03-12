<?php

namespace App\Support;

use Closure;

class InvoiceTypeValueResolver
{
    private const SNAP_PREFIX = '__snap_';

    /**
     * 兼容多种下拉回传格式，统一解析出发票类型 ID
     *
     * @param mixed $value
     * @param Closure(string): (int|string|null)|null $nameResolver
     * @return int|null
     */
    public static function resolveInvoiceTypeId(mixed $value, ?Closure $nameResolver = null): ?int
    {
        $scalarValue = self::extractScalarValue($value);

        if ($scalarValue === null) {
            return null;
        }

        if (is_int($scalarValue) || is_float($scalarValue) || (is_string($scalarValue) && is_numeric($scalarValue))) {
            $id = (int)$scalarValue;
            return $id > 0 ? $id : null;
        }

        if (!is_string($scalarValue)) {
            return null;
        }

        $name = trim($scalarValue);
        if ($name === '' || $nameResolver === null) {
            return null;
        }

        $resolvedId = $nameResolver($name);
        if ($resolvedId === null || $resolvedId === '') {
            return null;
        }

        $id = (int)$resolvedId;
        return $id > 0 ? $id : null;
    }

    /**
     * 提取下拉值中的标量内容（兼容对象/数组）
     *
     * @param mixed $value
     * @return float|int|string|null
     */
    private static function extractScalarValue(mixed $value): float|int|string|null
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }

        if (is_string($value)) {
            $normalized = trim($value);
            if (str_starts_with($normalized, self::SNAP_PREFIX)) {
                $snapshotId = substr($normalized, strlen(self::SNAP_PREFIX));
                if ($snapshotId !== '' && is_numeric($snapshotId)) {
                    return (int)$snapshotId;
                }
            }

            return $normalized;
        }

        if (is_array($value)) {
            foreach (['id', 'value', 'key'] as $key) {
                if (array_key_exists($key, $value)) {
                    return self::extractScalarValue($value[$key]);
                }
            }

            return null;
        }

        if (is_object($value)) {
            return self::extractScalarValue(get_object_vars($value));
        }

        return null;
    }
}
