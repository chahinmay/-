<?php
session_start();

// ?????? ??? ??? ???? ????? ??? ??????
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ?????? ??? ????? ?????? ??? ?????
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addToCart') {
    $response = ['success' => false, 'message' => '', 'cartCount' => count($_SESSION['cart'])];

    // ??????? ???????? ?? ?????
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $image = $_POST['image'] ?? '';
    $quantity = $_POST['quantity'] ?? 1;
    $color = $_POST['color'] ?? null; // ?????? ???????? ????
    $size = $_POST['size'] ?? null;   // ?????? ???????? ??????

    // ?????? ?? ???????? ????????
    if (empty($name) || $price <= 0) {
        $response['message'] = '???????? ??? ????? ?? ??? ?????';
        echo json_encode($response);
        exit;
    }

    // ????? ?????? ?????? ?? ????? ????????
    $item = [
        'name' => $name,
        'price' => $price,
        'image' => $image,
        'quantity' => $quantity,
        'color' => $color,  // ????? ?????
        'size' => $size,    // ????? ??????
    ];

    // ????? ?????? ??? ?????
    $_SESSION['cart'][] = $item;
    $response['success'] = true;
    $response['cartCount'] = count($_SESSION['cart']);
    $response['message'] = "$name ??? ?????? ??? ????? ?????!";

    // ????? ????????? ?? JSON
    echo json_encode($response);
    exit;
}

// ?????? ??? ??? ????? ????? (??? ??? ???????)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'viewCart') {
    echo json_encode(['cart' => $_SESSION['cart'], 'cartCount' => count($_SESSION['cart'])]);
    exit;
}

// ??? ?? ??? ???? ??? ????
http_response_code(400);
echo json_encode(['success' => false, 'message' => '??? ??? ????']);
exit;