<?php

namespace controllers;

use core\Controller;
use models\Product;
use models\ProductVariant;
use models\Inventory;

/**
 * Home Controller
 */
class HomeController extends Controller
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
     * Home page - Display all products
     */
    public function index()
    {
        $limit = 12;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->getProductsWithCategory($limit, $offset);
        $totalProducts = $this->productModel->count();
        $totalPages = ceil($totalProducts / $limit);

        // Add default variant info for each product
        foreach ($products as &$product) {
            $variants = $this->variantModel->getVariantsWithInventory($product['product_id']);
            $product['variants'] = $variants;
            $product['in_stock'] = !empty($variants) && array_sum(array_column($variants, 'quantity')) > 0;
        }

        $data = [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ];

        $this->render('home/index', $data);
    }

    /**
     * Product detail page
     */
    public function detail($id)
    {
        $product = $this->productModel->getProductWithCategory($id);

        if (!$product) {
            http_response_code(404);
            die('Sản phẩm không tìm thấy');
        }

        $variants = $this->variantModel->getVariantsWithInventory($id);

        $data = [
            'product' => $product,
            'variants' => $variants
        ];

        $this->render('product/detail', $data);
    }

    /**
     * Search products (API)
     */
    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->json(['error' => 'Invalid request method'], 405);
        }

        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

        if (strlen($keyword) < 2) {
            $this->json(['error' => 'Keyword too short'], 400);
        }

        $products = $this->productModel->search($keyword, 20);

        foreach ($products as &$product) {
            $variants = $this->variantModel->getByProductId($product['product_id']);
            $product['variants'] = $variants;
        }

        $this->json(['success' => true, 'data' => $products]);
    }

    /**
     * Get product variants (API)
     */
    public function getVariants($id)
    {
        $variants = $this->variantModel->getVariantsWithInventory($id);

        if (empty($variants)) {
            $this->json(['error' => 'Không có biến thể sản phẩm'], 404);
        }

        $this->json(['success' => true, 'data' => $variants]);
    }

    /**
     * Get single variant (API)
     */
    public function getVariant($variantId)
    {
        $variant = $this->variantModel->getVariantWithInventory($variantId);

        if (!$variant) {
            $this->json(['error' => 'Biến thể không tìm thấy'], 404);
        }

        $this->json(['success' => true, 'data' => $variant]);
    }
}
