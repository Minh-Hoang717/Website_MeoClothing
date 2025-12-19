<?php

namespace controllers;

use core\Controller;
use models\Promotion;

/**
 * Promotion Controller - Marketing Management
 */
class PromotionController extends Controller
{
    private $promotionModel;

    public function __construct()
    {
        parent::__construct();
        $this->checkAdmin();
        $this->promotionModel = new Promotion();
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
     * List all promotions
     */
    public function index()
    {
        $promotions = $this->promotionModel->getAll();

        $data = [
            'promotions' => $promotions
        ];

        $this->render('admin/promotions/index', $data);
    }

    /**
     * Add promotion form
     */
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleAdd();
        }

        $this->render('admin/promotions/add');
    }

    /**
     * Handle add promotion
     */
    private function handleAdd()
    {
        $code = isset($_POST['code']) ? strtoupper(trim($_POST['code'])) : '';
        $discountType = isset($_POST['discount_type']) ? trim($_POST['discount_type']) : '';
        $discountValue = isset($_POST['discount_value']) ? (float)$_POST['discount_value'] : 0;
        $startDate = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
        $endDate = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';

        $errors = [];

        // Validation
        if (empty($code) || strlen($code) < 2) {
            $errors[] = 'Mã khuyến mãi phải ít nhất 2 ký tự';
        }

        if ($this->promotionModel->getByColumn('code', $code)) {
            $errors[] = 'Mã khuyến mãi này đã tồn tại';
        }

        if (!in_array($discountType, ['fixed', 'percentage'])) {
            $errors[] = 'Loại giảm giá không hợp lệ';
        }

        if ($discountValue <= 0) {
            $errors[] = 'Giá trị giảm phải lớn hơn 0';
        }

        if ($discountType === 'percentage' && $discountValue > 100) {
            $errors[] = 'Phần trăm giảm không được vượt quá 100%';
        }

        if (empty($startDate)) {
            $errors[] = 'Ngày bắt đầu không được để trống';
        }

        if (empty($endDate)) {
            $errors[] = 'Ngày kết thúc không được để trống';
        }

        $startDateTime = strtotime($startDate);
        $endDateTime = strtotime($endDate);
        $now = time();

        if ($endDateTime <= $startDateTime) {
            $errors[] = 'Ngày kết thúc phải sau ngày bắt đầu';
        }

        if (!empty($errors)) {
            $data = [
                'errors' => $errors,
                'code' => $code,
                'discountType' => $discountType,
                'discountValue' => $discountValue,
                'startDate' => $startDate,
                'endDate' => $endDate
            ];
            return $this->render('admin/promotions/add', $data);
        }

        $promotionData = [
            'code' => $code,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        $this->promotionModel->insert($promotionData);
        $_SESSION['success'] = 'Thêm mã khuyến mãi thành công';
        $this->redirect(APP_URL . '/admin/promotions');
    }

    /**
     * Edit promotion form
     */
    public function edit($id)
    {
        $promotion = $this->promotionModel->getById($id);

        if (!$promotion) {
            http_response_code(404);
            die('Mã khuyến mãi không tìm thấy');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleEdit($id);
        }

        $data = ['promotion' => $promotion];
        $this->render('admin/promotions/edit', $data);
    }

    /**
     * Handle edit promotion
     */
    private function handleEdit($id)
    {
        $code = isset($_POST['code']) ? strtoupper(trim($_POST['code'])) : '';
        $discountType = isset($_POST['discount_type']) ? trim($_POST['discount_type']) : '';
        $discountValue = isset($_POST['discount_value']) ? (float)$_POST['discount_value'] : 0;
        $startDate = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
        $endDate = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';

        $promotion = $this->promotionModel->getById($id);
        if (!$promotion) {
            http_response_code(404);
            die('Mã khuyến mãi không tìm thấy');
        }

        $errors = [];

        // Validation
        if (empty($code) || strlen($code) < 2) {
            $errors[] = 'Mã khuyến mãi phải ít nhất 2 ký tự';
        }

        // Check if code is changed and already exists
        if ($code !== $promotion['code']) {
            if ($this->promotionModel->getByColumn('code', $code)) {
                $errors[] = 'Mã khuyến mãi này đã tồn tại';
            }
        }

        if (!in_array($discountType, ['fixed', 'percentage'])) {
            $errors[] = 'Loại giảm giá không hợp lệ';
        }

        if ($discountValue <= 0) {
            $errors[] = 'Giá trị giảm phải lớn hơn 0';
        }

        if ($discountType === 'percentage' && $discountValue > 100) {
            $errors[] = 'Phần trăm giảm không được vượt quá 100%';
        }

        if (empty($startDate)) {
            $errors[] = 'Ngày bắt đầu không được để trống';
        }

        if (empty($endDate)) {
            $errors[] = 'Ngày kết thúc không được để trống';
        }

        if (strtotime($endDate) <= strtotime($startDate)) {
            $errors[] = 'Ngày kết thúc phải sau ngày bắt đầu';
        }

        if (!empty($errors)) {
            $data = [
                'errors' => $errors,
                'promotion' => array_merge($promotion, [
                    'code' => $code,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ])
            ];
            return $this->render('admin/promotions/edit', $data);
        }

        $updateData = [
            'code' => $code,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        $this->promotionModel->update($id, $updateData);
        $_SESSION['success'] = 'Cập nhật mã khuyến mãi thành công';
        $this->redirect(APP_URL . '/admin/promotions');
    }

    /**
     * Delete promotion
     */
    public function delete($id)
    {
        $promotion = $this->promotionModel->getById($id);

        if (!$promotion) {
            $this->json(['error' => 'Mã khuyến mãi không tìm thấy'], 404);
        }

        $this->promotionModel->delete($id);
        $_SESSION['success'] = 'Xoá mã khuyến mãi thành công';
        $this->redirect(APP_URL . '/admin/promotions');
    }

    /**
     * Delete via AJAX
     */
    public function deleteAjax()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        if ($id <= 0) {
            $this->json(['error' => 'Invalid ID'], 400);
        }

        $promotion = $this->promotionModel->getById($id);
        if (!$promotion) {
            $this->json(['error' => 'Mã khuyến mãi không tìm thấy'], 404);
        }

        $this->promotionModel->delete($id);
        $this->json(['success' => true, 'message' => 'Xoá mã khuyến mãi thành công']);
    }
}
