<?php

namespace App\Models;

use DbTableNameConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class OrderPackageModel extends Model
{
    use HasUuids;

    protected $table = DbTableNameConstant::ORDER_PACKAGE;

    protected $fillable = [
        'id',
        'customer_id',
        'discount',
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

    public function order()
    {
        return $this->hasMany(OrderModel::class, 'order_package_id', 'id');
    }

    public function productCount()
    {
        return $this->order()->count();
    }
}
