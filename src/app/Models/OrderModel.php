<?php

namespace App\Models;

use DbTableNameConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class OrderModel extends Model
{
    use HasUuids;


    protected $table = DbTableNameConstant::ORDER;

    protected $fillable = [
        'order_package_id',
        'product_id',
        'quantity',
        'unit_price',
        'total'
    ];
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'id');
    }
}
