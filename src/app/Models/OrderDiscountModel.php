<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use DbTableNameConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDiscountModel extends Model
{
    use HasUuids, HasFactory;

    protected $table = DbTableNameConstant::ORDER_DISCOUNT;

    protected $fillable = [
        'id',
        'order_id',
        'discount_id',
        'discount_amount',
        'sub_total'
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

    public function order()
    {
        return $this->belongsTo(OrderModel::class, 'order_id', 'id');
    }

    public function discount()
    {
        return $this->belongsTo(DiscountModel::class, 'discount_id', 'id');
    }
}
