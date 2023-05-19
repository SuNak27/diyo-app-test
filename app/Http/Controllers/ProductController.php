<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $products = Product::getAll($request->limit);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $products['data'],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required',
            'price' => 'required|integer',
            'variants' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ]);
        }

        DB::beginTransaction();

        try {
            $data = $validator->validated();

            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
            ]);

            foreach ($data['variants'] as $variant) {
                ProductDetail::create([
                    'product_id' => $product->id,
                    'variant_id' => $variant,
                ]);
            }

            DB::commit();

            return response()->json([
                'code' => Response::HTTP_CREATED,
                'message' => 'Success',
                'data' => $product,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage(),
                'data' => $e->getTrace(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        try {
            return response()->json([
                'code' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $product,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage(),
                'data' => $e->getTrace()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required',
            'price' => 'required|integer',
            'variants' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ]);
        }

        DB::beginTransaction();

        try {
            $data = $validator->validated();

            $product->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
            ]);

            ProductDetail::where('product_id', $product->id)->delete();

            foreach ($data['variants'] as $variant) {
                ProductDetail::create([
                    'product_id' => $product->id,
                    'variant_id' => $variant,
                ]);
            }


            DB::commit();
            return response()->json([
                'code' => Response::HTTP_CREATED,
                'message' => 'Success',
                'data' => $product,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage(),
                'data' => $e->getTrace(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return response()->json([
                'code' => Response::HTTP_CREATED,
                'message' => 'Success Delete Product',
                'data' => $product,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage(),
                'data' => $e->getTrace()
            ]);
        }
    }
}
