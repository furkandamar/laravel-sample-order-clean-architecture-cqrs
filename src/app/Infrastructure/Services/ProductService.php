<?php

namespace App\Infrastructure\Services;

use App\Infrastructure\Abstraction\Service\IProductService;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\Response\ProductResponse;
use Illuminate\Support\Facades\Cache;

class ProductService implements IProductService
{
    public function getCategories()
    {
        return Cache::remember('categories', 60, function () {
            return CategoryModel::all();
        });
    }

    public function getProducts($categoryId)
    {
        $cacheKey = sprintf("products_all:category_%s", $categoryId ?? 'all');
        return Cache::remember($cacheKey, 60, function () use ($categoryId) {
            $entity =  ProductModel::query();
            if ($categoryId) {
                $entity =  $entity->where('category_id', $categoryId)->get();
            } else {
                $entity =  $entity->get();
            }

            return collect($entity)->map(function ($item) {
                return new ProductResponse($item->id, $item->name, $item->category_id, $item->category->category_name, $item->price, $item->stock);
            });
        });
    }
}
