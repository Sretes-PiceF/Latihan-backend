<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::all();

        return response()->json([
            "Message" => "Sukses kawan",
            $data
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
        ]);

        $product = Product::create([
            'product_id' => strtoupper(\Illuminate\Support\Str::random(16)),
            'product_name' => $request->product_name,
            'product_stock' => $request->product_stock,
            'product_price' => $request->product_price,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($product_id)
    {
        $product = Product::find($product_id);

        if (!$product) {
            return response()->json(["message" => "data invicible"], 404);
        }
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
