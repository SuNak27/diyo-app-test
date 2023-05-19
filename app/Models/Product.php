<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['pivot', 'created_at', 'updated_at', 'deleted_at'];

    public function variants()
    {
        return $this->belongsToMany(Variant::class, 'product_details')->where('product_details.deleted_at', null);
    }

    public static function getAll($limit = null)
    {
        $query = $limit != null ? self::paginate($limit)->with('variants') : self::with('variants')->get();

        $item = [
            'data' => $limit ? $query->items() : $query
        ];

        return $item;
    }
}
