<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جهات الاتصال</title>
    <!-- إضافة مكتبة Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

        .contact-content {
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .contact-content h2 {
            color: #8b1e3f;
            margin-bottom: 1rem;
        }

        .contact-content p {
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 2rem;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #ffafcc;
            color: #fff;
            font-size: 1.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .social-links a:hover {
            transform: translateY(-5px);
            background: #e89fb9;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
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
                <span id="cartCount"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
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

    <div class="contact-content">
        <h2>جهات الاتصال</h2>
        <p>يمكنكم التواصل معنا عبر البريد الإلكتروني أو الهاتف:</p>
        <p><strong>البريد الإلكتروني:</strong> example@domain.com</p>
        <p><strong>رقم الهاتف:</strong> 123-456-7890</p>

        <!-- روابط وسائل التواصل الاجتماعي -->
        <div class="social-links">
            <!-- فيسبوك -->
            <a href="https://www.facebook.com/chemsobelkacemi" target="_blank" title="فيسبوك">
                <i class="fab fa-facebook-f"></i>
            </a>
            <!-- تيك توك -->
            <a href="https://tiktok.com/@ayad_officiel" target="_blank" title="تيك توك">
                <i class="fab fa-tiktok"></i>
            </a>
            <!-- إنستغرام -->
            <a href="https://instagram.com/yourprofile" target="_blank" title="إنستغرام">
                <i class="fab fa-instagram"></i>
            </a>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
</body>
</html>