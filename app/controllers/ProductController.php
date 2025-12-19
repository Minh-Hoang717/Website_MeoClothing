<?php

namespace controllers;

use core\Controller;
use models\Product;
use models\ProductVariant;
use models\Inventory;

/**
 * Product Controller
 */
class ProductController extends Controller
{
    private $productModel;
    private $variantModel;
    private $inventoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->variantModel = new ProductVariant();
        $this->inventoryModel = new Inventory();
    }

    /**
     * Get product by category
     */
    public function category($categoryId)
    {
        $limit = 12;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->getByCategory($categoryId, $limit, $offset);
        $totalProducts = $this->productModel->count('category_id', $categoryId);
        $totalPages = ceil($totalProducts / $limit);

        foreach ($products as &$product) {
            $variants = $this->variantModel->getVariantsWithInventory($product['product_id']);
            $product['variants'] = $variants;
            $product['in_stock'] = !empty($variants) && array_sum(array_column($variants, 'quantity')) > 0;
        }

        $data = [
            'products' => $products,
            'categoryId' => $categoryId,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ];

        $this->render('product/category', $data);
    }
}
