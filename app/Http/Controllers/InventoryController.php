<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $inventory = Inventory::getAll($request->limit);

            return response()->json([
                'code' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $inventory['data'],
            ], Response::HTTP_OK);
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
            'name' => 'required|string|unique:inventories',
            'price' => 'required|integer',
            'amount' => 'required|integer',
            'unit' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ]);
        }

        try {
            $data = $validator->validated();

            $inventory = Inventory::create($data);

            return response()->json([
                'code' => Response::HTTP_CREATED,
                'message' => 'Success',
                'data' => $inventory,
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
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
    public function show(Inventory $inventory)
    {
        try {
            return response()->json([
                'code' => Response::HTTP_OK,
                'message' => 'Success',
                'data' => $inventory,
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
    public function update(Request $request, Inventory $inventory)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:inventories,name,' . $inventory->id,
            'price' => 'required|integer',
            'amount' => 'required|integer',
            'unit' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ]);
        }

        try {
            $data = $validator->validated();

            $inventory->update($data);

            return response()->json([
                'code' => Response::HTTP_CREATED,
                'message' => 'Success',
                'data' => $inventory,
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'status' => 'error',
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        try {
            $inventory->delete();
            return response()->json([
                'code' => Response::HTTP_CREATED,
                'message' => 'Success',
                'data' => $inventory,
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'status' => 'error',
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
