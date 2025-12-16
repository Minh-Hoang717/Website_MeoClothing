<?php
/**
 * Payment Controller
 * Xử lý API requests cho payments và tích hợp VNPay
 */

require_once __DIR__ . '/../services/VNPayService.php';

class PaymentController extends BaseController {
    
    private $paymentModel;
    private $orderModel;
    private $vnpayService;
    
    public function __construct($db) {
        parent::__construct($db);
        $this->paymentModel = new Payment($db);
        $this->orderModel = new Order($db);
        $this->vnpayService = new VNPayService();
    }
    
    /**
     * Process request based on HTTP method
     */
    public function processRequest() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriParts = explode('/', trim($uri, '/'));
        
        // Remove 'api' and 'payments' parts
        array_shift($uriParts); // 'api'
        array_shift($uriParts); // 'payments'
        
        $action = $uriParts[0] ?? null;
        $id = $uriParts[1] ?? null;
        
        // Handle special endpoints
        if ($action === 'vnpay-return' && $this->requestMethod === 'GET') {
            $this->handleVNPayReturn();
            return;
        }
        
        if ($action === 'vnpay-ipn' && $this->requestMethod === 'POST') {
            $this->handleVNPayIPN();
            return;
        }
        
        switch ($this->requestMethod) {
            case 'GET':
                if ($action && !$id) {
                    // GET /api/payments/{id}
                    $this->getPaymentById($action);
                } else {
                    // GET /api/payments
                    $this->getAllPayments();
                }
                break;
                
            case 'POST':
                if ($action === 'process' || $action === 'create') {
                    // POST /api/payments/process
                    $this->processPayment();
                } else {
                    Response::notFound("Endpoint not found");
                }
                break;
                
            default:
                $this->methodNotAllowed();
                break;
        }
    }
    
    /**
     * GET /api/payments
     * Get all payments (Admin only)
     */
    private function getAllPayments() {
        Auth::requireAdmin();
        
        $params = $this->getPaginationParams();
        
        try {
            $data = $this->paymentModel->getAll($params['page'], $params['pageSize'], 'payment_date DESC');
            $total = $this->paymentModel->getCount();
            
            Response::paginated($data, $total, $params['page'], $params['pageSize'], "Lấy danh sách thanh toán thành công");
            
        } catch (Exception $e) {
            Response::serverError("Lỗi khi lấy danh sách thanh toán: " . $e->getMessage());
        }
    }
    
    /**
     * GET /api/payments/{id}
     * Get payment by ID
     */
    private function getPaymentById($id) {
        try {
            $payment = $this->paymentModel->getPaymentWithOrder($id);
            
            if (!$payment) {
                Response::notFound("Không tìm thấy thanh toán");
            }
            
            Response::success($payment, "Lấy thông tin thanh toán thành công");
            
        } catch (Exception $e) {
            Response::serverError("Lỗi khi lấy thông tin thanh toán: " . $e->getMessage());
        }
    }
    
    /**
     * POST /api/payments/process
     * Create payment and redirect to VNPay
     */
    private function processPayment() {
        // Validate input
        $validator = new Validator();
        $validator->required('order_id', $this->requestData['order_id'] ?? null, 'Mã đơn hàng');
        
        if ($validator->fails()) {
            Response::validationError($validator->getErrors());
        }
        
        try {
            $orderId = $this->requestData['order_id'];
            
            // Get order
            $order = $this->orderModel->getOrderWithCustomer($orderId);
            
            if (!$order) {
                Response::notFound("Không tìm thấy đơn hàng");
            }
            
            // Check if order already has payment
            $existingPayment = $this->paymentModel->getByOrderId($orderId);
            
            if ($existingPayment) {
                Response::error("Đơn hàng đã có thanh toán", 400);
            }
            
            // Check order status
            if ($order['status'] !== 'pending') {
                Response::error("Đơn hàng không ở trạng thái chờ thanh toán", 400);
            }
            
            // Get client IP
            $ipAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            
            // Create payment URL
            $orderInfo = "Thanh toán đơn hàng #" . $orderId . " - " . $order['full_name'];
            $amount = floatval($order['total_amount']);
            
            $paymentData = $this->vnpayService->createPaymentUrl($orderId, $amount, $orderInfo, $ipAddr);
            
            Response::success([
                'payment_url' => $paymentData['url'],
                'txn_ref' => $paymentData['txnRef'],
                'order_id' => $orderId,
                'amount' => $amount
            ], "Tạo thanh toán thành công");
            
        } catch (Exception $e) {
            Response::serverError("Lỗi khi tạo thanh toán: " . $e->getMessage());
        }
    }
    
    /**
     * GET /api/payments/vnpay-return
     * Handle return from VNPay (user redirect)
     */
    private function handleVNPayReturn() {
        try {
            $inputData = $_GET;
            
            // Validate secure hash
            if (!$this->vnpayService->validateReturnData($inputData)) {
                // Redirect to error page
                header("Location: /payment-error.html?message=invalid_signature");
                exit;
            }
            
            $vnp_TxnRef = $inputData['vnp_TxnRef'];
            $vnp_ResponseCode = $inputData['vnp_ResponseCode'];
            $vnp_Amount = $inputData['vnp_Amount'] / 100; // Convert back to VND
            $vnp_BankCode = $inputData['vnp_BankCode'] ?? '';
            $vnp_TransactionNo = $inputData['vnp_TransactionNo'] ?? '';
            
            // Get order ID from transaction reference
            $orderId = $this->vnpayService->parseOrderIdFromTxnRef($vnp_TxnRef);
            
            if (!$orderId) {
                header("Location: /payment-error.html?message=invalid_order");
                exit;
            }
            
            // Get order
            $order = $this->orderModel->getById($orderId);
            
            if (!$order) {
                header("Location: /payment-error.html?message=order_not_found");
                exit;
            }
            
            // Check if payment successful
            if ($this->vnpayService->isSuccessful($vnp_ResponseCode)) {
                
                // Check if payment already recorded
                $existingPayment = $this->paymentModel->getByOrderId($orderId);
                
                if (!$existingPayment) {
                    // Record payment
                    $paymentData = [
                        'order_id' => $orderId,
                        'payment_date' => date('Y-m-d H:i:s'),
                        'payment_method' => 'VNPay - ' . $vnp_BankCode,
                        'amount' => $vnp_Amount,
                        'transaction_code' => $vnp_TransactionNo
                    ];
                    
                    $this->paymentModel->create($paymentData);
                    
                    // Update order status
                    $this->orderModel->updateStatus($orderId, 'confirmed');
                }
                
                // Redirect to success page
                header("Location: /payment-success.html?order_id=" . $orderId . "&amount=" . $vnp_Amount);
                exit;
                
            } else {
                // Payment failed
                $message = $this->vnpayService->getTransactionStatus($vnp_ResponseCode);
                header("Location: /payment-error.html?message=" . urlencode($message));
                exit;
            }
            
        } catch (Exception $e) {
            header("Location: /payment-error.html?message=" . urlencode($e->getMessage()));
            exit;
        }
    }
    
    /**
     * POST /api/payments/vnpay-ipn
     * Handle IPN (Instant Payment Notification) from VNPay
     */
    private function handleVNPayIPN() {
        try {
            $inputData = $_GET; // VNPay sends IPN as GET parameters
            
            // Validate secure hash
            if (!$this->vnpayService->validateReturnData($inputData)) {
                Response::error("Invalid signature", 400);
            }
            
            $vnp_TxnRef = $inputData['vnp_TxnRef'];
            $vnp_ResponseCode = $inputData['vnp_ResponseCode'];
            $vnp_Amount = $inputData['vnp_Amount'] / 100;
            $vnp_BankCode = $inputData['vnp_BankCode'] ?? '';
            $vnp_TransactionNo = $inputData['vnp_TransactionNo'] ?? '';
            
            // Get order ID
            $orderId = $this->vnpayService->parseOrderIdFromTxnRef($vnp_TxnRef);
            
            if (!$orderId) {
                Response::error("Invalid order ID", 400);
            }
            
            // Get order
            $order = $this->orderModel->getById($orderId);
            
            if (!$order) {
                Response::error("Order not found", 404);
            }
            
            // Check amount
            if (floatval($order['total_amount']) != $vnp_Amount) {
                Response::error("Invalid amount", 400);
            }
            
            // Check if payment successful
            if ($this->vnpayService->isSuccessful($vnp_ResponseCode)) {
                
                // Check if payment already recorded
                $existingPayment = $this->paymentModel->getByOrderId($orderId);
                
                if (!$existingPayment) {
                    // Record payment
                    $paymentData = [
                        'order_id' => $orderId,
                        'payment_date' => date('Y-m-d H:i:s'),
                        'payment_method' => 'VNPay - ' . $vnp_BankCode,
                        'amount' => $vnp_Amount,
                        'transaction_code' => $vnp_TransactionNo
                    ];
                    
                    $this->paymentModel->create($paymentData);
                    
                    // Update order status
                    $this->orderModel->updateStatus($orderId, 'confirmed');
                    
                    // Return success to VNPay
                    echo json_encode(['RspCode' => '00', 'Message' => 'Confirm Success']);
                } else {
                    // Already recorded
                    echo json_encode(['RspCode' => '02', 'Message' => 'Order already confirmed']);
                }
                
            } else {
                // Payment failed
                echo json_encode(['RspCode' => '00', 'Message' => 'Confirm Received']);
            }
            
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['RspCode' => '99', 'Message' => $e->getMessage()]);
            exit;
        }
    }
}
?>
