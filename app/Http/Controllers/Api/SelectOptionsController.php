<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\CompanyType;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SelectOptionsController extends Controller
{
    /**
     * 详情
     * @param $key
     * @return JsonResponse
     */
    public function index($key): JsonResponse
    {
        $list = match ($key) {
            'ADMIN_USER' => AdminUser::query()->get(['id', 'name'])->toArray(),
            'DEPARTMENT' => Department::query()->get(['id', 'name'])->toArray(),
            'COMPANY_TYPE' => CompanyType::query()->get(['id', 'name'])->toArray(),
            default => [],
        };
        return response()->json([
            'data' => $list,
        ]);
    }
}
