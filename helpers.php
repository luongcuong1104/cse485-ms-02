<?php
/**
 * Tính tổng giá trị dòng cho một sản phẩm duy nhất. (price * quantity).
 *
 * @param array $product
 * @return int
 */
function lineTotal(array $product): int {
    return (int) (($product['price'] ?? 0) * ($product['qty'] ?? 0));
}

/**
 * Tính toán tổng giá trị hàng tồn kho của tất cả sản phẩm.
 *
 * @param array $products
 * @return int
 */
function inventoryValue(array $products): int {
    $total = 0;
    foreach ($products as $product) {
        // Ví dụ về continue: bỏ qua các sản phẩm không hợp lệ
        if (!isset($product['price']) || !isset($product['qty'])) {
            continue;
        }
        $total += lineTotal($product);
    }
    return $total;
}

/**
 * Tìm kiếm sản phẩm theo mã SKU.
 *
 * @param array $products
 * @param string $sku
 * @return array|null
 */
function findProductBySku(array $products, string $sku): ?array {
    $foundProduct = null;
    foreach ($products as $product) {
        if (($product['sku'] ?? '') === $sku) {
            $foundProduct = $product;
            break; // Sử dụng break để thoát khỏi vòng lặp sớm khi tìm thấy
        }
    }
    return $foundProduct;
}

/**
 * Đếm số lượng sản phẩm trong một danh mục nhất định.
 *
 * @param array $products
 * @param int $categoryId
 * @return int
 */
function countByCategory(array $products, int $categoryId): int {
    $count = 0;
    foreach ($products as $product) {
        // Sử dụng câu lệnh if/elseif và logic tiếp tục.
        if (($product['category_id'] ?? 0) === $categoryId) {
            $count++;
        } elseif ($categoryId === 0) {
            // Nếu categoryId bằng 0, hãy đếm tất cả sản phẩm
            $count++;
        } else {
            continue;
        }
    }
    return $count;
}

/**
 *  Xác định trạng thái mức tồn kho dựa trên số lượng.
 *
 * @param array $product
 * @return string
 */
function stockLevel(array $product): string {
    $qty = $product['qty'] ?? 0;
    
    // Sử dụng câu lệnh switch case để xác định trạng thái
    switch (true) {
        case ($qty >= 5):
            return "Du";
        case ($qty >= 2):
            return "Sap het";
        default:
            return "Can nhap";
    }
}

/**
 * Lọc sản phẩm theo danh mục.
 *
 * @param array $products
 * @param int|null $categoryId
 * @return array
 */
function filterByCategory(array $products, ?int $categoryId): array {
    if ($categoryId === null) {
        return $products;
    }
    $filtered = [];
    foreach ($products as $product) {
        if (($product['category_id'] ?? null) === $categoryId) {
            $filtered[] = $product;
        }
    }
    return $filtered;
}

/**
 * Xếp hạng quy mô giá trị kho.
 *
 * @param int $totalValue
 * @return string
 */
function rankInventory(int $totalValue): string {
    if ($totalValue < 15000000) {
        return "Nho";
    } elseif ($totalValue < 35000000) {
        return "Trung binh";
    } else {
        return "Lon";
    }
}

/**
 * Xuất mã HTML <tr>...</tr> cho danh sách sản phẩm.
 *
 * @param array $products
 * @param array $categoryMap
 * @return void
 */
function renderProductRows(array $products, array $categoryMap): void {
    foreach ($products as $product) {
        $lineTotalVal = lineTotal($product);
        $catName = $categoryMap[$product['category_id'] ?? 0] ?? 'Khác';
        $status = stockLevel($product);
        
        $statusClass = 'status-default';
        if ($status === 'Du') {
            $statusClass = 'status-ok';
        } elseif ($status === 'Sap het') {
            $statusClass = 'status-warning';
        } else {
            $statusClass = 'status-danger';
        }
        
        echo '<tr>';
        echo '<td><span class="sku-badge">' . htmlspecialchars($product['sku']) . '</span></td>';
        echo '<td><span class="category-name" style="font-weight: 500; color: var(--text-muted);">' . htmlspecialchars($catName) . '</span></td>';
        echo '<td><span class="product-name">' . htmlspecialchars($product['name']) . '</span></td>';
        echo '<td class="price-col">' . number_format($product['price'], 0, ',', '.') . ' ₫</td>';
        echo '<td class="qty-col">' . number_format($product['qty']) . '</td>';
        echo '<td style="text-align: center;"><span class="status-badge ' . $statusClass . '">' . htmlspecialchars($status) . '</span></td>';
        echo '<td class="total-col">' . number_format($lineTotalVal, 0, ',', '.') . ' ₫</td>';
        echo '</tr>';
    }
}

