<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حول الموقع</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f4f4f4;
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

        .cart, .search-icon {
            position: relative;
            cursor: pointer;
        }

        .cart svg, .search-icon svg {
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

        /* أنماط النافذة الجانبية */
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

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        p {
            color: #555;
            margin-bottom: 0.5rem;
        }

        /* أنماط النافذة المنبثقة للبحث */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1001;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .modal-content h3 {
            margin-bottom: 20px;
            color: #333;
        }

        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .modal-content input[type="text"] {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .modal-content button {
            background: #ffafcc;
            color: #fff;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content button:hover {
            background: #e89fb9;
        }

        .close-btn {
            background: #ff4444;
            margin-top: 10px;
        }

        .close-btn:hover {
            background: #cc0000;
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
            <div class="search-icon" onclick="openSearchModal()">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
            </div>
            <div class="cart" onclick="window.location.href='welcome.php'">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-0.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-0.16.28-0.25.61-0.25.96 0 1.1 0.9 2 2 2h10v-2H7.42c-0.14 0-0.25-0.11-0.25-0.25l0.03-0.12.9-1.63h7.45c0.75 0 1.41-0.41 1.75-1.03l3.58-6.49c0.08-0.14 0.12-0.31 0.12-0.48 0-0.55-0.45-1-1-1H5.21l-0.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s0.89 2 1.99 2 2-0.9 2-2-0.9-2-2-2z"/>
                </svg>
                <span id="cartCount"><?php echo count($_SESSION['cart']); ?></span>
            </div>
        </div>
    </div>

    <!-- النافذة الجانبية -->
    <?php
    $db = new SQLite3('shopping_dbbb.db');
    $categories_result = $db->query("SELECT * FROM categories");
    $categories = [];
    while ($row = $categories_result->fetchArray(SQLITE3_ASSOC)) {
        $categories[$row['id']] = $row['name'];
    }
    ?>
    <div class="sidebar" id="sidebar">
        <button class="close-btn" onclick="toggleSidebar()">&times;</button>
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

    <!-- النافذة المنبثقة للبحث -->
    <div id="searchModal" class="modal">
        <div class="modal-content">
            <h3>ابحث عن منتج</h3>
            <form method="GET" action="index.php">
                <input type="text" name="search" placeholder="اسم المنتج">
                <button type="submit">بحث</button>
                <button type="button" class="close-btn" onclick="closeSearchModal()">إغلاق</button>
            </form>
        </div>
    </div>

    <div class="container">
        <h2>حول الموقع</h2>
        <p>مرحبًا بكم في متجرنا الإلكتروني!</p>
        <p>نحن نقدم مجموعة واسعة من المنتجات عالية الجودة بأسعار تنافسية. مهمتنا هي توفير تجربة تسوق مريحة وممتعة لجميع عملائنا.</p>
        <p>إذا كان لديك أي استفسار، لا تتردد في التواصل معنا!</p>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        function openSearchModal() {
            document.getElementById('searchModal').style.display = 'flex';
        }

        function closeSearchModal() {
            document.getElementById('searchModal').style.display = 'none';
        }
    </script>
</body>
</html>