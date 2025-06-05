<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SftRecordRequest;
use App\Http\Resources\SftRecord\SftRecordInfoResource;
use App\Http\Resources\SftRecord\SftRecordResource;
use App\Models\SftRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SftRecordsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $keyword = $request->input('keyword');
        $type = $request->input('type');
        $isConfirm = $request->input('is_confirm');
        $operationUserId = $request->input('operation_user_id');
        $documentUserId = $request->input('document_user_id');
        $commerceUserId = $request->input('commerce_user_id');
        $builder = SftRecord::query()
            ->with(['confirmUser:id,name'])
            ->latest();

        if (!empty($keyword)) {
            $builder = $builder->whereLike('name', '%' . $keyword . '%')
                ->orWhereLike('remark', '%' . $keyword . '%');
        }
        if (!empty($type)) {
            $builder = $builder->where('type', $type);
        }

        if (!empty($isConfirm)) {
            $builder = $builder->where('is_confirm', $isConfirm);
        }

        if (!empty($operationUserId)) {
            $builder = $builder->whereJsonContains('operation_user_id', (string)$operationUserId);
        }
        if (!empty($documentUserId)) {
            $builder = $builder->whereJsonContains('document_user_id', (string)$documentUserId);
        }
        if (!empty($commerceUserId)) {
            $builder = $builder->whereJsonContains('commerce_user_id', (string)$commerceUserId);
        }

        $sftRecords = $builder->paginate();
        return SftRecordResource::collection($sftRecords);
    }

    /**
     * 新增
     * @param SftRecordRequest $request
     * @param SftRecord $sftRecord
     * @return SftRecordInfoResource
     * @throws InvalidRequestException
     */
    public function store(SftRecordRequest $request, SftRecord $sftRecord): SftRecordInfoResource
    {
        $user = $request->user();
        $data = $request->all();

        if (SftRecord::query()->where('name', $data['name'])->exists()) {
            throw new InvalidRequestException('当前名称已存在!');
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
        if (!empty($data['commerce_user_ids'])) {
            $data['commerce_user_ids'] = json_decode($data['commerce_user_ids'], true);
        } else {
            $data['commerce_user_ids'] = [];
        }
        if ((int)$data['is_confirm'] === 0) {
            $data['confirm_user_id'] = null;
        } else {
            $data['confirm_user_id'] = $user->id;
        }
        $sftRecord->fill($data);
        $sftRecord->save();
        return new SftRecordInfoResource($sftRecord);
    }

    /**
     * 详情
     * @param SftRecord $sftRecord
     * @return SftRecordInfoResource
     */
    public function show(SftRecord $sftRecord): SftRecordInfoResource
    {
        return new SftRecordInfoResource($sftRecord);
    }

    /**
     * 新增
     * @param SftRecordRequest $request
     * @param SftRecord $sftRecord
     * @return SftRecordInfoResource
     * @throws InvalidRequestException
     */
    public function update(SftRecordRequest $request, SftRecord $sftRecord): SftRecordInfoResource
    {
        $user = $request->user();
        $data = $request->all();
        if (SftRecord::query()->where('name', $data['name'])->whereNot('id', $sftRecord->id)->exists()) {
            throw new InvalidRequestException('当前名称已存在!');
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
        if (!empty($data['commerce_user_ids'])) {
            $data['commerce_user_ids'] = json_decode($data['commerce_user_ids'], true);
        } else {
            $data['commerce_user_ids'] = [];
        }

        if ((int)$data['is_confirm'] === 0) {
            $data['confirm_user_id'] = null;
        } else {
            $data['confirm_user_id'] = $user->id;
        }
        $sftRecord->fill($data);
        $sftRecord->update();
        return new SftRecordInfoResource($sftRecord);
    }

    /**
     * 删除
     * @param SftRecord $sftRecord
     * @return Response
     */
    public function destroy(SftRecord $sftRecord): Response
    {
        $sftRecord->delete();
        return response()->noContent();
    }
}
