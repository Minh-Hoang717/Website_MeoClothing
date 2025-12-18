"use strict";

(function () {
  const API_BASE = "/api/promotions";
  const tableBody = () => document.querySelector("#promoTable tbody");
  const statusText = () => document.querySelector("#statusText");

  function getToken() {
    return localStorage.getItem("adminToken") || "";
  }

  function setToken(t) {
    localStorage.setItem("adminToken", t || "");
  }

  function authHeaders() {
    const token = getToken();
    return token ? { Authorization: "Bearer " + token } : {};
  }

  function fmtDateTimeLocal(dt) {
    // expects 'YYYY-MM-DD HH:MM:SS' -> 'YYYY-MM-DDTHH:MM'
    if (!dt) return "";
    if (dt.includes("T")) return dt.slice(0, 16);
    return dt.replace(" ", "T").slice(0, 16);
  }

  function toApiDate(dtLocal) {
    // 'YYYY-MM-DDTHH:MM' -> 'YYYY-MM-DD HH:MM:SS'
    if (!dtLocal) return "";
    return dtLocal.replace("T", " ") + ":00";
  }

  async function fetchPromotions() {
    const filter = document.querySelector("#filterSelect").value;
    const url = `${API_BASE}?filter=${encodeURIComponent(
      filter
    )}&page=1&pageSize=50`;
    const res = await fetch(url, {
      headers: { "Content-Type": "application/json", ...authHeaders() },
    });
    const json = await res.json();
    if (!json.success) {
      throw new Error(json.message || "Fetch failed");
    }
    renderTable(json.data || []);
  }

  function renderTable(rows) {
    const tbody = tableBody();
    tbody.innerHTML = "";
    rows.forEach((row) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${row.promotion_id}</td>
        <td>${row.code}</td>
        <td>${row.discount_type}</td>
        <td>${row.discount_value}</td>
        <td>${row.start_date || ""}</td>
        <td>${row.end_date || ""}</td>
        <td class="right">
          <button class="btn secondary" data-action="edit" data-id="${
            row.promotion_id
          }">Sửa</button>
          <button class="btn danger" data-action="delete" data-id="${
            row.promotion_id
          }">Xóa</button>
        </td>`;
      tbody.appendChild(tr);
    });
  }

  function fillForm(row) {
    document.querySelector("#promotion_id").value = row.promotion_id || "";
    document.querySelector("#code").value = row.code || "";
    document.querySelector("#discount_type").value =
      row.discount_type || "percentage";
    document.querySelector("#discount_value").value = row.discount_value || "";
    document.querySelector("#start_date").value = fmtDateTimeLocal(
      row.start_date
    );
    document.querySelector("#end_date").value = fmtDateTimeLocal(row.end_date);
    document.querySelector("#formTitle").textContent = "Cập nhật khuyến mãi";
  }

  function resetForm() {
    document.querySelector("#promotion_id").value = "";
    document.querySelector("#code").value = "";
    document.querySelector("#discount_type").value = "percentage";
    document.querySelector("#discount_value").value = "";
    document.querySelector("#start_date").value = "";
    document.querySelector("#end_date").value = "";
    document.querySelector("#formTitle").textContent = "Thêm khuyến mãi";
  }

  async function createOrUpdate(e) {
    e.preventDefault();
    const id = document.querySelector("#promotion_id").value;
    const payload = {
      code: document.querySelector("#code").value.trim(),
      discount_type: document.querySelector("#discount_type").value,
      discount_value: document.querySelector("#discount_value").value,
      start_date: toApiDate(document.querySelector("#start_date").value),
      end_date: toApiDate(document.querySelector("#end_date").value),
    };

    const options = {
      method: id ? "PUT" : "POST",
      headers: { "Content-Type": "application/json", ...authHeaders() },
      body: JSON.stringify(payload),
    };

    const url = id ? `${API_BASE}/${id}` : API_BASE;
    const res = await fetch(url, options);
    const json = await res.json();

    if (!json.success) {
      alert(json.message || "Có lỗi xảy ra");
      return;
    }

    resetForm();
    await fetchPromotions();
  }

  async function onTableClick(e) {
    const btn = e.target.closest("button");
    if (!btn) return;
    const id = btn.getAttribute("data-id");
    const action = btn.getAttribute("data-action");

    if (action === "delete") {
      if (!confirm("Bạn có chắc muốn xóa khuyến mãi này?")) return;
      const res = await fetch(`${API_BASE}/${id}`, {
        method: "DELETE",
        headers: { ...authHeaders() },
      });
      const json = await res.json();
      if (!json.success) {
        alert(json.message || "Xóa thất bại");
        return;
      }
      await fetchPromotions();
    }

    if (action === "edit") {
      const res = await fetch(`${API_BASE}/${id}`, {
        headers: { ...authHeaders() },
      });
      const json = await res.json();
      if (!json.success) {
        alert(json.message || "Không lấy được dữ liệu");
        return;
      }
      fillForm(json.data);
    }
  }

  async function validateCode() {
    const code = (document.querySelector("#test_code").value || "").trim();
    if (!code) {
      alert("Nhập mã cần kiểm tra");
      return;
    }
    const res = await fetch(
      `${API_BASE}/validate?code=${encodeURIComponent(code)}`
    );
    const json = await res.json();
    const el = document.querySelector("#testResult");
    if (json.success && json.data && json.data.valid) {
      el.textContent = `Hợp lệ: ${json.message} (type=${json.data.promotion.discount_type}, value=${json.data.promotion.discount_value})`;
    } else {
      el.textContent = `Không hợp lệ: ${json.message}`;
    }
  }

  async function applyCode() {
    const code = (document.querySelector("#test_code").value || "").trim();
    const total = parseFloat(
      document.querySelector("#test_total").value || "0"
    );
    if (!code || !total) {
      alert("Nhập mã và tổng tiền");
      return;
    }
    const res = await fetch(`${API_BASE}/apply`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ code, total_amount: total }),
    });
    const json = await res.json();
    const el = document.querySelector("#testResult");
    if (json.success) {
      el.textContent = `Giảm ${json.data.discount_amount} VND, thanh toán ${json.data.final_amount} VND`;
    } else {
      el.textContent = `Không áp dụng được: ${json.message}`;
    }
  }

  function initToken() {
    const input = document.querySelector("#adminToken");
    input.value = getToken();
    document.querySelector("#saveTokenBtn").addEventListener("click", () => {
      setToken(input.value.trim());
      statusText().textContent = "Đã lưu token";
      setTimeout(() => (statusText().textContent = ""), 1500);
    });
  }

  function bindEvents() {
    document
      .querySelector("#filterSelect")
      .addEventListener("change", fetchPromotions);
    document
      .querySelector("#promoForm")
      .addEventListener("submit", createOrUpdate);
    document.querySelector("#resetBtn").addEventListener("click", resetForm);
    document
      .querySelector("#promoTable")
      .addEventListener("click", onTableClick);
    document
      .querySelector("#btnValidate")
      .addEventListener("click", validateCode);
    document.querySelector("#btnApply").addEventListener("click", applyCode);
  }

  async function boot() {
    initToken();
    bindEvents();
    await fetchPromotions();
  }

  document.addEventListener("DOMContentLoaded", boot);
})();
