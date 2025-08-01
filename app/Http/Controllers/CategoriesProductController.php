<?php

namespace App\Http\Controllers;

use App\Models\CategoriesProduct;
use App\Http\Requests\StoreCategoriesProductRequest;
use App\Http\Requests\UpdateCategoriesProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriesProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = CategoriesProduct::all();

        return response()->json([
            'Message' => "Sukses Kawan",
            $data
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'categories_nama' => 'required|max:255',
        ]);
        $categories = CategoriesProduct::create([
            'categories_id' => strtoupper(Str::random(16)),
            'categories_nama' => $request->categories_nama,
        ]);

        return response()->json([
            'Message' => 'Categories sukses ditambah',
            $categories
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoriesProduct $categoriesProduct)
    {
        $categoriesProduct = CategoriesProduct::where('categories_id', $categoriesProduct)->first();
        if (!$categoriesProduct) {
            return response()->json(["Message" => "data invicible"], 404);
        }
        return response()->json([$categoriesProduct]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoriesProductRequest $request, CategoriesProduct $categoriesProduct)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoriesProduct $categoriesProduct)
    {
        //
    }
}
