<?php

namespace App\Infrastructure\Handlers\Queries;

use App\Infrastructure\Abstraction\Service\IProductService;
use App\Models\ProductModel;
use Illuminate\Support\Facades\Cache;


class GetProductListQuery
{
    public function __construct(public $categoryId = null) {}
}

class GetProductListQueryHandler
{
    public function __construct(private IProductService $productService) {}

    public function handle(GetProductListQuery $query)
    {
        return $this->productService->getProducts($query->categoryId);
    }
}
