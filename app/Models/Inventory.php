<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public static function getAll($limit = null)
    {
        $query = $limit != null ? self::paginate($limit) : self::get();

        $item = [
            'data' => $limit ? $query->items() : $query
        ];

        return $item;
    }
}
