<?php
/**
 *  文件 Controller
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderFile\OrderFileResource;
use App\Models\OrderFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class OrderFilesController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $file = $request->input('file');
        $companyHeaderId = $request->input('company_header_id');
        $builder = OrderFile::query()->with([
            'order:id,business_user_id',
            'order.businessUser:id,name',
        ])->orderByDesc('created_at');
        if ($file) {
            $builder = $builder->whereLike('file', '%' . $file . '%');
        }
        if ($companyHeaderId) {
            $builder = $builder->withWhereHas('order.orderDelegationHeader', function ($query) use ($companyHeaderId) {
                $query->with('companyHeader:id,company_name')->where('company_header_id', $companyHeaderId);
            });
        }
        $orderFiles = $builder->paginate();
        return OrderFileResource::collection($orderFiles);
    }

    /**
     * 删除
     * @param OrderFile $orderFile
     * @return Response
     */
    public function destroy(OrderFile $orderFile): Response
    {
        $orderFile->delete();
        return response()->noContent();
    }
}
