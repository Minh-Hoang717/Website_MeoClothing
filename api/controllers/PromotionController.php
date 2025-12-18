<?php
/**
 * Promotion Controller
 * Xử lý API requests cho promotions
 */

class PromotionController extends BaseController {
    
    private $promotionModel;
    
    public function __construct($db) {
        parent::__construct($db);
        $this->promotionModel = new Promotion($db);
    }
    
    /**
     * Process request based on HTTP method
     */
    public function processRequest() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriParts = explode('/', trim($uri, '/'));
        
        // Remove 'api' and 'promotions' parts
        array_shift($uriParts); // 'api'
        array_shift($uriParts); // 'promotions'
        
        $action = $uriParts[0] ?? null;
        $id = $uriParts[1] ?? null;
        
        switch ($this->requestMethod) {
            case 'GET':
                if ($action === 'apply' || $action === 'validate') {
                    // For GET /api/promotions/validate?code=XXX
                    $this->validatePromotion();
                } else if ($action && !$id) {
                    // For GET /api/promotions/{id}
                    $this->getPromotionById($action);
                } else {
                    // For GET /api/promotions
                    $this->getAllPromotions();
                }
                break;
                
            case 'POST':
                if ($action === 'apply' || $action === 'validate') {
                    // For POST /api/promotions/apply
                    $this->applyPromotion();
                } else {
                    // For POST /api/promotions (create)
                    $this->createPromotion();
                }
                break;
                
            case 'PUT':
                if ($action) {
                    // For PUT /api/promotions/{id}
                    $this->updatePromotion($action);
                } else {
                    Response::error("Promotion ID is required", 400);
                }
                break;
                
            case 'DELETE':
                if ($action) {
                    // For DELETE /api/promotions/{id}
                    $this->deletePromotion($action);
                } else {
                    Response::error("Promotion ID is required", 400);
                }
                break;
                
            default:
                $this->methodNotAllowed();
                break;
        }
    }
    
    /**
     * GET /api/promotions
     * Get all promotions with filters
     */
    private function getAllPromotions() {
        $params = $this->getPaginationParams();
        $filter = $_GET['filter'] ?? 'all'; // all, active, upcoming, expired
        
        try {
            $data = [];
            $total = 0;
            
            switch ($filter) {
                case 'active':
                    $data = $this->promotionModel->getActivePromotions($params['page'], $params['pageSize']);
                    $total = $this->promotionModel->getActivePromotionsCount();
                    break;
                    
                case 'upcoming':
                    $data = $this->promotionModel->getUpcomingPromotions($params['pageSize']);
                    $total = count($data);
                    break;
                    
                case 'expired':
                    $data = $this->promotionModel->getExpiredPromotions($params['page'], $params['pageSize']);
                    $total = $this->promotionModel->getCount("end_date < NOW()");
                    break;
                    
                default: // all
                    $data = $this->promotionModel->getAll($params['page'], $params['pageSize'], 'start_date DESC');
                    $total = $this->promotionModel->getCount();
                    break;
            }
            
            Response::paginated($data, $total, $params['page'], $params['pageSize'], "Lấy danh sách khuyến mãi thành công");
            
        } catch (Exception $e) {
            Response::serverError("Lỗi khi lấy danh sách khuyến mãi: " . $e->getMessage());
        }
    }
    
    /**
     * GET /api/promotions/{id}
     * Get promotion by ID
     */
    private function getPromotionById($id) {
        try {
            $promotion = $this->promotionModel->getById($id);
            
            if (!$promotion) {
                Response::notFound("Không tìm thấy khuyến mãi");
            }
            
            Response::success($promotion, "Lấy thông tin khuyến mãi thành công");
            
        } catch (Exception $e) {
            Response::serverError("Lỗi khi lấy thông tin khuyến mãi: " . $e->getMessage());
        }
    }
    
    /**
     * POST /api/promotions
     * Create new promotion
     */
    private function createPromotion() {
        // Require admin authentication
        Auth::requireAdmin();
        
        // Validate input
        $validator = new Validator();
        
        $validator->required('code', $this->requestData['code'] ?? null, 'Mã khuyến mãi');
        $validator->required('discount_type', $this->requestData['discount_type'] ?? null, 'Loại giảm giá');
        $validator->required('discount_value', $this->requestData['discount_value'] ?? null, 'Giá trị giảm');
        $validator->required('start_date', $this->requestData['start_date'] ?? null, 'Ngày bắt đầu');
        $validator->required('end_date', $this->requestData['end_date'] ?? null, 'Ngày kết thúc');
        
        if (!$validator->fails()) {
            $validator->maxLength('code', $this->requestData['code'], 20, 'Mã khuyến mãi');
            $validator->in('discount_type', $this->requestData['discount_type'], ['percentage', 'fixed'], 'Loại giảm giá');
            $validator->decimal('discount_value', $this->requestData['discount_value'], 2, 'Giá trị giảm');
        }
        
        if ($validator->fails()) {
            Response::validationError($validator->getErrors());
        }
        
        try {
            // Check if code already exists
            if ($this->promotionModel->codeExists($this->requestData['code'])) {
                Response::error("Mã khuyến mãi đã tồn tại", 409);
            }
            
            // Validate dates
            $startDate = new DateTime($this->requestData['start_date']);
            $endDate = new DateTime($this->requestData['end_date']);
            
            if ($endDate <= $startDate) {
                Response::error("Ngày kết thúc phải sau ngày bắt đầu", 400);
            }
            
            // Create promotion
            $data = [
                'code' => strtoupper(trim($this->requestData['code'])),
                'discount_type' => $this->requestData['discount_type'],
                'discount_value' => $this->requestData['discount_value'],
                'start_date' => $this->requestData['start_date'],
                'end_date' => $this->requestData['end_date']
            ];
            
            $promotionId = $this->promotionModel->create($data);
            
            if ($promotionId) {
                $promotion = $this->promotionModel->getById($promotionId);
                Response::success($promotion, "Tạo khuyến mãi thành công", 201);
            } else {
                Response::serverError("Không thể tạo khuyến mãi");
            }
            
        } catch (Exception $e) {
            Response::serverError("Lỗi khi tạo khuyến mãi: " . $e->getMessage());
        }
    }
    
    /**
     * PUT /api/promotions/{id}
     * Update promotion
     */
    private function updatePromotion($id) {
        // Require admin authentication
        Auth::requireAdmin();
        
        try {
            // Check if promotion exists
            $existing = $this->promotionModel->getById($id);
            if (!$existing) {
                Response::notFound("Không tìm thấy khuyến mãi");
            }
            
            // Validate input
            $validator = new Validator();
            $data = [];
            
            // Code
            if (isset($this->requestData['code'])) {
                $validator->required('code', $this->requestData['code'], 'Mã khuyến mãi');
                $validator->maxLength('code', $this->requestData['code'], 20, 'Mã khuyến mãi');
                
                // Check if code already exists (excluding current)
                if ($this->promotionModel->codeExists($this->requestData['code'], $id)) {
                    Response::error("Mã khuyến mãi đã tồn tại", 409);
                }
                
                $data['code'] = strtoupper(trim($this->requestData['code']));
            }
            
            // Discount type
            if (isset($this->requestData['discount_type'])) {
                $validator->in('discount_type', $this->requestData['discount_type'], ['percentage', 'fixed'], 'Loại giảm giá');
                $data['discount_type'] = $this->requestData['discount_type'];
            }
            
            // Discount value
            if (isset($this->requestData['discount_value'])) {
                $validator->decimal('discount_value', $this->requestData['discount_value'], 2, 'Giá trị giảm');
                $data['discount_value'] = $this->requestData['discount_value'];
            }
            
            // Start date
            if (isset($this->requestData['start_date'])) {
                $data['start_date'] = $this->requestData['start_date'];
            }
            
            // End date
            if (isset($this->requestData['end_date'])) {
                $data['end_date'] = $this->requestData['end_date'];
            }
            
            if ($validator->fails()) {
                Response::validationError($validator->getErrors());
            }
            
            // Validate dates if both provided
            if (isset($data['start_date']) && isset($data['end_date'])) {
                $startDate = new DateTime($data['start_date']);
                $endDate = new DateTime($data['end_date']);
                
                if ($endDate <= $startDate) {
                    Response::error("Ngày kết thúc phải sau ngày bắt đầu", 400);
                }
            }
            
            // Update promotion
            if (empty($data)) {
                Response::error("Không có dữ liệu để cập nhật", 400);
            }
            
            $result = $this->promotionModel->update($id, $data);
            
            if ($result) {
                $promotion = $this->promotionModel->getById($id);
                Response::success($promotion, "Cập nhật khuyến mãi thành công");
            } else {
                Response::serverError("Không thể cập nhật khuyến mãi");
            }
            
        } catch (Exception $e) {
            Response::serverError("Lỗi khi cập nhật khuyến mãi: " . $e->getMessage());
        }
    }
    
    /**
     * DELETE /api/promotions/{id}
     * Delete promotion
     */
    private function deletePromotion($id) {
        // Require admin authentication
        Auth::requireAdmin();
        
        try {
            // Check if promotion exists
            $existing = $this->promotionModel->getById($id);
            if (!$existing) {
                Response::notFound("Không tìm thấy khuyến mãi");
            }
            
            // Delete promotion
            $result = $this->promotionModel->delete($id);
            
            if ($result) {
                Response::success(null, "Xóa khuyến mãi thành công");
            } else {
                Response::serverError("Không thể xóa khuyến mãi");
            }
            
        } catch (Exception $e) {
            Response::serverError("Lỗi khi xóa khuyến mãi: " . $e->getMessage());
        }
    }
    
    /**
     * POST /api/promotions/apply
     * Apply promotion code and calculate discount
     */
    private function applyPromotion() {
        // Validate input
        $validator = new Validator();
        $validator->required('code', $this->requestData['code'] ?? null, 'Mã khuyến mãi');
        $validator->required('total_amount', $this->requestData['total_amount'] ?? null, 'Tổng tiền');
        
        if (!$validator->fails()) {
            $validator->decimal('total_amount', $this->requestData['total_amount'], 2, 'Tổng tiền');
        }
        
        if ($validator->fails()) {
            Response::validationError($validator->getErrors());
        }
        
        try {
            $code = strtoupper(trim($this->requestData['code']));
            $totalAmount = floatval($this->requestData['total_amount']);
            
            // Validate promotion code
            $validation = $this->promotionModel->validateCode($code);
            
            if (!$validation['valid']) {
                Response::error($validation['message'], 400);
            }
            
            $promotion = $validation['promotion'];
            
            // Calculate discount
            $discountAmount = $this->promotionModel->calculateDiscount($promotion['promotion_id'], $totalAmount);
            $finalAmount = $totalAmount - $discountAmount;
            
            Response::success([
                'promotion' => $promotion,
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount
            ], "Áp dụng mã khuyến mãi thành công");
            
        } catch (Exception $e) {
            Response::serverError("Lỗi khi áp dụng mã khuyến mãi: " . $e->getMessage());
        }
    }
    
    /**
     * GET /api/promotions/validate?code=XXX
     * Validate promotion code (without calculating discount)
     */
    private function validatePromotion() {
        $code = $_GET['code'] ?? null;
        
        if (!$code) {
            Response::error("Mã khuyến mãi không được để trống", 400);
        }
        
        try {
            $code = strtoupper(trim($code));
            $validation = $this->promotionModel->validateCode($code);
            
            if ($validation['valid']) {
                Response::success([
                    'valid' => true,
                    'promotion' => $validation['promotion']
                ], $validation['message']);
            } else {
                Response::success([
                    'valid' => false,
                    'promotion' => null
                ], $validation['message']);
            }
            
        } catch (Exception $e) {
            Response::serverError("Lỗi khi kiểm tra mã khuyến mãi: " . $e->getMessage());
        }
    }
}
?>
