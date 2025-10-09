<?php
/**
 * 上传 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadsController extends Controller
{
    /**
     * 上传文件
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function file(Request $request): JsonResponse
    {
        $file = $request->file('file');
        if (empty($file)) {
            throw new InvalidRequestException('上传文件不能为空');
        }

        // 获取是否保留原名的参数（默认不保留）
        $keepOriginalName = $request->input('keep_original_name', 0);

        // 获取文件扩展名
        $extension = strtolower($file->getClientOriginalExtension());

        // 根据选项生成文件名
        if ($keepOriginalName) {
            // 保留原名（移除扩展名后拼接，避免重复添加）
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // 处理特殊字符，确保文件名安全
            $filename = $originalName . '.' . $extension;
            // 可选：若文件已存在，添加时间戳避免覆盖
            if (Storage::exists($filename)) {
                $filename = $originalName . '_' . time() . '.' . $extension;
            }
        } else {
            // 生成随机文件名（原逻辑）
            $filename = time() . '_' . Str::random(10) . '.' . $extension;
        }
        Storage::put($filename, $file->getContent());
        return response()->json([
            'path' => $filename,
            'url' => formatUrl($filename),
        ]);
    }
}
