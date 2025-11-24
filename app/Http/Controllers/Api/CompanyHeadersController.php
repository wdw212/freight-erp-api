<?php
/**
 * 公司抬头 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyHeaderRequest;
use App\Http\Resources\CompanyHeader\CompanyHeaderInfoResource;
use App\Http\Resources\CompanyHeader\CompanyHeaderResource;
use App\Http\Resources\CompanyType\CompanyTypeResource;
use App\Models\AdminUser;
use App\Models\CompanyHeader;
use App\Notifications\AdminUserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class CompanyHeadersController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $adminUser = $request->user();
        $keyword = $request->input('keyword', '');
        $isPaginate = $request->input('is_paginate', 1);
        $companyType = $request->input('company_type', '');

        $builder = CompanyHeader::query()
            ->with(['adminUser:id,name'])
            ->latest();

        if (!$adminUser->hasRole('超管')) {
            $builder = $builder->where('admin_user_id', $adminUser->id);
        }

        if (!empty($keyword)) {
            $builder = $builder->whereLike('company_name', '%' . $keyword . '%');
        }

        if (!empty($companyType)) {
            $companyType = json_decode($companyType, true);
            if (is_array($companyType)) {
                $builder = $builder->where(function ($query) use ($companyType) {
                    foreach ($companyType as $type) {
                        $query->orWhereJsonContains('company_type', (int)$type);
                    }
                });
            } else {
                $builder = $builder->whereJsonContains('company_type', (int)$companyType);
            }
        }
        if ((int)$isPaginate === 1) {
            $companyHeaders = $builder->paginate();
        } else {
            $companyHeaders = $builder->get();
            CompanyTypeResource::wrap('data');
        }
        return CompanyHeaderResource::collection($companyHeaders);
    }

    /**
     * 新增
     * @param CompanyHeaderRequest $request
     * @param CompanyHeader $companyHeader
     * @return CompanyHeaderInfoResource
     * @throws InvalidRequestException
     */
    public function store(CompanyHeaderRequest $request, CompanyHeader $companyHeader): CompanyHeaderInfoResource
    {
        $adminUser = $request->user();
        $data = $request->all();
        $data['company_type'] = json_decode($data['company_type'], true);
        $builder = CompanyHeader::query()
            ->whereBelongsTo($adminUser)
            ->where('company_name', $data['company_name']);
        // 检测类型是否重复
        $oldCompanyType = $builder->clone()->pluck('company_type')->toArray();
        $oldCompanyType = array_unique(Arr::collapse($oldCompanyType));
        foreach ($data['company_type'] as $type) {
            Log::info($type);
            if (in_array($type, $oldCompanyType)) {
                throw new InvalidRequestException(CompanyHeader::$companyTypeMap[$type] . '重复，请重试！');
            }
        }

        if ($builder->clone()->exists()) {
            throw new InvalidRequestException('当前抬头已存在,请重试！');
        }

//        if (!empty($data['business_user_ids'])) {
//            $data['business_user_ids'] = json_decode($data['business_user_ids'], true);
//            // 校验是否存在
//            if (!empty($data['business_user_ids'])) {
//                foreach (CompanyHeader::$companyTypeMap as $type) {
//                    $oldBusinessUserIds = $builder->clone()->whereJsonContains('company_type', $type)->pluck('business_user_ids')->toArray();
//                    $oldBusinessUserIds = array_unique(Arr::collapse($oldBusinessUserIds));
//                    foreach ($data['business_user_ids'] as $id) {
//                        if (in_array($id, $oldBusinessUserIds)) {
//                            throw new InvalidRequestException('业务员共享重复，请重试！');
//                        }
//                    }
//                }
//            }
//
//        } else {
//            $data['business_user_ids'] = [];
//        }
//
//        if (!empty($data['operation_user_ids'])) {
//            $data['operation_user_ids'] = json_decode($data['operation_user_ids'], true);
//            if (!empty($data['operation_user_ids'])) {
//                foreach (CompanyHeader::$companyTypeMap as $type) {
//                    // 校验是否存在
//                    $oldOperationUserIds = $builder->clone()->whereJsonContains('company_type', $type)->pluck('operation_user_ids')->toArray();
//                    $oldOperationUserIds = array_unique(Arr::collapse($oldOperationUserIds));
//
//                    Log::info('---测试---');
//                    Log::info($oldOperationUserIds);
//                    foreach ($data['operation_user_ids'] as $id) {
//                        if (in_array($id, $oldOperationUserIds)) {
//                            throw new InvalidRequestException('业务员共享重复，请重试！');
//                        }
//                    }
//                }
//            }
//        } else {
//            $data['operation_user_ids'] = [];
//        }
//
//        if (!empty($data['document_user_ids'])) {
//            $data['document_user_ids'] = json_decode($data['document_user_ids'], true);
//            // 校验是否存在
//            if (!empty($data['document_user_ids'])) {
//                foreach (CompanyHeader::$companyTypeMap as $type) {
//                    $oldDocumentUserIds = $builder->clone()->whereJsonContains('company_type', $type)->pluck('document_user_ids')->toArray();
//                    $oldDocumentUserIds = array_unique(Arr::collapse($oldDocumentUserIds));
//                    foreach ($data['operation_user_ids'] as $id) {
//                        if (in_array($id, $oldDocumentUserIds)) {
//                            throw new InvalidRequestException('业务员共享重复，请重试！');
//                        }
//                    }
//                }
//            }
//        } else {
//            $data['document_user_ids'] = [];
//        }
        $companyHeader->fill($data);
        $companyHeader->adminUser()->associate($adminUser);
        $companyHeader->save();
        return new CompanyHeaderInfoResource($companyHeader);
    }

    /**
     * 详情
     * @param CompanyHeader $companyHeader
     * @return CompanyHeaderInfoResource
     */
    public function show(CompanyHeader $companyHeader): CompanyHeaderInfoResource
    {
        return new CompanyHeaderInfoResource($companyHeader);
    }

    /**
     * 编辑
     * @param CompanyHeaderRequest $request
     * @param CompanyHeader $companyHeader
     * @return CompanyHeaderInfoResource
     */
    public function update(CompanyHeaderRequest $request, CompanyHeader $companyHeader): CompanyHeaderInfoResource
    {
        $data = $request->all();

        $data['company_type'] = json_decode($data['company_type'], true);
        if (!empty($data['business_user_ids'])) {
            $data['business_user_ids'] = json_decode($data['business_user_ids'], true);
        } else {
            $data['business_user_ids'] = [];
        }

        if (!empty($data['operation_user_ids'])) {
            $data['operation_user_ids'] = json_decode($data['operation_user_ids'], true);
        } else {
            $data['operation_user_ids'] = [];
        }

        if (!empty($data['document_user_ids'])) {
            $data['document_user_ids'] = json_decode($data['document_user_ids'], true);
        } else {
            $data['document_user_ids'] = [];
        }

        $companyHeader->fill($data);
        $companyHeader->update();
        return new CompanyHeaderInfoResource($companyHeader);
    }

    /**
     * 删除
     * @param CompanyHeader $companyHeader
     * @return Response
     */
    public function destroy(CompanyHeader $companyHeader): Response
    {
        $companyHeader->delete();
        return response()->noContent();
    }

    /**
     * 分享
     * @param Request $request
     * @param CompanyHeader $companyHeader
     * @return JsonResponse
     */
    public function share(Request $request, CompanyHeader $companyHeader): JsonResponse
    {
        $adminUser = $request->user();
        $businessUserIds = $request->input('business_user_ids');
        $operationUserIds = $request->input('operation_user_ids');
        $documentUserIds = $request->input('document_user_ids');

        $businessUserIds = json_decode($businessUserIds, true);
        $operationUserIds = json_decode($operationUserIds, true);
        $documentUserIds = json_decode($documentUserIds, true);

        $adminUserIds = Arr::collapse([$businessUserIds, $operationUserIds, $documentUserIds]);

        foreach ($adminUserIds as $adminUserId) {
            // 判断账号是否存在当前公司抬头
            $oldCompanyHeader = CompanyHeader::query()
                ->where('company_name', $companyHeader->company_name)
                ->where('admin_user_id', $adminUserId)
                ->first();
            if (!$oldCompanyHeader) {
                $replicateCompanyHeader = $companyHeader->replicate();
                $replicateCompanyHeader->adminUser()->associate($adminUserId);
                $replicateCompanyHeader->save();

                // 发送消息通知
                $currentAdminUser = AdminUser::query()->where('id', $adminUserId)->first();
                $currentAdminUser->notify(new AdminUserNotification([
                    'title' => '分享抬头通知',
                    'content' => $adminUser->name . '给你分享了公司抬头:' . $companyHeader->company_name
                ]));
            }


        }
        return response()->json([
            'message' => '分享成功!'
        ]);
    }
}
