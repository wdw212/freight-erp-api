<?php
/**
 * 船公司 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingCompanyRequest;
use App\Http\Resources\ShippingCompany\ShippingCompanyInfoResource;
use App\Http\Resources\ShippingCompany\ShippingCompanyResource;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ShippingCompaniesController extends Controller
{
    /**
     * 列表
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $shippingCompanies = ShippingCompany::query()
            ->orderByDesc('id')
            ->paginate();
        return ShippingCompanyResource::collection($shippingCompanies);
    }

    /**
     * 详情
     * @param ShippingCompanyRequest $request
     * @param ShippingCompany $shippingCompany
     * @return ShippingCompanyInfoResource
     */
    public function store(ShippingCompanyRequest $request, ShippingCompany $shippingCompany): ShippingCompanyInfoResource
    {
        $shippingCompany->fill($request->all());
        $shippingCompany->save();
        return new ShippingCompanyInfoResource($shippingCompany);
    }

    /**
     * 详情
     * @param ShippingCompany $shippingCompany
     * @return ShippingCompanyInfoResource
     */
    public function show(ShippingCompany $shippingCompany): ShippingCompanyInfoResource
    {
        return new ShippingCompanyInfoResource($shippingCompany);
    }

    /**
     * 编辑
     * @param ShippingCompanyRequest $request
     * @param ShippingCompany $shippingCompany
     * @return ShippingCompanyInfoResource
     */
    public function update(ShippingCompanyRequest $request, ShippingCompany $shippingCompany): ShippingCompanyInfoResource
    {
        $shippingCompany->fill($request->all());
        $shippingCompany->update();
        return new ShippingCompanyInfoResource($shippingCompany);
    }

    /**
     * 删除
     * @param ShippingCompany $shippingCompany
     * @return Response
     */
    public function destroy(ShippingCompany $shippingCompany): Response
    {
        $shippingCompany->delete();
        return response()->noContent();
    }
}
