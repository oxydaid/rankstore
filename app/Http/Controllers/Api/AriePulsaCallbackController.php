<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AriePulsaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AriePulsaCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // Log semua request masuk untuk debugging (Form Data otomatis terbaca oleh $request->all())
        Log::info('AriePulsa Callback Hit:', $request->all());

        $data = $request->all();

        // Normalisasi Data: AriePulsa kadang kirim boolean sebagai string "true"/"false" di Form Data
        if (isset($data['status'])) {
            $data['status'] = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN);
        }

        $service = new AriePulsaService;
        $result = $service->handleCallback($data);

        return response()->json($result);
    }
}
