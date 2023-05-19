<?php

namespace App\Models;

use App\Traits\UniqueID;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UniqueID;

    protected $guarded = ['id'];
    protected $hidden = ['updated_at', 'deleted_at'];

    public static function generateID()
    {
        $date = Carbon::now()->format('ymd');
        $time = Carbon::now()->format('His');

        $id = "S-{$date}-{$time}";

        return $id;
    }

    public static function getAll($limit)
    {
        $query = $limit != null ? self::paginate($limit) : self::get();
        $data = [];
        foreach ($query as $key => $value) {
            $data[$key] = [
                'id' => $value->id,
                'cart' => Cart::getBySalesID($value->id),
                'total' => $value->total,
                'payment_method' => $value->payment_method,
                'created_at' => $value->created_at,
            ];
        }

        $item = [
            'data' => $data
        ];

        return $item;
    }
}
