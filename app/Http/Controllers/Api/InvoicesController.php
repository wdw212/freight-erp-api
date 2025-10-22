<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoicesController extends Controller
{
    public function index(Request $request)
    {

    }

    public function store(InvoiceRequest $request, Invoice $invoice)
    {

    }

    public function show(Invoice $invoice)
    {

    }

    public function update(InvoiceRequest $request, Invoice $invoice)
    {

    }

    /**
     * 删除
     * @param Invoice $invoice
     * @return Response
     */
    public function destroy(Invoice $invoice): Response
    {
        $invoice->delete();
        return response()->noContent();
    }
}
