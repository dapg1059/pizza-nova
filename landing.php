<!-- views/landing.php -->
<div style="min-height: 100vh; background: <?php echo $bgColor; ?>; color: #fff; font-family: 'Lato', sans-serif; overflow-x: hidden;">
    <nav style="display: flex; align-items: center; justify-content: space-between; padding: 20px 40px; border-bottom: 1px solid rgba(255,255,255,0.06);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 36px;"><?php echo htmlspecialchars($settings['logo']); ?></span>
            <span style="font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 900;">QuickQR</span>
        </div>
        <a href="index.php?view=menu" class="btn-primary">Ver Menú Demo</a>
    </nav>

    <div style="max-width: 900px; margin: 0 auto; padding: 80px 40px 60px; text-align: center;">
        <!-- Contenido del Hero -->
        <div style="display: inline-block; background: rgba(192,57,43,0.15); border: 1px solid rgba(192,57,43,0.3); border-radius: 20px; padding: 6px 16px; font-size: 12px; font-weight: 700; color: #e67e56; margin-bottom: 24px; letter-spacing: 1px;">PLATAFORMA DE MENÚS DIGITALES</div>
        <h1 style="font-family: 'Playfair Display', serif; font-size: clamp(36px, 6vw, 64px); font-weight: 900; line-height: 1.1; margin-bottom: 24px;">
            Transforma tu menú en<br><span style="color: <?php echo $primaryHex; ?>;">una experiencia digital</span>
        </h1>
        <p style="font-size: 18px; color: rgba(255,255,255,0.55); max-width: 560px; margin: 0 auto 40px; line-height: 1.7;">
            Tus clientes escanean un QR, ordenan desde su mesa y el pedido llega directo a cocina. Sin papel, sin esperas, sin errores.
        </p>
        <a href="index.php?view=menu" class="btn-primary" style="padding: 16px 36px; font-size: 16px;">Probar Demo →</a>
    </div>
    <!-- Más contenido de Features... -->
</div>