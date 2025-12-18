<?php
/**
 * VNPay Service
 * Xử lý tích hợp VNPay Payment Gateway
 */

class VNPayService {
    
    private $tmnCode;
    private $hashSecret;
    private $url;
    private $returnUrl;
    
    public function __construct() {
        $this->tmnCode = VNPAY_TMN_CODE;
        $this->hashSecret = VNPAY_HASH_SECRET;
        $this->url = VNPAY_URL;
        $this->returnUrl = VNPAY_RETURN_URL;
    }
    
    /**
     * Create payment URL
     */
    public function createPaymentUrl($orderId, $amount, $orderInfo, $ipAddr) {
        $vnp_TxnRef = $orderId . '_' . time(); // Mã giao dịch
        $vnp_OrderInfo = $orderInfo;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $amount * 100; // VNPay yêu cầu số tiền * 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = ''; // Để trống để hiển thị tất cả phương thức
        $vnp_IpAddr = $ipAddr;
        
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->tmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $this->returnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );
        
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        
        // Sort data
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $this->url . "?" . $query;
        
        if (isset($this->hashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        
        return [
            'url' => $vnp_Url,
            'txnRef' => $vnp_TxnRef
        ];
    }
    
    /**
     * Validate return data from VNPay
     */
    public function validateReturnData($inputData) {
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
        
        ksort($inputData);
        $hashdata = "";
        $i = 0;
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        $secureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
        
        return $secureHash === $vnp_SecureHash;
    }
    
    /**
     * Get transaction status from response code
     */
    public function getTransactionStatus($responseCode) {
        $status = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)'
        ];
        
        return $status[$responseCode] ?? 'Lỗi không xác định';
    }
    
    /**
     * Check if transaction is successful
     */
    public function isSuccessful($responseCode) {
        return $responseCode === '00';
    }
    
    /**
     * Parse transaction reference to get order ID
     */
    public function parseOrderIdFromTxnRef($txnRef) {
        $parts = explode('_', $txnRef);
        return $parts[0] ?? null;
    }
}
?>
