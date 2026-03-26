<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TokopayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TokopayCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // Log request masuk untuk debugging (bisa dimatikan nanti)
        Log::info('Tokopay Callback Hit:', $request->all());

        $service = new TokopayService;
        $result = $service->handleCallback($request->all());

        if ($result['status']) {
            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false, 'error' => $result['message']], 400);
        }
    }
}
