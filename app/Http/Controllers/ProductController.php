<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->get();

        $data = $products->map(function ($product) {
            return [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'product_stock' => $product->product_stock,
                'product_price' => $product->product_price,
                'category_name' => $product->category?->categories_nama ?? '(Kategori tidak ditemukan)',
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ];
        });

        return response()->json([
            'Message' => 'Sukses kawan',
            0 => $data
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'product_stock' => 'required|integer',
            'product_price' => 'required|integer',
            'categories_id' => 'required|string'
        ]);


        $product = Product::create([
            'product_id' => strtoupper(\Illuminate\Support\Str::random(16)),
            'product_name' => $request->product_name,
            'product_stock' => $request->product_stock,
            'product_price' => $request->product_price,
            'categories_id' => $request->categories_id
        ]);

        $product->load('category');

        return response()->json([
            'message' => 'Product created successfully',
            'data' => [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'product_stock' => $product->product_stock,
                'product_price' => $product->product_price,
                'category_name' => $product->category?->categories_nama ?? '(kategori tidak ditemukan)'
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($product)
    {
        $product = Product::where('product_id', $product)->first();
        if (!$product) {
            return response()->json(["message" => "data invicible"], 404);
        }
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($product_id, Request $request)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($product_id);

            // Handle both form-data and JSON
            $input = $request->all();

            // Jika request JSON
            if ($request->isJson()) {
                $input = $request->json()->all();
            }

            $validator = Validator::make($input, [
                'product_name' => 'sometimes|string|max:255',
                'product_stock' => 'sometimes|integer',
                'product_price' => 'sometimes|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "msg" => "Validasi gagal",
                    "errors" => $validator->errors()
                ], 422);
            }

            $product->update($validator->validated());

            DB::commit();

            return response()->json([
                'msg' => 'Data berhasil diperbarui',
                'data' => $product->refresh()
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "msg" => "Terjadi kesalahan",
                "error" => $e->getMessage(),
                "debug" => $request->all() // Untuk debugging
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($product_id)
    {
        $product = Product::find($product_id);
        $product->delete();

        return response()->json([
            "massage" => "Data telah terhapus",
            $product
        ]);
    }
}
