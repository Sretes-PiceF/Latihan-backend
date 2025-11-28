<?php

namespace App\Http\Controllers;

use App\Models\CategoriesProduct;
use App\Http\Requests\StoreCategoriesProductRequest;
use App\Http\Requests\UpdateCategoriesProductRequest;
use Exception;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoriesProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $data = CategoriesProduct::all();

        return response()
            ->json([
                'message' => "Sukses kawan",
                'data' => $data
            ])
            ->header('Cache-Control', 'public, max-age=300');
    }


    public function refreshData()
    {
        $data = CategoriesProduct::all();

        Cache::put('categories_product', $data, now()->addMinutes(5));
        return response()->json([
            'message' => 'Data berhasil di refresh',
            'data' => $data
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // validasi dulu
        $request->validate([
            'categories_nama' => 'required|max:255',
        ]);

        // simpan data
        $categories = CategoriesProduct::create([
            'categories_id' => strtoupper(Str::random(16)),
            'categories_nama' => $request->categories_nama,
        ]);

        // response sukses
        return response()->json([
            'message' => 'Categories sukses ditambah',
            'data' => $categories
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show($categoriesProduct)
    {
        $categoriesProduct = CategoriesProduct::where('categories_id', $categoriesProduct)->first();
        if (!$categoriesProduct) {
            return response()->json(["Message" => "data invicible"], 404);
        }

        Cache::forget('categories_products');

        return response()->json([$categoriesProduct]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update($categoriesProduct_id, Request $request)
    {
        DB::beginTransaction();
        try {
            $categories = CategoriesProduct::findOrFail($categoriesProduct_id);

            $input = $request->all();

            if ($request->isJson()) {
                $input = $request->json()->all();
            }

            $validator = Validator::make($input, [
                'categories_nama' => 'sometimes|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validasi gagal",
                    "errors" => $validator->errors()
                ], 422);
            }

            $categories->update($validator->validated());
            DB::commit();


            // hapus cache biar data terbaru nanti diambil ulang
            Cache::forget('categories_products');

            return response()->json([
                'msg' => 'Data berhasil diperbarui',
                'data' => $categories->refresh()
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'msg' => "Terjadi kesalahan",
                'error' => $e->getMessage(),
                'debug' => $request->all()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($categoriesProduct_id, Request $request)
    {
        $categoriesProduct = CategoriesProduct::find($categoriesProduct_id);
        $categoriesProduct->delete();

        return response()->json([
            "message" => "data sukses dihapus",
            $categoriesProduct
        ]);
    }
}
