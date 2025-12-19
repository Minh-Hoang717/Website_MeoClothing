<?php

namespace controllers;

use core\Controller;
use models\Product;
use models\ProductVariant;
use models\Promotion;

/**
 * Cart Controller
 */
class CartController extends Controller
{
    private $productModel;
    private $variantModel;
    private $promotionModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->variantModel = new ProductVariant();
        $this->promotionModel = new Promotion();
    }

    /**
     * Get cart from session
     */
    private function getCart()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        return $_SESSION['cart'];
    }

    /**
     * Save cart to session
     */
    private function saveCart($cart)
    {
        $_SESSION['cart'] = $cart;
    }

    /**
     * View cart page
     */
    public function viewCart()
    {
        $cart = $this->getCart();
        $cartItems = [];
        $total = 0;
        $subtotal = 0;

        foreach ($cart as $variantId => $item) {
            $variant = $this->variantModel->getVariantWithInventory($variantId);
            if ($variant) {
                $itemTotal = $variant['current_price'] * $item['quantity'];
                $total += $itemTotal;
                $subtotal = $total;

                $cartItems[] = [
                    'variant' => $variant,
                    'quantity' => $item['quantity'],
                    'itemTotal' => $itemTotal
                ];
            }
        }

        $discount = 0;
        if (isset($_SESSION['promotion'])) {
            $promotion = $_SESSION['promotion'];
            $discount = $this->promotionModel->calculateDiscount($promotion, $subtotal);
            $total -= $discount;
        }

        $data = [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total
        ];

        $this->render('cart/view', $data);
    }

    /**
     * Add to cart (API)
     */
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $variantId = isset($data['variant_id']) ? (int)$data['variant_id'] : 0;
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

        if ($variantId <= 0 || $quantity <= 0) {
            $this->json(['error' => 'Invalid variant or quantity'], 400);
        }

        $variant = $this->variantModel->getVariantWithInventory($variantId);
        if (!$variant) {
            $this->json(['error' => 'Variant not found'], 404);
        }

        if ($variant['quantity'] < $quantity) {
            $this->json(['error' => 'Not enough stock'], 400);
        }

        $cart = $this->getCart();
        if (isset($cart[$variantId])) {
            $cart[$variantId]['quantity'] += $quantity;
        } else {
            $cart[$variantId] = ['quantity' => $quantity];
        }

        $this->saveCart($cart);
        $this->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng', 'cartCount' => array_sum(array_column($cart, 'quantity'))]);
    }

    /**
     * Update cart item (API)
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $variantId = isset($data['variant_id']) ? (int)$data['variant_id'] : 0;
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 0;

        $cart = $this->getCart();

        if ($quantity <= 0) {
            unset($cart[$variantId]);
        } else {
            $variant = $this->variantModel->getVariantWithInventory($variantId);
            if ($variant && $variant['quantity'] >= $quantity) {
                $cart[$variantId] = ['quantity' => $quantity];
            } else {
                $this->json(['error' => 'Not enough stock'], 400);
            }
        }

        $this->saveCart($cart);
        $this->json(['success' => true, 'message' => 'Cập nhật giỏ hàng thành công']);
    }

    /**
     * Remove from cart (API)
     */
    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $variantId = isset($data['variant_id']) ? (int)$data['variant_id'] : 0;

        $cart = $this->getCart();
        unset($cart[$variantId]);
        $this->saveCart($cart);

        $this->json(['success' => true, 'message' => 'Xoá khỏi giỏ hàng thành công']);
    }

    /**
     * Apply promotion code (API)
     */
    public function applyPromotion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $code = isset($data['code']) ? trim($data['code']) : '';

        if (empty($code)) {
            $this->json(['error' => 'Mã khuyến mãi không được để trống'], 400);
        }

        $promotion = $this->promotionModel->getActiveByCode($code);

        if (!$promotion) {
            $this->json(['error' => 'Mã khuyến mãi không hợp lệ hoặc đã hết hạn'], 400);
        }

        $_SESSION['promotion'] = $promotion;
        $this->json(['success' => true, 'message' => 'Áp dụng mã khuyến mãi thành công', 'data' => $promotion]);
    }

    /**
     * Clear promotion (API)
     */
    public function clearPromotion()
    {
        unset($_SESSION['promotion']);
        $this->json(['success' => true, 'message' => 'Xoá mã khuyến mãi thành công']);
    }
}
