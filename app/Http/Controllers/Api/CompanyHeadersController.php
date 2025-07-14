<?php
/**
 * 公司抬头 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyHeaderRequest;
use App\Http\Resources\CompanyHeader\CompanyHeaderInfoResource;
use App\Http\Resources\CompanyHeader\CompanyHeaderResource;
use App\Http\Resources\CompanyType\CompanyTypeResource;
use App\Models\CompanyHeader;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

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
        $operationUserId = $request->input('operation_user_id');
        $documentUserId = $request->input('document_user_id');
        $isPaginate = $request->input('is_paginate', 1);
        $companyType = $request->input('company_type', '');

        $builder = CompanyHeader::query()->whereBelongsTo($adminUser)->with(['adminUser:id,name'])->latest();

        if (!empty($keyword)) {
            $builder = $builder->whereLike('company_name', '%' . $keyword . '%');
        }

        if (!empty($operationUserId)) {
            $builder = $builder->where('operation_user_id', $operationUserId);
        }
        if (!empty($documentUserId)) {
            $builder = $builder->where('document_user_id', $documentUserId);
        }

        if (!empty($companyType)) {
            $builder = $builder->orWhere(function ($query) use ($companyType) {
                $companyType = json_decode($companyType, true);
                foreach ($companyType as $type) {
                    $query->orWhereJsonContains('company_type', $type);
                }
            });
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
     */
    public function store(CompanyHeaderRequest $request, CompanyHeader $companyHeader): CompanyHeaderInfoResource
    {
        $adminUser = $request->user();
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
}
