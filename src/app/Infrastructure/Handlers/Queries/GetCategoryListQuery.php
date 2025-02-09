<?php

namespace App\Infrastructure\Handlers\Queries;

use App\Infrastructure\Abstraction\Service\IProductService;
use App\Models\CategoryModel;
use Illuminate\Support\Facades\Cache;

class GetCategoryListQuery {}

class GetCategoryListQueryHandler
{
    public function __construct(private IProductService $productService) {}

    public function handle(GetCategoryListQuery $query)
    {
        return $this->productService->getCategories();
    }
}
