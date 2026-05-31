<?php
// index.php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Determinar la vista actual (landing, menu, adminLogin, admin, admin_actions)
$view = $_GET['view'] ?? 'landing';
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

// --- Lógica de Administración por POST ---
if ($view === 'admin_actions') {
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'login':
            $pin = $_POST['pin'] ?? '';
            if (verifyAdminPin($pin)) {
                $_SESSION['admin_logged_in'] = true;
                header('Location: index.php?view=admin');
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'PIN incorrecto.'];
                header('Location: index.php?view=adminLogin');
            }
            exit;

        case 'add_product':
            if (!isset($_SESSION['admin_logged_in'])) break;
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("INSERT INTO products (category, name, description, price, image, available) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['category'],
                $_POST['name'],
                $_POST['description'],
                (float)$_POST['price'],
                $_POST['image'] ?? '🍕',
                isset($_POST['available']) ? 1 : 0
            ]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto agregado.'];
            header('Location: index.php?view=admin&tab=productos');
            exit;
        
        case 'update_product':
            if (!isset($_SESSION['admin_logged_in'])) break;
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE products SET category=?, name=?, description=?, price=?, image=?, available=? WHERE id=?");
            $stmt->execute([
                $_POST['category'],
                $_POST['name'],
                $_POST['description'],
                (float)$_POST['price'],
                $_POST['image'] ?? '🍕',
                isset($_POST['available']) ? 1 : 0,
                (int)$_POST['id']
            ]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto actualizado.'];
            header('Location: index.php?view=admin&tab=productos');
            exit;
        
        // ... más casos para update_settings, change_pin, etc.

        case 'place_order':
            $cart = json_decode($_POST['cart_items'], true);
            $orderId = createOrder($cart, $_POST['mesa'], $_POST['tipo'], $_POST['metodoPago']);
            if ($orderId) {
                $_SESSION['message'] = ['type' => 'success', 'text' => '¡Orden #' . $orderId . ' creada con éxito!'];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al crear la orden.'];
            }
            header('Location: index.php?view=menu');
            exit;
    }
}

// --- INCLUIR HEADER COMÚN ---
$settings = getSettings();
if (!$settings) {
    $settings = [
        'name' => 'QuickQR',
        'description' => 'Menú Digital',
        'logo' => '🍕',
        'primary_color' => '#C0392B',
        'bg_color' => '#1a0a00'
    ];
}
$primaryHex = $settings['primary_color'];
$bgColor = $settings['bg_color'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['name']); ?> - QuickQR Menú</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos globales - La mayor parte se hereda del código original */
        *{box-sizing:border-box;margin:0;padding:0;}
        body {
            font-family: 'Georgia', serif;
            background: <?php echo $bgColor; ?>;
            color: #fff;
        }
        .pf { font-family: 'Playfair Display', serif; }
        .lt { font-family: 'Lato', sans-serif; }
        .btn-primary {
            background: <?php echo $primaryHex; ?>;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            cursor: pointer;
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: 14px;
            transition: filter 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary:hover { filter: brightness(1.15); }
        .btn-outline {
            background: transparent;
            color: <?php echo $primaryHex; ?>;
            border: 1.5px solid <?php echo $primaryHex; ?>;
            border-radius: 8px;
            padding: 10px 20px;
            cursor: pointer;
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: 13px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-outline:hover { background: <?php echo $primaryHex; ?>; color: #fff; }
        .card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
        }
        .input-field {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            padding: 10px 14px;
            color: #fff;
            font-family: 'Lato', sans-serif;
            font-size: 14px;
            width: 100%;
            outline: none;
        }
        .input-field:focus { border-color: <?php echo $primaryHex; ?>; }
        .input-field::placeholder { color: rgba(255,255,255,0.4); }
        .chip {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 8px 16px;
            cursor: pointer;
            font-family: 'Lato', sans-serif;
            font-size: 13px;
            transition: all 0.2s;
            white-space: nowrap;
            color: #fff;
        }
        .chip.selected {
            background: <?php echo $primaryHex; ?>;
            border-color: <?php echo $primaryHex; ?>;
        }
        /* Estilos del panel admin */
        .admin-panel {
            background: #fff;
            min-height: 100vh;
            color: #1a0a00;
        }
        .admin-input {
            background: #f8f5f0;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px 14px;
            color: #333;
            font-family: 'Lato', sans-serif;
            font-size: 14px;
            width: 100%;
            outline: none;
        }
        .admin-input:focus { border-color: <?php echo $primaryHex; ?>; }
    </style>
</head>
<body>
    <?php if ($message): ?>
        <div style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:999;padding:12px 24px;border-radius:24px;font-family:'Lato',sans-serif;font-size:14px;font-weight:700;color:#fff;background:<?php echo $message['type'] === 'success' ? '#22c55e' : '#ef4444'; ?>;">
            <?php echo htmlspecialchars($message['text']); ?>
        </div>
    <?php endif; ?>

    <?php
    // --- ENRUTADOR DE VISTAS ---
    switch ($view) {
        case 'landing':
            include 'views/landing.php';
            break;
        case 'menu':
            include 'views/menu.php';
            break;
        case 'adminLogin':
            include 'views/admin_login.php';
            break;
        case 'admin':
            if (!isset($_SESSION['admin_logged_in'])) {
                header('Location: index.php?view=adminLogin');
                exit;
            }
            $adminTab = $_GET['tab'] ?? 'dashboard';
            include 'views/admin_panel.php';
            break;
        default:
            include 'views/landing.php';
    }
    ?>
</body>
</html>