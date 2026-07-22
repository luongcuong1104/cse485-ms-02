<?php
// index.php
require_once 'data.php';
require_once 'helpers.php';

// Xây dựng mảng phụ map category_id sang tên danh mục chữ
$categoryMap = [];
foreach ($categories as $cat) {
    $categoryMap[$cat['id']] = $cat['name'];
}

// Đọc bộ lọc SKU và Category từ GET (2 cách lọc)
$selectedSku = isset($_GET['sku']) ? trim($_GET['sku']) : '';
$selectedCategoryId = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null;

if ($selectedSku !== '') {
    $found = findProductBySku($products, $selectedSku);
    $filteredProducts = $found ? [$found] : [];
} else {
    $filteredProducts = filterByCategory($products, $selectedCategoryId);
}

// Tính tổng giá trị kho và số sản phẩm bằng các hàm helper (dựa trên toàn bộ sản phẩm)
$inventoryValue = inventoryValue($products);
$productCount = count($products);
$inventoryRank = rankInventory($inventoryValue);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniShop — Catalog (Buoi 1)</title>
    <!-- Google Fonts: Inter for premium typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- External CSS link -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
    <div class="container">
        <div class="header">
            <h1><?php echo htmlspecialchars(STORE_NAME); ?> — Catalog</h1>
            <p>Danh sách sản phẩm</p>
        </div>

        <!-- Thanh công cụ lọc sản phẩm (Nhiệm vụ A) -->
        <div class="toolbar">
            <!-- Lọc theo Danh mục -->
            <div class="filter-menu" style="margin: 0;">
                <a href="index.php" class="filter-link <?php echo ($selectedCategoryId === null && $selectedSku === '') ? 'active' : ''; ?>">
                    Tat ca <span class="filter-count"><?php echo count($products); ?></span>
                </a>
                <?php foreach ($categories as $cat): ?>
                    <?php $count = countByCategory($products, $cat['id']); ?>
                    <a href="index.php?category_id=<?php echo $cat['id']; ?>" class="filter-link <?php echo ($selectedCategoryId === $cat['id'] && $selectedSku === '') ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                        <span class="filter-count"><?php echo $count; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Tìm kiếm SKU -->
            <form action="index.php" method="GET" class="search-form">
                <div class="search-input-wrapper">
                    <input type="text" name="sku" placeholder="Nhập SKU cần tìm..." value="<?php echo htmlspecialchars($selectedSku); ?>" class="search-input">
                    <?php if ($selectedSku !== ''): ?>
                        <a href="index.php" class="clear-search" title="Xóa lọc SKU">&times;</a>
                    <?php endif; ?>
                </div>
                <button type="submit" class="search-btn">Tìm SKU</button>
            </form>
        </div>
        
        <div class="table-container">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 12%;">SKU</th>
                            <th style="width: 18%;">Danh mục</th>
                            <th style="width: 25%;">Tên sản phẩm</th>
                            <th style="text-align: right; width: 15%;">Giá bán</th>
                            <th style="text-align: right; width: 8%;">SL</th>
                            <th style="text-align: center; width: 12%;">Muc ton</th>
                            <th style="text-align: right; width: 10%;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php renderProductRows($filteredProducts, $categoryMap); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Báo cáo theo danh mục (Nhiệm vụ B) -->
        <div class="header" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); padding: 1.5rem 2.5rem; border-top: 1px solid var(--border);">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #ffffff; margin: 0;">Báo cáo theo danh mục</h2>
        </div>
        
        <div class="table-container" style="padding-top: 1.5rem;">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Danh muc</th>
                            <th style="text-align: right;">So SP</th>
                            <th style="text-align: right;">Tong gia tri</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <?php 
                                $catProducts = filterByCategory($products, $cat['id']);
                                $catValue = inventoryValue($catProducts);
                                $catCount = countByCategory($products, $cat['id']);
                            ?>
                            <tr>
                                <td>
                                    <span class="product-name" style="font-weight: 600;">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </span>
                                </td>
                                <td style="text-align: right; font-weight: 500;">
                                    <?php echo $catCount; ?>
                                </td>
                                <td class="total-col" style="text-align: right; font-weight: 600; color: var(--primary);">
                                    <?php echo number_format($catValue, 0, ',', '.'); ?> ₫
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="stats-grid" style="grid-template-columns: 1fr 1fr 1fr;">
            <div class="stat-card">
                <span class="stat-value" id="product_count">
                    Số sản phẩm: <?php echo $productCount; ?>
                </span>
            </div>
            <div class="stat-card">
                <span class="stat-value" id="inventory_value" style="color: var(--primary);">
                    Tổng giá trị kho: <?php echo number_format($inventoryValue, 0, ',', '.'); ?> ₫
                </span>
            </div>
            <div class="stat-card">
                <span class="stat-value" id="inventory_rank" style="color: #0d9488;">
                    Quy mo kho: <?php echo htmlspecialchars($inventoryRank); ?>
                </span>
            </div>
        </div>
    </div>
                            
    <!-- Debug section (Nhiệm vụ D) -->
    <div class="debug-container">
        <div class="debug-title">Hệ thống gỡ lỗi (debug)</div>
        <pre class="debug-pre"><?php var_dump($products); ?></pre>
    </div>
</div>
</body>
</html>
<!-- MS_EXPECT inventory_value=41380000 rank=Lon -->
