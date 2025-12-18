"use strict";

(function () {
  const API_BASE = "/api";

  // Mock cart data structure (in real app, comes from sessionStorage/cart service)
  let cart = {
    items: [
      {
        variant_id: 1,
        product_name: "Black Jacket",
        size: "M",
        color: "Black",
        quantity: 1,
        unit_price: 450000,
      },
      {
        variant_id: 2,
        product_name: "White T-Shirt",
        size: "L",
        color: "White",
        quantity: 2,
        unit_price: 150000,
      },
    ],
  };

  let promoApplied = null;
  let subtotal = 0;
  let discount = 0;

  function calculateSubtotal() {
    subtotal = cart.items.reduce(
      (sum, item) => sum + item.quantity * item.unit_price,
      0
    );
    return subtotal;
  }

  function formatCurrency(num) {
    return new Intl.NumberFormat("vi-VN", {
      style: "currency",
      currency: "VND",
    }).format(num);
  }

  function renderCart() {
    calculateSubtotal();
    const container = document.querySelector("#cartItems");
    const html = cart.items
      .map(
        (item) => `
      <div class="cart-item">
        <div>
          <strong>${item.product_name}</strong><br>
          <small>${item.size} / ${item.color} x ${item.quantity}</small>
        </div>
        <div class="right">${formatCurrency(
          item.quantity * item.unit_price
        )}</div>
      </div>
    `
      )
      .join("");
    container.innerHTML = html;
    updateSummary();
  }

  function updateSummary() {
    const total = subtotal - discount;
    document.querySelector("#subtotal").textContent = formatCurrency(subtotal);
    document.querySelector("#discount").textContent = formatCurrency(discount);
    document.querySelector("#total").textContent = formatCurrency(total);
  }

  async function applyPromo() {
    const code = document.querySelector("#promoCode").value.trim();
    const alert = document.querySelector("#promoAlert");

    if (!code) {
      alert.innerHTML =
        '<div class="alert error">Vui lòng nhập mã khuyến mãi</div>';
      return;
    }

    try {
      alert.innerHTML = '<div class="alert info">Đang kiểm tra...</div>';
      const res = await fetch(`${API_BASE}/promotions/apply`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ code, total_amount: subtotal }),
      });

      const json = await res.json();

      if (!json.success) {
        alert.innerHTML = `<div class="alert error">${json.message}</div>`;
        discount = 0;
        promoApplied = null;
      } else {
        discount = json.data.discount_amount;
        promoApplied = code;
        alert.innerHTML = `<div class="alert success">✓ Áp dụng mã thành công! Tiết kiệm ${formatCurrency(
          discount
        )}</div>`;
      }

      updateSummary();
    } catch (err) {
      alert.innerHTML = `<div class="alert error">Lỗi: ${err.message}</div>`;
    }
  }

  async function submitPayment() {
    const fullName = document.querySelector("#fullName").value.trim();
    const phone = document.querySelector("#phone").value.trim();
    const email = document.querySelector("#email").value.trim();
    const address = document.querySelector("#address").value.trim();
    const alertDiv = document.querySelector("#customerAlert");
    const paymentAlertDiv = document.querySelector("#paymentAlert");

    // Validate
    const errors = [];
    if (!fullName) errors.push("Họ và tên");
    if (!phone) errors.push("Số điện thoại");
    if (!address) errors.push("Địa chỉ");

    if (errors.length) {
      alertDiv.innerHTML = `<div class="alert error">Vui lòng nhập: ${errors.join(
        ", "
      )}</div>`;
      return;
    }

    try {
      const paymentBtn = document.querySelector("#paymentBtn");
      paymentBtn.disabled = true;
      paymentAlertDiv.innerHTML =
        '<div class="alert info">Đang xử lý thanh toán...</div>';

      // Step 1: Create order (mock - you'd call /api/orders endpoint)
      // For now, use a dummy order ID
      const orderId = Math.floor(Math.random() * 100000);

      // Step 2: Request VNPay payment URL
      const payRes = await fetch(`${API_BASE}/payments/process`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ order_id: orderId }),
      });

      const payJson = await payRes.json();

      if (!payJson.success) {
        paymentAlertDiv.innerHTML = `<div class="alert error">${payJson.message}</div>`;
        paymentBtn.disabled = false;
        return;
      }

      // Step 3: Redirect to VNPay
      const paymentUrl = payJson.data.payment_url;
      window.location.href = paymentUrl;
    } catch (err) {
      paymentAlertDiv.innerHTML = `<div class="alert error">Lỗi: ${err.message}</div>`;
      document.querySelector("#paymentBtn").disabled = false;
    }
  }

  function init() {
    renderCart();
    document
      .querySelector("#applyPromoBtn")
      .addEventListener("click", applyPromo);
    document
      .querySelector("#paymentBtn")
      .addEventListener("click", submitPayment);

    // Allow Enter key in promo field
    document.querySelector("#promoCode").addEventListener("keypress", (e) => {
      if (e.key === "Enter") applyPromo();
    });
  }

  document.addEventListener("DOMContentLoaded", init);
})();
