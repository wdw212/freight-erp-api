<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyContactRequest;
use App\Http\Resources\CompanyContact\CompanyContactInfoResource;
use App\Http\Resources\CompanyContact\CompanyContactResource;
use App\Models\CompanyContact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CompanyContactsController extends Controller
{
    /**
     * 列表
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $companyContacts = CompanyContact::query()
            ->with('department:id,name')
            ->orderByDesc('id')
            ->paginate();
        return CompanyContactResource::collection($companyContacts);
    }

    /**
     * 新增
     * @param CompanyContactRequest $request
     * @param CompanyContact $companyContact
     * @return CompanyContactInfoResource
     */
    public function store(CompanyContactRequest $request, CompanyContact $companyContact): CompanyContactInfoResource
    {
        $companyContact->fill($request->all());
        $companyContact->save();
        return new CompanyContactInfoResource($companyContact);
    }

    /**
     * 详情
     * @param CompanyContact $companyContact
     * @return CompanyContactInfoResource
     */
    public function show(CompanyContact $companyContact)
    {
        return new CompanyContactInfoResource($companyContact);
    }

    /**
     * 编辑
     * @param CompanyContactRequest $request
     * @param CompanyContact $companyContact
     * @return CompanyContactInfoResource
     */
    public function update(CompanyContactRequest $request, CompanyContact $companyContact): CompanyContactInfoResource
    {
        $companyContact->fill($request->all());
        $companyContact->update();
        return new CompanyContactInfoResource($companyContact);
    }

    /**
     * 删除
     * @param CompanyContact $companyContact
     * @return Response
     */
    public function destroy(CompanyContact $companyContact): Response
    {
        $companyContact->delete();
        return response()->noContent();
    }
}
