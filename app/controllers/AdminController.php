<?php

namespace controllers;

use core\Controller;
use models\User;
use models\Product;
use models\ProductVariant;
use models\Order;
use models\Inventory;

/**
 * Admin Controller
 */
class AdminController extends Controller
{
    private $userModel;
    private $productModel;
    private $variantModel;
    private $orderModel;
    private $inventoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->checkAdmin();
        $this->userModel = new User();
        $this->productModel = new Product();
        $this->variantModel = new ProductVariant();
        $this->orderModel = new Order();
        $this->inventoryModel = new Inventory();
    }

    /**
     * Check admin permission
     */
    private function checkAdmin()
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'staff')) {
            http_response_code(403);
            die('Bạn không có quyền truy cập');
        }
    }

    /**
     * Admin dashboard
     */
    public function index()
    {
        $totalProducts = $this->productModel->count();
        $totalOrders = $this->orderModel->count();
        $totalUsers = $this->userModel->count();

        // Calculate total revenue from completed orders
        $totalRevenue = $this->orderModel->getTotalRevenue();

        // Get top 5 best-selling products
        $topProducts = $this->orderModel->getTopProducts(5);

        $data = [
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
            'topProducts' => $topProducts
        ];

        $this->render('admin/index', $data);
    }

    /**
     * Manage products
     */
    public function products()
    {
        $products = $this->productModel->getProductsWithCategory();

        $data = [
            'products' => $products
        ];

        $this->render('admin/products', $data);
    }

    /**
     * Add product form
     */
    public function addProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleAddProduct();
        }

        $this->render('admin/add-product');
    }

    /**
     * Handle add product
     */
    private function handleAddProduct()
    {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $originalPrice = isset($_POST['original_price']) ? (float)$_POST['original_price'] : 0;

        $errors = [];

        if (empty($name)) {
            $errors[] = 'Tên sản phẩm không được để trống';
        }

        if ($categoryId <= 0) {
            $errors[] = 'Vui lòng chọn danh mục';
        }

        if ($originalPrice <= 0) {
            $errors[] = 'Giá gốc phải lớn hơn 0';
        }

        // Handle image upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploadImage($_FILES['image']);
            if (!$imagePath) {
                $errors[] = 'Lỗi upload ảnh';
            }
        }

        if (!empty($errors)) {
            $data = [
                'errors' => $errors,
                'name' => $name,
                'categoryId' => $categoryId,
                'description' => $description,
                'originalPrice' => $originalPrice
            ];
            return $this->render('admin/add-product', $data);
        }

        $productData = [
            'name' => $name,
            'category_id' => $categoryId,
            'description' => $description,
            'original_price' => $originalPrice,
            'image_path' => $imagePath
        ];

        $productId = $this->productModel->insert($productData);
        $_SESSION['success'] = 'Thêm sản phẩm thành công';
        $this->redirect(APP_URL . '/admin/products');
    }

    /**
     * Upload image helper
     */
    private function uploadImage($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        $filename = time() . '-' . uniqid() . '-' . basename($file['name']);
        $filepath = UPLOAD_PATH . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return false;
        }

        return $filename;
    }

    /**
     * Manage orders
     */
    public function orders()
    {
        $orders = $this->orderModel->getAll();

        $data = [
            'orders' => $orders
        ];

        $this->render('admin/orders', $data);
    }

    /**
     * Update order status (API)
     */
    public function updateOrderStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $orderId = isset($data['order_id']) ? (int)$data['order_id'] : 0;
        $status = isset($data['status']) ? trim($data['status']) : '';

        if ($orderId <= 0 || empty($status)) {
            $this->json(['error' => 'Invalid order or status'], 400);
        }

        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'completed'];
        if (!in_array($status, $validStatuses)) {
            $this->json(['error' => 'Invalid status'], 400);
        }

        $this->orderModel->updateStatus($orderId, $status);
        $this->json(['success' => true, 'message' => 'Cập nhật trạng thái đơn hàng thành công']);
    }

    /**
     * Get order details (API)
     */
    public function getOrderDetails($id)
    {
        $order = $this->orderModel->getOrderWithDetails($id);
        
        if (!$order) {
            $this->json(['error' => 'Không tìm thấy đơn hàng'], 404);
            return;
        }

        $this->json([
            'order' => $order,
            'details' => $order['details'] ?? []
        ]);
    }

    /**
     * Edit product form
     */
    public function editProduct($id)
    {
        $product = $this->productModel->getProductWithCategory($id);
        
        if (!$product) {
            $_SESSION['error'] = 'Không tìm thấy sản phẩm';
            $this->redirect(APP_URL . '/admin/products');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleEditProduct($id, $product);
        }

        $data = [
            'product' => $product
        ];

        $this->render('admin/edit-product', $data);
    }

    /**
     * Handle edit product
     */
    private function handleEditProduct($id, $oldProduct)
    {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $originalPrice = isset($_POST['original_price']) ? (float)$_POST['original_price'] : 0;

        $errors = [];

        if (empty($name)) {
            $errors[] = 'Tên sản phẩm không được để trống';
        }

        if ($categoryId <= 0) {
            $errors[] = 'Vui lòng chọn danh mục';
        }

        if ($originalPrice <= 0) {
            $errors[] = 'Giá gốc phải lớn hơn 0';
        }

        // Handle image upload (optional for edit)
        $imagePath = $oldProduct['image_path'] ?? null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $newImagePath = $this->uploadImage($_FILES['image']);
            if ($newImagePath) {
                // Delete old image if exists
                if ($imagePath && file_exists(UPLOAD_PATH . '/' . $imagePath)) {
                    unlink(UPLOAD_PATH . '/' . $imagePath);
                }
                $imagePath = $newImagePath;
            } else {
                $errors[] = 'Lỗi upload ảnh';
            }
        }

        if (!empty($errors)) {
            $data = [
                'errors' => $errors,
                'product' => array_merge($oldProduct, [
                    'name' => $name,
                    'category_id' => $categoryId,
                    'description' => $description,
                    'original_price' => $originalPrice
                ])
            ];
            return $this->render('admin/edit-product', $data);
        }

        $productData = [
            'name' => $name,
            'category_id' => $categoryId,
            'description' => $description,
            'original_price' => $originalPrice,
            'image_path' => $imagePath
        ];

        $this->productModel->update($id, $productData, 'product_id');
        $_SESSION['success'] = 'Cập nhật sản phẩm thành công';
        $this->redirect(APP_URL . '/admin/products');
    }

    /**
     * Delete product
     */
    public function deleteProduct($id)
    {
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $_SESSION['error'] = 'Không tìm thấy sản phẩm';
            $this->redirect(APP_URL . '/admin/products');
            return;
        }

        // Check if product has variants that are used in orders
        if ($this->isProductInOrders($id)) {
            $_SESSION['error'] = 'Không thể xóa sản phẩm này vì đã có trong đơn hàng. Bạn có thể ẩn sản phẩm thay vì xóa.';
            $this->redirect(APP_URL . '/admin/products');
            return;
        }

        // Delete related data first (variants, inventory)
        $this->deleteProductRelatedData($id);

        // Delete image if exists
        if (isset($product['image_path']) && $product['image_path'] && file_exists(UPLOAD_PATH . '/' . $product['image_path'])) {
            unlink(UPLOAD_PATH . '/' . $product['image_path']);
        }

        $this->productModel->delete($id);
        $_SESSION['success'] = 'Xóa sản phẩm thành công';
        $this->redirect(APP_URL . '/admin/products');
    }

    /**
     * Check if product variants are used in any orders
     */
    private function isProductInOrders($productId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM orderdetails od
                INNER JOIN productvariants pv ON od.variant_id = pv.variant_id
                WHERE pv.product_id = :product_id";
        
        $db = \core\Database::getInstance();
        $result = $db->prepare($sql)
            ->bind(':product_id', $productId, \PDO::PARAM_INT)
            ->single();
        
        return $result['count'] > 0;
    }

    /**
     * Delete product related data (variants, inventory)
     */
    private function deleteProductRelatedData($productId)
    {
        $db = \core\Database::getInstance();
        
        // Delete inventory records for product variants
        $sql = "DELETE i FROM inventory i
                INNER JOIN productvariants pv ON i.variant_id = pv.variant_id
                WHERE pv.product_id = :product_id";
        $db->prepare($sql)
            ->bind(':product_id', $productId, \PDO::PARAM_INT)
            ->execute();
        
        // Delete product variants
        $sql = "DELETE FROM productvariants WHERE product_id = :product_id";
        $db->prepare($sql)
            ->bind(':product_id', $productId, \PDO::PARAM_INT)
            ->execute();
    }
}
