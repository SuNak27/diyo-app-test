<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $sales = Sales::getAll($request->limit);

            return response()->json([
                'code' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $sales['data'],
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'status' => 'error',
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products.*.product_id' => 'required|exists:product_details,product_id',
            'products.*.variants' => 'required|array',
            'payment_method' => 'required|in:OVO,DANA,Shopee Pay,GOPAY'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'status' => $validator->errors()->first(),
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();

        try {
            $checkProduct = Product::whereIn('id', array_column($request->products, 'product_id'))
                ->with('variants')
                ->get();

            if ($checkProduct->count() != count($request->products)) {
                return response()->json([
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'status' => 'error',
                    'message' => 'Product Not Found',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $total = 0;
            $total += $checkProduct->sum('price');

            foreach ($checkProduct as $key => $value) {
                $checkVariant = $value->variants->whereIn('id', $request->products[$key]['variants']);

                if ($checkVariant->count() != count($request->products[$key]['variants'])) {
                    return response()->json([
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'status' => 'error',
                        'message' => 'Variant Not Found',
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $total += $checkVariant->sum('additional_price');
            }

            $sales = Sales::create([
                'total' => $total,
                'payment_method' => $request->payment_method,
            ]);

            // Create Cart
            foreach ($checkProduct as $key => $value) {
                $productDetail = ProductDetail::where('product_id', $request->products[$key]['product_id'])
                    ->whereIn('variant_id', $request->products[$key]['variants'])
                    ->get();

                foreach ($productDetail as $key => $value) {
                    Cart::create([
                        'product_detail_id' => $value->id,
                        'sales_id' => $sales->id,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'code' => Response::HTTP_CREATED,
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'id' => $sales->id,
                    'total' => $sales->total,
                    'payment_method' => $sales->payment_method,
                    'created_at' => $sales->created_at,
                ],
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'status' => 'error',
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $sales = Sales::getByID($id);

            return response()->json([
                'code' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $sales,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'status' => 'error',
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sales $sales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sales $sales)
    {
        //
    }
}
