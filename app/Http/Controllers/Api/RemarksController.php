<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RemarkRequest;
use App\Models\Remark;
use Illuminate\Http\Request;

class RemarksController extends Controller
{
    public function index()
    {

    }

    public function store(RemarkRequest $request, Remark $remark)
    {
        
    }
}
