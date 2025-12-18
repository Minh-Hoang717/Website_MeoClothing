#!/bin/bash
# Quick Start Commands - Copy & Paste to test the API

echo "=== Meo Clothing Store - API Testing ==="
echo ""

# Test database connectivity
echo "1. Testing database connection..."
curl -s http://localhost/api/test-db.php | jq '.' || echo "Database test: Check if server is running at http://localhost"
echo ""

# List all promotions
echo "2. Listing all promotions..."
curl -s http://localhost/api/promotions | jq '.' || echo "Failed"
echo ""

# Get active promotions only
echo "3. Getting active promotions..."
curl -s "http://localhost/api/promotions?filter=active" | jq '.' || echo "Failed"
echo ""

# Create a new promotion (requires admin token)
echo "4. Creating a promotion (requires admin token)..."
PROMO_CODE="TEST$(date +%s)"
curl -s -X POST http://localhost/api/promotions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer admin-token" \
  -d "{
    \"code\": \"$PROMO_CODE\",
    \"discount_type\": \"percentage\",
    \"discount_value\": 15,
    \"start_date\": \"$(date -d 'today' '+%Y-%m-%d') 00:00:00\",
    \"end_date\": \"$(date -d '+30 days' '+%Y-%m-%d') 23:59:59\"
  }" | jq '.'
echo ""

# Apply promo code
echo "5. Applying promo code..."
curl -s -X POST http://localhost/api/promotions/apply \
  -H "Content-Type: application/json" \
  -d '{
    "code": "SUMMER10",
    "total_amount": 500000
  }' | jq '.'
echo ""

# Revenue report
echo "6. Getting revenue report..."
curl -s "http://localhost/api/reports/revenue?group_by=daily" | jq '.'
echo ""

# Customer report
echo "7. Getting customer stats..."
curl -s "http://localhost/api/reports/customers?metric=summary" | jq '.'
echo ""

# Product report
echo "8. Getting product stats..."
curl -s "http://localhost/api/reports/products?metric=best_selling" | jq '.'
echo ""

echo "=== Testing Complete ==="
