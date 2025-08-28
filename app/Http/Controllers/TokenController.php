<?php

namespace App\Http\Controllers;

use App\Models\CategoriesProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Pest\Support\Str;

class TokenController extends Controller
{
    public function generateToken(Request $request)
    {
        $categoryId = 1;

        $token = Str::random(40);

        Cache::put("access_token:{$categoryId}", $token, now()->addMinutes(5));
        return response()->json([
            'access_token' => $token,
            'expired_in' => '5 minutes'
        ]);
    }

    public function getData(Request $request)
    {
        $categoryId = 1;
        $token = $request->bearerToken();

        $cachedToken = Cache::get("access_token:{$categoryId}");

        if (!$cachedToken || $cachedToken !== $token) {
            return response()->json([
                'message' => 'Token kadaluwarsa, mohon ambil ulang',
            ], 403);
        }

        $data = CategoriesProduct::all();
        return response()->json([
            'message' => 'Sukses kawan',
            'data' => $data
        ]);
    }
}
