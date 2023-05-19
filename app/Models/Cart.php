<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public static function getBySalesID($id)
    {
        $cart = Cart::join('product_details', 'product_details.id', 'carts.product_detail_id')
            ->join('products', 'products.id', 'product_details.product_id')
            ->where('sales_id', $id)
            ->groupBy('product_id')
            ->select(
                'product_id',
                'products.name',
                'products.price',
            )
            ->get();

        foreach ($cart as $key => $value) {
            $variant = Cart::join('product_details', 'product_details.id', 'carts.product_detail_id')
                ->join('variants', 'variants.id', 'product_details.variant_id')
                ->where('sales_id', $id)
                ->where('product_id', $value->product_id)
                ->select(
                    'variants.name as variants_name',
                    'variants.additional_price as price',
                )
                ->get();

            if ($variant->count() > 1) {
                $cart[$key]->variants = $variant;
            } else if ($variant->count() == 1 && $variant[0]->price != 0) {
                $cart[$key]->variants = $variant[0];
            }
        }

        return $cart;
    }
}
