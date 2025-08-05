<?php

namespace App\Controllers;

use App\Models\Product;
use PHPFramework\Pagination;

class ProductController extends BaseController
{
    private int $perPage = 10;

    public function index()
    {
        $model = new Product();
        
        // Получаем общее количество компаний
        $companyAll = $model->count();

        // $pagination = new Pagination($companyAll, $this->perPage, tpl: 'pagination/base2', midSize: 3);

        // Получаем список продуктов с учётом пагинации
        $products = $model->findAll($this->perPage);

        if (!$products) {
            response()->setResponseCode(404);
            return json_encode([
                'status' => 'error', 
                'data' => 'Products not found'
            ]);
        }

        response()->setResponseCode(200);
        return json_encode(['status' => 'success', 'data' => $products]);
    }

}