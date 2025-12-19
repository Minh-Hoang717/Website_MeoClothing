// ======================== CART FUNCTIONS ========================

/**
 * Add product to cart
 */
function addToCart(event, productId, variantId = null, quantity = 1) {
  if (event) {
    event.preventDefault();
  }

  // If no variant specified, get first available variant
  if (!variantId) {
    fetch(`/Meow_Clothing_Store/public/home/get-variants/${productId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.data.length > 0) {
          const firstVariant = data.data[0];
          addToCartRequest(firstVariant.variant_id, quantity);
        } else {
          alert("Sản phẩm không có biến thể");
        }
      })
      .catch((error) => console.error("Error:", error));
  } else {
    addToCartRequest(variantId, quantity);
  }
}

/**
 * Send add to cart request
 */
function addToCartRequest(variantId, quantity) {
  fetch("/Meow_Clothing_Store/public/cart/add", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      variant_id: variantId,
      quantity: quantity,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert(data.message);
        updateCartCount(data.cartCount);
      } else {
        alert(data.error || "Có lỗi xảy ra");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Lỗi kết nối");
    });
}

/**
 * Update cart item quantity
 */
function updateCartItem(variantId, quantity) {
  fetch("/Meow_Clothing_Store/public/cart/update", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      variant_id: variantId,
      quantity: parseInt(quantity),
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        location.reload();
      } else {
        alert(data.error || "Có lỗi xảy ra");
      }
    })
    .catch((error) => console.error("Error:", error));
}

/**
 * Remove item from cart
 */
function removeFromCart(variantId) {
  if (confirm("Bạn chắc chắn muốn xoá sản phẩm này?")) {
    fetch("/Meow_Clothing_Store/public/cart/remove", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        variant_id: variantId,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          location.reload();
        } else {
          alert(data.error || "Có lỗi xảy ra");
        }
      })
      .catch((error) => console.error("Error:", error));
  }
}

/**
 * Apply promotion code
 */
function applyPromotion() {
  const code = document.getElementById("promotionCode")?.value;

  if (!code) {
    alert("Vui lòng nhập mã khuyến mãi");
    return;
  }

  fetch("/Meow_Clothing_Store/public/cart/apply-promotion", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      code: code,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert(data.message);
        location.reload();
      } else {
        alert(data.error || "Có lỗi xảy ra");
      }
    })
    .catch((error) => console.error("Error:", error));
}

/**
 * Update cart count in header
 */
function updateCartCount(count) {
  const badge = document.getElementById("cartCount");
  if (badge) {
    badge.textContent = count;
  }
}

// ======================== SEARCH FUNCTIONS ========================

/**
 * Search products
 */
document.addEventListener("DOMContentLoaded", function () {
  const searchBox = document.getElementById("searchBox");

  if (searchBox) {
    searchBox.addEventListener("keyup", function () {
      const query = this.value;

      if (query.length < 2) {
        return;
      }

      fetch(
        `/Meow_Clothing_Store/public/home/search?q=${encodeURIComponent(query)}`
      )
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            displaySearchResults(data.data);
          }
        })
        .catch((error) => console.error("Error:", error));
    });
  }
});

/**
 * Display search results
 */
function displaySearchResults(products) {
  console.log("Search results:", products);
  // Implement search results display UI here
}

// ======================== FORM VALIDATION ========================

/**
 * Validate email
 */
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

/**
 * Validate password
 */
function validatePassword(password) {
  return password.length >= 6;
}

// ======================== ADMIN FUNCTIONS ========================

/**
 * Update order status
 */
function updateOrderStatus(orderId, status) {
  fetch("/Meow_Clothing_Store/public/admin/update-order-status", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      order_id: orderId,
      status: status,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert(data.message);
        location.reload();
      } else {
        alert(data.error || "Có lỗi xảy ra");
      }
    })
    .catch((error) => console.error("Error:", error));
}

// ======================== UTILITY FUNCTIONS ========================

/**
 * Format currency
 */
function formatCurrency(amount) {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(amount);
}

/**
 * Get query parameter
 */
function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}
