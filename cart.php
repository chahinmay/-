<?php
session_start();

// التحقق مما إذا كانت السلة غير موجودة
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// معالجة حذف المنتج من السلة (اختياري)
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // إعادة ترقيم المصفوفة
    }
}

// حساب إجمالي السعر
$total = 0;
foreach ($_SESSION['cart'] as $index => $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>السلة</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #ffffff;
            color: #333;
            padding-top: 6rem;
        }

        .top-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #ffafcc;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        .menu-icon svg {
            width: 30px;
            height: 30px;
            fill: #fff;
        }

        .nav-icons {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .cart {
            position: relative;
            cursor: pointer;
        }

        .cart svg {
            width: 30px;
            height: 30px;
            fill: #fff;
        }

        .cart span {
            background: #ff5722;
            color: #fff;
            border-radius: 50%;
            padding: 0.3rem 0.6rem;
            font-size: 0.9rem;
            position: absolute;
            top: -0.8rem;
            left: -0.8rem;
        }

        .sidebar {
            position: fixed;
            top: 0;
            right: -250px;
            width: 250px;
            height: 100%;
            background: #fff;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            transition: right 0.3s ease;
        }

        .sidebar.active {
            right: 0;
        }

        .sidebar .close-btn {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .sidebar-content {
            padding: 3rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .sidebar-content a {
            color: #333;
            text-decoration: none;
            font-size: 1.1rem;
            padding: 0.5rem;
            border-radius: 5px;
        }

        .sidebar-content a:hover {
            background: #ffafcc;
            color: #fff;
        }

        .cart-content {
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .cart-content h2 {
            color: #8b1e3f;
            margin-bottom: 1rem;
        }

        .cart-item {
            background: #fff;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            margin-left: 1rem;
        }

        .cart-item .details {
            text-align: right;
            flex-grow: 1;
        }

        .cart-item .details p {
            margin: 0.25rem 0;
        }

        .cart-item .remove {
            background: #ff4444;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
        }

        .cart-item .remove:hover {
            background: #cc0000;
        }

        .total {
            margin-top: 1rem;
            font-size: 1.25rem;
            color: #28a745;
        }

        .checkout-btn {
            background: #ffafcc;
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 1rem;
        }

        .checkout-btn:hover {
            background: #e89fb9;
        }

        .empty-cart {
            color: #555;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="top-nav">
        <div class="menu-icon" onclick="toggleSidebar()">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 6h18v2H3V6zm0 5h18v2H3v-2zm0 5h18v2H3v-2z"/>
            </svg>
        </div>
        <div class="nav-icons">
            <div class="cart" onclick="window.location.href='cart.php'">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-0.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-0.16.28-0.25.61-0.25.96 0 1.1 0.9 2 2 2h10v-2H7.42c-0.14 0-0.25-0.11-0.25-0.25l0.03-0.12.9-1.63h7.45c0.75 0 1.41-0.41 1.75-1.03l3.58-6.49c0.08-0.14 0.12-0.31 0.12-0.48 0-0.55-0.45-1-1-1H5.21l-0.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s0.89 2 1.99 2 2-0.9 2-2-0.9-2-2-2z"/>
                </svg>
                <span id="cartCount"><?php echo count($_SESSION['cart']); ?></span>
            </div>
        </div>
    </div>

    <!-- النافذة الجانبية -->
    <div class="sidebar" id="sidebar">
        <button class="close-btn" onclick="toggleSidebar()">×</button>
        <div class="sidebar-content">
            <a href="index.php">المتجر</a>
            <a href="contact.php">جهات الاتصال</a>
            <a href="about.php">حول الموقع</a>
        </div>
    </div>

    <div class="cart-content">
        <h2>محتوى السلة</h2>
        <?php if (count($_SESSION['cart']) > 0): ?>
            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <div class="cart-item">
                    <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="details">
                        <p><strong>الاسم:</strong> <?php echo htmlspecialchars($item['name']); ?></p>
                        <p><strong>اللون:</strong> <?php echo isset($item['color']) ? htmlspecialchars($item['color']) : 'غير محدد'; ?></p>
                        <p><strong>المقاس:</strong> <?php echo isset($item['size']) ? htmlspecialchars($item['size']) : 'غير محدد'; ?></p>
                        <p><strong>السعر:</strong> <?php echo number_format($item['price'], 2); ?> دج</p>
                        <p><strong>الكمية:</strong> <?php echo $item['quantity']; ?></p>
                    </div>
                    <button class="remove" onclick="if(confirm('هل تريد حذف هذا المنتج؟')) window.location.href='cart.php?remove=<?php echo $index; ?>'">حذف</button>
                </div>
            <?php endforeach; ?>
            <div class="total">
                <strong>الإجمالي:</strong> <?php echo number_format($total, 2); ?> دج
            </div>
            <button class="checkout-btn" onclick="alert('وظيفة الدفع قيد التطوير!')">إتمام الدفع</button>
        <?php else: ?>
            <p class="empty-cart">السلة فارغة!</p>
        <?php endif; ?>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
</body>
</html>