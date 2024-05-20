<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $product = Products::all();
        return response()->json([
            'success' => true,
            'message' => 'data product',
            'data' => $product
        ]);
    }


    public function store(Request $request)
    {
        // validator
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|integer',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required',
            'expired_at' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Insert Failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Categories::where('name', $request->category_id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'category not found'
            ], 404);
        }

        $hash_image = $request->image->hashName();
        $request->image->move(public_path('images/products'), $hash_image);

        Products::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $hash_image,
            'category_id' => $category->id,
            'expired_at' => $request->expired_at,
            'modifed_by' => auth()->user()->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Insert Success',
            'data' => Products::latest()->first()
        ]);
    }


    public function update(Request $request, string $id)
    {
        $product = Products::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        // validator
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|integer',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required',
            'expired_at' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Insert Failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Categories::where('name', $request->category_id)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'category not found'
            ], 404);
        }

        $old_image = $product->image;

        if ($request->file('image')) {
            $image = $request->file('image');
            $image_name = $image->hashName();
            $image->move(public_path('images/products'), $image_name);
            unlink(public_path('images/products/' . $old_image));

            $product->update([
                'image' => $image_name,
            ]);
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $category->id,
            'expired_at' => $request->expired_at,
            'modifed_by' => auth()->user()->email
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Update Success',
            'data' => $product
        ], 200);
    }

    public function destroy(string $id)
    {
        $product = Products::find($id);

        if ($product) {
            $product->delete();
            return response()->json([
                "status" => true,
                "message" => "Delete Product success"
            ], 200);
        }

        return response()->json([
            "status" => false,
            "message" => "tidak terdapat product"
        ], 404);
    }
}
