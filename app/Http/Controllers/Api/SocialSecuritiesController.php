<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialSecurityRequest;
use App\Http\Resources\SocialSecurity\SocialSecurityInfoResource;
use App\Http\Resources\SocialSecurity\SocialSecurityResource;
use App\Models\SocialSecurity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SocialSecuritiesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $keyword = $request->input('keyword');
        $builder = SocialSecurity::query()->latest();
        if (!empty($keyword)) {
            $builder = $builder->where(function ($query) use ($keyword) {
                $query->whereLike('name', '%' . $keyword . '%')
                    ->orWhereLike('id_card', '%' . $keyword . '%')
                    ->orWhereLike('phone', '%' . $keyword . '%');
            });
        }
        $socialSecurities = $builder->paginate();
        return SocialSecurityResource::collection($socialSecurities);
    }

    /**
     * 新增
     * @param SocialSecurityRequest $request
     * @param SocialSecurity $socialSecurity
     * @return SocialSecurityInfoResource
     */
    public function store(SocialSecurityRequest $request, SocialSecurity $socialSecurity): SocialSecurityInfoResource
    {
        $socialSecurity->fill($request->all());
        $socialSecurity->save();
        return new SocialSecurityInfoResource($socialSecurity);
    }

    /**
     * 详情
     * @param SocialSecurity $socialSecurity
     * @return SocialSecurityInfoResource
     */
    public function show(SocialSecurity $socialSecurity): SocialSecurityInfoResource
    {
        return new SocialSecurityInfoResource($socialSecurity);
    }

    /**
     * 编辑
     * @param Request $request
     * @param SocialSecurity $socialSecurity
     * @return SocialSecurityInfoResource
     */
    public function update(Request $request, SocialSecurity $socialSecurity): SocialSecurityInfoResource
    {
        $socialSecurity->fill($request->all());
        $socialSecurity->update();
        return new SocialSecurityInfoResource($socialSecurity);
    }

    /**
     * 删除
     * @param SocialSecurity $socialSecurity
     * @return Response
     */
    public function destroy(SocialSecurity $socialSecurity): Response
    {
        $socialSecurity->delete();
        return response()->noContent();
    }
}
