<?php
// views/admin_panel.php
// Asegúrate de que $adminTab esté definida desde index.php
$productsAll = getProducts();
$orders = getOrders();
$qrCodes = getQRCodes();
$settings = getSettings();

// Métricas rápidas
$totalVentas = array_reduce($orders, function($sum, $order) { return $sum + $order['total']; }, 0);
?>
<div class="admin-panel">
    <!-- HEADER -->
    <div style="background:<?php echo $primaryHex; ?>;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div class="pf" style="color:#fff;font-size:20px;">Panel Administrador</div>
            <div class="lt" style="color:rgba(255,255,255,0.7);"><?php echo htmlspecialchars($settings['name']); ?></div>
        </div>
        <div>
            <a href="index.php?view=menu" class="btn-outline" style="border-color:#fff;color:#fff;">← Menú</a>
            <a href="index.php?view=admin&action=logout" style="background:rgba(255,255,255,0.2);color:#fff;padding:8px 16px;border-radius:8px;text-decoration:none;margin-left:10px;">Salir</a>
        </div>
    </div>

    <!-- TABS -->
    <div style="display:flex;border-bottom:1px solid #eee;padding:0 24px;">
        <?php 
        $tabs = ['dashboard' => '📊 Dashboard', 'productos' => '📋 Productos', 'ordenes' => '🧾 Órdenes', 'qr' => '📱 Códigos QR', 'ajustes' => '⚙️ Ajustes'];
        foreach ($tabs as $key => $label): ?>
            <a href="index.php?view=admin&tab=<?php echo $key; ?>" 
               class="lt" style="padding:10px 16px;color:<?php echo $adminTab === $key ? $primaryHex : '#888'; ?>;border-bottom:2px solid <?php echo $adminTab === $key ? $primaryHex : 'transparent'; ?>;text-decoration:none;">
               <?php echo $label; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- CONTENIDO DINÁMICO -->
    <div style="padding:24px;max-width:960px;margin:0 auto;">
        <?php
        // Incluye la subvista correspondiente
        switch ($adminTab) {
            case 'dashboard':
                include 'admin_dashboard.php';
                break;
            case 'productos':
                include 'admin_productos.php';
                break;
            case 'ordenes':
                include 'admin_ordenes.php';
                break;
            case 'qr':
                include 'admin_qr.php';
                break;
            case 'ajustes':
                include 'admin_ajustes.php';
                break;
            default:
                include 'admin_dashboard.php';
        }
        ?>
    </div>
</div>