<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use DbTableNameConstant;

class DiscountModel extends Model
{
    use HasUuids, HasFactory;

    protected $table = DbTableNameConstant::DISCOUNT;

    protected $fillable = [
        'id',
        'discount_type',
        'product_id',
        'category_id',
        'min_limit_quantity',
        'discount_value'
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
