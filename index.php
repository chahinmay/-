<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$db = new SQLite3('shopping_dbbb.db');

// جلب الفئات لعرضها في النافذة الجانبية
$categories_result = $db->query("SELECT * FROM categories");
$categories = [];
while ($row = $categories_result->fetchArray(SQLITE3_ASSOC)) {
    $categories[$row['id']] = $row['name'];
}

// بناء استعلام البحث بناءً على الفئة فقط
$search_query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
    $search_query .= " AND p.category_id = :category_id";
    $params[':category_id'] = intval($_GET['category_id']);
}

$stmt = $db->prepare($search_query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT);
}
$result = $stmt->execute();

$products = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المتجر</title>
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
            position: relative;
            overflow-x: hidden;
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

        .sidebar-content .categories {
            margin-top: 1rem;
        }

        .sidebar-content .categories h4 {
            margin-bottom: 0.5rem;
            color: #ffafcc;
        }

        .sidebar-content .categories a {
            display: block;
            font-size: 1rem;
            padding: 0.3rem 0.5rem;
        }

        .products {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1rem;
            width: 100%;
            min-height: 100vh;
        }

        .product-card {
            background: #fff;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            color: #8b1e3f;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
            opacity: 0;
            transform: translateY(60px);
            transition: opacity 0.6s ease, transform 0.6s ease;
            width: 100%;
            min-height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }

        .product-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .product-card img {
            width: 100%;
            height: 50vh;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }

        @media (min-width: 1024px) {
            .product-card img {
                height: 55vh;
            }
            .product-card {
                transform: translateY(60px);
                min-height: 75vh;
            }
        }

        @media (max-width: 768px) {
            .product-card img {
                height: 45vh;
            }
            .product-card {
                transform: translateY(40px);
                min-height: 65vh;
            }
        }

        @media (max-width: 480px) {
            .product-card img {
                height: 40vh;
                width: 100%;
            }
            .product-card {
                transform: translateY(30px);
                min-height: 60vh;
                padding: 0.5rem;
            }
        }

        .product-card .availability {
            font-size: 0.75rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .product-card h3 {
            margin: 0.5rem 0;
            font-size: 1rem;
            color: #333;
        }

        .product-card p {
            margin-bottom: 0.5rem;
            font-size: 1.125rem;
            color: #28a745;
        }

        .product-card button {
            background: #ffafcc;
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .product-card button:hover {
            background: #e89fb9;
        }

        .product-sidebar {
            position: fixed;
            top: 0;
            right: -300px;
            width: 300px;
            height: 100%;
            background: #fff;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
            z-index: 1002;
            transition: right 0.3s ease;
            padding: 20px;
            overflow-y: auto;
        }

        .product-sidebar.active {
            right: 0;
        }

        .product-sidebar .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            background: none;
            border: none;
            color: #ff4444;
        }

        .product-sidebar img {
            width: 100%;
            height: 30vh;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .product-sidebar h3 {
            color: #8b1e3f;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .product-sidebar p {
            color: #8b1e3f;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .product-sidebar .price {
            color: #28a745;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .product-sidebar .options {
            margin-bottom: 1rem;
        }

        .product-sidebar .options label {
            display: block;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: #8b1e3f;
        }

        .product-sidebar .options select {
            width: 100%;
            padding: 0.5rem;
            font-size: 1rem;
            border-radius: 5px;
            border: 2px solid #b3d4fc;
            margin-bottom: 1rem;
        }

        .product-sidebar button {
            background: #ffafcc;
            color: #fff;
            border: none;
            padding: 0.75rem;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
        }

        .product-sidebar button:hover {
            background: #e89fb9;
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

    <!-- النافذة الجانبية للقائمة -->
    <div class="sidebar" id="sidebar">
        <button class="close-btn" onclick="toggleSidebar()">×</button>
        <div class="sidebar-content">
            <a href="index.php">المتجر</a>
            <a href="contact.php">جهات الاتصال</a>
            <a href="about.php">حول الموقع</a>
            <div class="categories">
                <h4>الفئات</h4>
                <?php foreach ($categories as $id => $name): ?>
                    <a href="index.php?category_id=<?php echo $id; ?>"><?php echo $name; ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="products">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="product-card">
                    <div class="availability">متوفر</div>
                    <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <h3><?php echo $product['name']; ?></h3>
                    <p><?php echo $product['price']; ?> دج</p>
                    <button onclick="showProductSidebar(<?php echo $index; ?>)">التفاصيل</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #555;">لا توجد منتجات مطابقة لمعايير البحث.</p>
        <?php endif; ?>
    </div>

    <!-- الشريط الجانبي لتفاصيل المنتج -->
    <div id="product-sidebar" class="product-sidebar">
        <button class="close-btn" onclick="closeProductSidebar()">×</button>
        <div id="product-sidebar-content"></div>
    </div>

    <script>
        let currentProductIndex = null;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        function showProductSidebar(index) {
            currentProductIndex = index;
            const product = <?php echo json_encode($products); ?>[index];
            const sidebarContent = document.getElementById('product-sidebar-content');

            // إضافة خيارات اللون والمقاس فقط لفئة الملابس
            let optionsHtml = '';
            if (product.category_name === "الملابس") {
                optionsHtml = `
                    <div class="options">
                        <label for="color_${index}">اختر اللون:</label>
                        <select id="color_${index}" name="color">
                            <option value="أحمر">أحمر</option>
                            <option value="أسود">أسود</option>
                            <option value="أزرق">أزرق</option>
                        </select>
                        <label for="size_${index}">اختر المقاس:</label>
                        <select id="size_${index}" name="size">
                            <option value="Standard">Standard</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                        </select>
                    </div>
                `;
            }

            sidebarContent.innerHTML = `
                <img src="images/${product.image}" alt="${product.name}">
                <h3>${product.name}</h3>
                <p>فئة: ${product.category_name}</p>
                <div class="price">${product.price} دج</div>
                ${optionsHtml}
                <button onclick="addToCart('${product.name}', ${product.price}, '${product.image}', this, ${index})">أضف إلى السلة</button>
            `;
            document.getElementById('product-sidebar').classList.add('active');
        }

        function closeProductSidebar() {
            document.getElementById('product-sidebar').classList.remove('active');
        }

        function addToCart(name, price, image, button, index) {
            const product = <?php echo json_encode($products); ?>[index];
            let color = '';
            let size = '';
            
            // جلب اللون والمقاس إذا كانت الفئة "الملابس"
            if (product.category_name === "الملابس") {
                color = document.getElementById(`color_${index}`).value;
                size = document.getElementById(`size_${index}`).value;
            }

            const data = new FormData();
            data.append('action', 'addToCart');
            data.append('name', name);
            data.append('price', price);
            data.append('image', image);
            data.append('quantity', 1);
            if (color) data.append('color', color);
            if (size) data.append('size', size);

            fetch('process.php', {
                method: 'POST',
                body: data
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('cartCount').innerText = data.cartCount;
                    alert(`${name}${color ? ' - اللون: ' + color : ''}${size ? ' - المقاس: ' + size : ''} تمت إضافته إلى السلة!`);
                    closeProductSidebar();
                } else {
                    alert('حدث خطأ أثناء الإضافة: ' + (data.message || 'غير معروف'));
                }
            })
            .catch(error => {
                console.error('خطأ في الاتصال:', error);
                alert('حدث خطأ في الاتصال بالخادم: ' + error.message);
            });
        }

        function handleScrollAnimation() {
            const cards = document.querySelectorAll('.product-card');
            const windowHeight = window.innerHeight;

            cards.forEach(card => {
                const cardTop = card.getBoundingClientRect().top;

                if (cardTop < windowHeight - 100) {
                    card.classList.add('visible');
                }
            });
        }

        window.addEventListener('scroll', handleScrollAnimation);
        window.addEventListener('load', handleScrollAnimation);

        // إغلاق الشريط الجانبي عند النقر خارجها
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('product-sidebar');
            if (!sidebar.contains(event.target) && sidebar.classList.contains('active') && !event.target.closest('.product-card')) {
                closeProductSidebar();
            }
        });
    </script>
</body>
</html>