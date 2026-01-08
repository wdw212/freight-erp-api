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
        $keepOriginalName = $request->input('keep_original_name', 0);
        $extension = strtolower($file->getClientOriginalExtension());
        if ($keepOriginalName) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = $originalName . '.' . $extension;
            if (Storage::exists($filename)) {
                $filename = $originalName . '_' . time() . '.' . $extension;
            }
        } else {
            $filename = time() . '_' . Str::random(10) . '.' . $extension;
        }
        Storage::put($filename, $file->getContent());
        return response()->json([
            'path' => $filename,
            'url' => formatUrl($filename),
        ]);
    }
}
