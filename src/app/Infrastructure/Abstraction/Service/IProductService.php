<?php

namespace App\Infrastructure\Abstraction\Service;

interface IProductService
{
    public function getCategories();
    public function getProducts($categoryId);
}
