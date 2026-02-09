<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\Transaction\TransactionInfoResource;
use App\Http\Resources\Transaction\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TransactionsController extends Controller
{
    /**
     * 用途分类
     * @return JsonResponse
     */
    public function category(): JsonResponse
    {
        return response()->json([
            'data' => TransactionCategory::options()
        ]);
    }

    /**
     * 列表
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $transactions = Transaction::query()
            ->with('seller:id,name')
            ->latest()
            ->paginate();
        return TransactionResource::collection($transactions);
    }

    /**
     * 新增
     * @param TransactionRequest $request
     * @param Transaction $transaction
     * @return TransactionInfoResource
     */
    public function store(TransactionRequest $request, Transaction $transaction): TransactionInfoResource
    {
        $transaction->fill($request->all());
        $transaction->save();
        return new TransactionInfoResource($transaction);
    }

    /**
     * 详情
     * @param Transaction $transaction
     * @return TransactionInfoResource
     */
    public function show(Transaction $transaction): TransactionInfoResource
    {
        return new TransactionInfoResource($transaction);
    }

    /**
     * 编辑
     * @param TransactionRequest $request
     * @param Transaction $transaction
     * @return TransactionInfoResource
     */
    public function update(TransactionRequest $request, Transaction $transaction): TransactionInfoResource
    {
        $transaction->fill($request->all());
        $transaction->save();
        return new TransactionInfoResource($transaction);
    }

    /**
     * 删除
     * @param Transaction $transaction
     * @return Response
     */
    public function destroy(Transaction $transaction): Response
    {
        $transaction->delete();
        return response()->noContent();
    }

    /**
     * 公帐
     * @param Request $request
     * @return JsonResponse
     */
    public function publicAccount(Request $request): JsonResponse
    {
        $builder = Transaction::query()->where('type', 'public');
        $incomeCnyAmount = $builder->clone()->sum('income_cny');
        $expenseCnyAmount = $builder->clone()->sum('expense_cny');
        $totalCnyAmount = $incomeCnyAmount - $expenseCnyAmount;

        $incomeUsdAmount = $builder->clone()->sum('income_usd');
        $expenseUsdAmount = $builder->clone()->sum('expense_usd');
        $totalUsdAmount = $incomeUsdAmount - $expenseUsdAmount;
        $data = [
            'cny' => [
                'total_amount' => $totalCnyAmount,
                'cut_off_amount' => 0,
                'cut_off_income_amount' => $incomeCnyAmount,
                'cut_off_expense_amount' => $expenseCnyAmount,
            ],
            'usd' => [
                'total_amount' => $totalUsdAmount,
                'cut_off_amount' => 0,
                'cut_off_income_amount' => $incomeUsdAmount,
                'cut_off_expense_amount' => $expenseUsdAmount,
            ],
            'income_cny_amount' => $incomeCnyAmount,
            'expense_cny_amount' => $expenseCnyAmount,
            'income_usd_amount' => $incomeUsdAmount,
            'expense_usd_amount' => $expenseUsdAmount,
        ];
        return response()->json($data);
    }

    /**
     * 私账
     * @param Request $request
     * @return JsonResponse
     */
    public function privateAccount(Request $request): JsonResponse
    {
        $builder = Transaction::query()->where('type', 'private');
        $incomeCnyAmount = $builder->clone()->sum('income_cny');
        $expenseCnyAmount = $builder->clone()->sum('expense_cny');
        $totalCnyAmount = $incomeCnyAmount - $expenseCnyAmount;
        $incomeUsdAmount = $builder->clone()->sum('income_usd');
        $expenseUsdAmount = $builder->clone()->sum('expense_usd');
        $totalUsdAmount = $incomeUsdAmount - $expenseUsdAmount;
        $data = [
            'cny' => [
                'total_amount' => $totalCnyAmount,
                'cut_off_amount' => 0,
                'cut_off_income_amount' => $incomeCnyAmount,
                'cut_off_expense_amount' => $expenseCnyAmount,
            ],
            'usd' => [
                'total_amount' => $totalUsdAmount,
                'cut_off_amount' => 0,
                'cut_off_income_amount' => $incomeUsdAmount,
                'cut_off_expense_amount' => $expenseUsdAmount,
            ]
        ];
        return response()->json($data);
    }
}
