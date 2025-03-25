<?php
/**
 * 公司合同 Controller
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyContractRequest;
use App\Http\Resources\CompanyContract\CompanyContractInfoResource;
use App\Http\Resources\CompanyContract\CompanyContractResource;
use App\Models\CompanyContract;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CompanyContractsController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $companyContracts = CompanyContract::query()
            ->with(['companyHeader:id,company_name', 'seller:id,name'])
            ->orderByDesc('created_at')
            ->paginate();
        return CompanyContractResource::collection($companyContracts);
    }

    /**
     * 新增
     * @param CompanyContractRequest $request
     * @param CompanyContract $companyContract
     * @return CompanyContractInfoResource
     * @throws InvalidRequestException
     */
    public function store(CompanyContractRequest $request, CompanyContract $companyContract): CompanyContractInfoResource
    {
        $no = $request->input('no');

        if (CompanyContract::query()->where('no', $no)->exists()) {
            throw new InvalidRequestException('合同编号已存在，请重试！');
        }

        $companyContract->fill($request->all());
        $companyContract->save();
        return new CompanyContractInfoResource($companyContract);
    }

    /**
     * 详情
     * @param CompanyContract $companyContract
     * @return CompanyContractInfoResource
     */
    public function show(CompanyContract $companyContract): CompanyContractInfoResource
    {
        return new CompanyContractInfoResource($companyContract);
    }

    /**
     * 编辑
     * @param CompanyContractRequest $request
     * @param CompanyContract $companyContract
     * @return CompanyContractInfoResource
     * @throws InvalidRequestException
     */
    public function update(CompanyContractRequest $request, CompanyContract $companyContract): CompanyContractInfoResource
    {
        $no = $request->input('no');

        if (CompanyContract::query()->whereNot('id', $companyContract->id)->where('no', $no)->exists()) {
            throw new InvalidRequestException('合同编号已存在，请重试！');
        }
        $companyContract->fill($request->all());
        $companyContract->update();
        return new CompanyContractInfoResource($companyContract);
    }

    /**
     * 删除
     * @param CompanyContract $companyContract
     * @return Response
     */
    public function destroy(CompanyContract $companyContract): Response
    {
        $companyContract->delete();
        return response()->noContent();
    }
}
