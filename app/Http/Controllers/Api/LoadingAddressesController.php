<?php
/**
 * 装箱地址 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoadingAddressRequest;
use App\Http\Resources\LoadingAddress\LoadingAddressInfoResource;
use App\Http\Resources\LoadingAddress\LoadingAddressResource;
use App\Models\AdminUser;
use App\Models\LoadingAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\password;

class LoadingAddressesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $adminUser = $request->user();
        $isPaginate = $request->input('is_paginate', 1);
        $keyword = $request->input('keyword', '');
        $businessUserId = $request->input('business_user_id');
        $operationUserId = $request->input('operation_user_id');
        $documentUserId = $request->input('document_user_id');

        $builder = LoadingAddress::query()
            ->with(['region:id,name', 'adminUser:id,name'])
            ->latest();

        if (!$adminUser->hasRole('超管')) {
            // 如果不是超管，隔离数据
            $builder = $builder->where(function ($query) use ($adminUser) {
                $query->whereJsonContains('business_user_ids', $adminUser->id)
                    ->orWhereJsonContains('operation_user_ids', $adminUser->id)
                    ->orWhereJsonContains('document_user_ids', $adminUser->id);
            });
        }

        if (!empty($keyword)) {
            $builder = $builder->whereLike('keyword', '%' . $keyword . '%')
                ->orWhereLike('address', '%' . $keyword . '%')
                ->orWhereLike('contact_name', '%' . $keyword . '%')
                ->orWhereLike('phone', '%' . $keyword . '%');
        }

        if (!empty($businessUserId)) {
            $builder = $builder->whereJsonContains('business_user_ids', (int)$businessUserId);
        }
        if (!empty($operationUserId)) {
            $builder = $builder->whereJsonContains('operation_user_ids', (int)$operationUserId);
        }
        if (!empty($documentUserId)) {
            $builder = $builder->whereJsonContains('document_user_ids', (int)$documentUserId);
        }

        if ($isPaginate) {
            $loadingAddresses = $builder->paginate();
        } else {
            $loadingAddresses = $builder->get();
            LoadingAddressResource::wrap('data');
        }
        return LoadingAddressResource::collection($loadingAddresses);
    }

    /**
     * 新增
     * @param LoadingAddressRequest $request
     * @param LoadingAddress $loadingAddress
     * @return LoadingAddressInfoResource
     * @throws InvalidRequestException
     */
    public function store(LoadingAddressRequest $request, LoadingAddress $loadingAddress): LoadingAddressInfoResource
    {
        $data = $request->all();
        $adminUser = $request->user();

        $oldLoadingAddress = LoadingAddress::query()
            ->where('address', $data['address'])
            ->where(function ($query) use ($adminUser) {
                $query->whereJsonContains('business_user_ids', $adminUser->id)
                    ->orWhereJsonContains('operation_user_ids', $adminUser->id)
                    ->orWhereJsonContains('document_user_ids', $adminUser->id);
            })
            ->first();

        if ($oldLoadingAddress) {
            throw new InvalidRequestException('地址已存在!');
        }
        if (!empty($data['business_user_ids'])) {
            if (!is_array($data['business_user_ids'])) {
                $data['business_user_ids'] = json_decode($data['business_user_ids'], true);

                if (is_array($data['business_user_ids'])) {
                    foreach ($data['business_user_ids'] as $businessUserId) {
                        $adminUser = AdminUser::query()->where('id', $businessUserId)->first();
                        $oldLoadingAddress = LoadingAddress::query()
                            ->where('address', $data['address'])
                            ->where(function ($query) use ($adminUser) {
                                $query->whereJsonContains('business_user_ids', $adminUser->id)
                                    ->orWhereJsonContains('operation_user_ids', $adminUser->id)
                                    ->orWhereJsonContains('document_user_ids', $adminUser->id);
                            })
                            ->first();
                        if ($oldLoadingAddress) {
                            throw new InvalidRequestException('业务员:' . $adminUser->name . '已拥有,请重试!');
                        }
                    }
                }

            }
        } else {
            $data['business_user_ids'] = [];
        }
        if (!empty($data['operation_user_ids'])) {
            if (!is_array($data['operation_user_ids'])) {
                $data['operation_user_ids'] = json_decode($data['operation_user_ids'], true);
                if (is_array($data['operation_user_ids'])) {
                    foreach ($data['operation_user_ids'] as $operationUserId) {
                        $adminUser = AdminUser::query()->where('id', $operationUserId)->first();
                        $oldLoadingAddress = LoadingAddress::query()
                            ->where('address', $data['address'])
                            ->where(function ($query) use ($adminUser) {
                                $query->whereJsonContains('business_user_ids', $adminUser->id)
                                    ->orWhereJsonContains('operation_user_ids', $adminUser->id)
                                    ->orWhereJsonContains('document_user_ids', $adminUser->id);
                            })
                            ->first();
                        if ($oldLoadingAddress) {
                            throw new InvalidRequestException('操作员:' . $adminUser->name . '已拥有,请重试!');
                        }
                    }
                }

            }
        } else {
            $data['operation_user_ids'] = [];
        }

        if (!empty($data['document_user_ids'])) {
            if (!is_array($data['document_user_ids'])) {
                $data['document_user_ids'] = json_decode($data['document_user_ids'], true);

                if (is_array($data['document_user_ids'])) {
                    foreach ($data['document_user_ids'] as $documentUserId) {
                        $adminUser = AdminUser::query()->where('id', $documentUserId)->first();
                        $oldLoadingAddress = LoadingAddress::query()
                            ->where('address', $data['address'])
                            ->where(function ($query) use ($adminUser) {
                                $query->whereJsonContains('business_user_ids', $adminUser->id)
                                    ->orWhereJsonContains('operation_user_ids', $adminUser->id)
                                    ->orWhereJsonContains('document_user_ids', $adminUser->id);
                            })
                            ->first();
                        if ($oldLoadingAddress) {
                            throw new InvalidRequestException('单证员:' . $adminUser->name . '已拥有,请重试!');
                        }
                    }
                }

            }
        } else {
            $data['document_user_ids'] = [];
        }

        $loadingAddress->fill($data);
        $loadingAddress->adminUser()->associate($adminUser);
        $loadingAddress->save();
        return new LoadingAddressInfoResource($loadingAddress);
    }

    /**
     * 详情
     * @param LoadingAddress $loadingAddress
     * @return LoadingAddressInfoResource
     */
    public function show(LoadingAddress $loadingAddress): LoadingAddressInfoResource
    {
        return new LoadingAddressInfoResource($loadingAddress);
    }

    /**
     * 编辑
     * @param LoadingAddressRequest $request
     * @param LoadingAddress $loadingAddress
     * @return LoadingAddressInfoResource
     * @throws InvalidRequestException
     */
    public function update(LoadingAddressRequest $request, LoadingAddress $loadingAddress): LoadingAddressInfoResource
    {
        $adminUser = $request->user();
        $data = $request->all();

        if (!empty($data['business_user_ids'])) {
            $data['business_user_ids'] = json_decode($data['business_user_ids'], true);

            if ($adminUser->id !== $loadingAddress->admin_user_id) {
                foreach ($data['business_user_ids'] as $businessUserId) {
                    if ($businessUserId !== $adminUser->id) {
                        $currentAdminUser = AdminUser::query()->where('id', $businessUserId)->first();
                        $oldLoadingAddress = LoadingAddress::query()
                            ->where('address', $data['address'])
                            ->where(function ($query) use ($currentAdminUser) {
                                $query->whereJsonContains('business_user_ids', $currentAdminUser->id)
                                    ->orWhereJsonContains('operation_user_ids', $currentAdminUser->id)
                                    ->orWhereJsonContains('document_user_ids', $currentAdminUser->id);
                            })
                            ->first();
                        if ($oldLoadingAddress) {
                            throw new InvalidRequestException('业务员:' . $adminUser->name . '已拥有,请重试!');
                        }
                    }
                }
            }
        } else {
            $data['business_user_ids'] = [];
        }

        if (!empty($data['operation_user_ids'])) {
            $data['operation_user_ids'] = json_decode($data['operation_user_ids'], true);

            if (is_array($data['operation_user_ids']) && $adminUser->id !== $loadingAddress->admin_user_id) {
                foreach ($data['operation_user_ids'] as $operationUserId) {
                    $currentAdminUser = AdminUser::query()->where('id', $operationUserId)->first();
                    $oldLoadingAddress = LoadingAddress::query()
                        ->where('address', $data['address'])
                        ->where(function ($query) use ($currentAdminUser) {
                            $query->whereJsonContains('business_user_ids', $currentAdminUser->id)
                                ->orWhereJsonContains('operation_user_ids', $currentAdminUser->id)
                                ->orWhereJsonContains('document_user_ids', $currentAdminUser->id);
                        })
                        ->first();
                    if ($oldLoadingAddress) {
                        throw new InvalidRequestException('操作员:' . $adminUser->name . '已拥有,请重试!');
                    }
                }
            }
        } else {
            $data['operation_user_ids'] = [];
        }

        if (!empty($data['document_user_ids'])) {
            $data['document_user_ids'] = json_decode($data['document_user_ids'], true);

            if ($adminUser->id !== $loadingAddress->admin_user_id) {
                foreach ($data['document_user_ids'] as $documentUserId) {
                    $currentAdminUser = AdminUser::query()->where('id', $documentUserId)->first();
                    $oldLoadingAddress = LoadingAddress::query()
                        ->where('address', $data['address'])
                        ->where(function ($query) use ($currentAdminUser) {
                            $query->whereJsonContains('business_user_ids', $currentAdminUser->id)
                                ->orWhereJsonContains('operation_user_ids', $currentAdminUser->id)
                                ->orWhereJsonContains('document_user_ids', $currentAdminUser->id);
                        })
                        ->first();
                    if ($oldLoadingAddress) {
                        throw new InvalidRequestException('单证员:' . $adminUser->name . '已拥有,请重试!');
                    }
                }
            }
        } else {
            $data['document_user_ids'] = [];
        }
        $loadingAddress->fill($data);
        $loadingAddress->update();
        return new LoadingAddressInfoResource($loadingAddress);
    }

    /**
     * 删除
     * @param Request $request
     * @param LoadingAddress $loadingAddress
     * @return Response
     * @throws InvalidRequestException
     */
    public function destroy(Request $request, LoadingAddress $loadingAddress): Response
    {
        $adminUser = $request->user();

        if ((int)$adminUser->id !== (int)$loadingAddress->admin_user_id) {
            throw new InvalidRequestException('只能创建人删除');
        }

        $loadingAddress->delete();
        return response()->noContent();
    }
}
