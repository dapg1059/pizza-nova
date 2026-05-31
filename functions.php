<?php
// includes/functions.php
// Funciones reutilizables para las operaciones de la base de datos.

require_once __DIR__ . '/../config/database.php';

/**
 * Obtiene todos los productos o los filtra por categoría.
 * @param string|null $category
 * @return array
 */
function getProducts($category = null) {
    $pdo = getDBConnection();
    if ($category && $category !== 'todos') {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY name");
        $stmt->execute([$category]);
    } else {
        $stmt = $pdo->query("SELECT * FROM products ORDER BY category, name");
    }
    return $stmt->fetchAll();
}

/**
 * Obtiene la configuración del restaurante.
 * @return array
 */
function getSettings() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
    return $stmt->fetch();
}

/**
 * Crea una nueva orden y sus ítems.
 * @param array $cartItems
 * @param string $mesa
 * @param string $tipo
 * @param string $metodoPago
 * @return int|bool El ID de la nueva orden o false en caso de error.
 */
function createOrder($cartItems, $mesa, $tipo, $metodoPago) {
    $pdo = getDBConnection();
    $total = array_reduce($cartItems, function ($carry, $item) {
        return $carry + ($item['price'] * $item['qty']);
    }, 0);

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO orders (table_number, order_type, payment_method, total) VALUES (?, ?, ?, ?)");
        $stmt->execute([$mesa, $tipo, $metodoPago, $total]);
        $orderId = $pdo->lastInsertId();

        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_name, product_image, product_price, quantity) VALUES (?, ?, ?, ?, ?)");
        foreach ($cartItems as $item) {
            $stmtItem->execute([$orderId, $item['name'], $item['image'], $item['price'], $item['qty']]);
        }

        $pdo->commit();
        return $orderId;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error creando orden: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene todas las órdenes con sus ítems.
 * @return array
 */
function getOrders() {
    $pdo = getDBConnection();
    $orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();

    foreach ($orders as &$order) {
        $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$order['id']]);
        $order['items'] = $stmt->fetchAll();
    }
    return $orders;
}

/**
 * Obtiene todos los códigos QR.
 * @return array
 */
function getQRCodes() {
    $pdo = getDBConnection();
    return $pdo->query("SELECT * FROM qr_codes ORDER BY created_at DESC")->fetchAll();
}

/**
 * Verifica el PIN de administrador.
 * @param string $inputPin
 * @return bool
 */
function verifyAdminPin($inputPin) {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT pin FROM admins WHERE username = 'admin' LIMIT 1");
    $admin = $stmt->fetch();
    return $admin && $admin['pin'] === $inputPin;
}
// ... más funciones como updateProduct, deleteProduct, etc., que se implementan en admin_functions.php
?>