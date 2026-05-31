<?php
// views/menu.php
$categoryFilter = $_GET['category'] ?? 'todos';
$products = getProducts($categoryFilter);
$allCategories = [
    ['id' => 'todos', 'label' => 'Todo el Menú', 'icon' => '🍽️'],
    ['id' => 'combos', 'label' => 'Combos', 'icon' => '🎁'],
    ['id' => 'pizzas', 'label' => 'Pizzas', 'icon' => '🍕'],
    ['id' => 'acompañamientos', 'label' => 'Acompañamientos', 'icon' => '🥗'],
    ['id' => 'refrescos', 'label' => 'Refrescos', 'icon' => '🥤'],
    ['id' => 'bebidas', 'label' => 'Bebidas', 'icon' => '🍹'],
];
?>
<div style="background: <?php echo $bgColor; ?>; min-height: 100vh;">
    <!-- Header del Menú -->
    <div style="background:linear-gradient(180deg,rgba(0,0,0,0.85) 0%,transparent 100%);padding:20px 20px 0;position:sticky;top:0;z-index:30;backdrop-filter:blur(10px);">
        <div style="max-width:700px;margin:0 auto;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-size:36px;"><?php echo htmlspecialchars($settings['logo']); ?></span>
                    <div>
                        <div class="pf" style="font-size:20px;font-weight:900;"><?php echo htmlspecialchars($settings['name']); ?></div>
                        <div class="lt" style="font-size:11px;color:rgba(255,255,255,0.5);"><?php echo htmlspecialchars($settings['description']); ?></div>
                    </div>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <a href="index.php?view=adminLogin" style="background:rgba(255,255,255,0.1);border:none;color:rgba(255,255,255,0.5);border-radius:8px;padding:6px 10px;font-size:16px;text-decoration:none;" title="Admin">⚙️</a>
                    <!-- Carrito se manejará con JS, pero el botón debe estar aquí -->
                    <button onclick="toggleCart()" style="background:<?php echo $primaryHex; ?>;border:none;border-radius:12px;padding:10px 16px;cursor:pointer;display:flex;align-items:center;gap:8px;position:relative;">
                        <span>🛒</span>
                        <span id="cart-count" class="badge" style="display:none;position:absolute;top:-6px;right:-6px;background:<?php echo $primaryHex; ?>;color:#fff;border-radius:50%;width:20px;height:20px;font-size:11px;display:flex;align-items:center;justify-content:center;">0</span>
                        <span id="cart-total" class="lt" style="color:#fff;font-weight:700;font-size:13px;">$0.00</span>
                    </button>
                </div>
            </div>
            <!-- Chips de Categorías -->
            <div style="display:flex;gap:8px;overflow-x:auto;padding-bottom:16px;">
                <?php foreach ($allCategories as $cat): ?>
                    <a href="index.php?view=menu&category=<?php echo $cat['id']; ?>" 
                       class="chip <?php echo $categoryFilter === $cat['id'] ? 'selected' : ''; ?>">
                       <?php echo $cat['icon'] . ' ' . $cat['label']; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Grid de Productos -->
    <div style="padding:16px 20px 120px;max-width:700px;margin:0 auto;">
        <?php foreach ($products as $product): ?>
            <div class="card" style="padding:16px;margin-bottom:12px;display:flex;align-items:center;gap:16px;opacity:<?php echo $product['available'] ? '1' : '0.5'; ?>">
                <?php 
                $imgSrc = htmlspecialchars($product['image']);
                // Si la imagen no es un emoji, asume que es un archivo en /images/
                $isEmoji = mb_strlen($imgSrc) < 5 && !preg_match('/\.[a-z]+$/i', $imgSrc);
                ?>
                <div style="font-size:40px;width:60px;text-align:center;flex-shrink:0;">
                    <?php if ($isEmoji): ?>
                        <?php echo $imgSrc; ?>
                    <?php else: ?>
                        <img src="images/<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                    <?php endif; ?>
                </div>
                <div style="flex:1;">
                    <div class="pf" style="font-size:15px;font-weight:700;"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="lt" style="font-size:11px;color:rgba(255,255,255,0.5);"><?php echo htmlspecialchars($product['description']); ?></div>
                    <div class="pf" style="color:<?php echo $primaryHex; ?>;font-weight:700;">$<?php echo number_format($product['price'], 2); ?></div>
                </div>
                <?php if ($product['available']): ?>
                    <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>', '<?php echo $imgSrc; ?>', <?php echo $product['price']; ?>)" 
                            style="background:<?php echo $primaryHex; ?>;border:none;border-radius:8px;width:36px;height:36px;font-size:22px;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        +
                    </button>
                <?php else: ?>
                    <span style="background:#ef4444;color:#fff;padding:5px 10px;border-radius:20px;font-size:11px;font-weight:700;">AGOTADO</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Cart Drawer (Se abre/cierra con JS) -->
    <div id="cart-drawer" class="overlay" style="display:none;" onclick="toggleCart()">
        <!-- Contenido del carrito y formulario de pedido manejado por JS -->
    </div>
</div>

<script>
    // Lógica del carrito en JavaScript (similar a React)
    // Debes implementar addToCart, removeFromCart, etc.
    // y al confirmar, enviar un formulario POST a index.php?view=admin_actions&action=place_order
</script>