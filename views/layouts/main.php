<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? APP_NAME; ?></title>
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" href="/icons/icon-192x192.png">
    <meta name="theme-color" content="#059669">
    <meta name="description" content="Livraison de gaz domestique au Cameroun - Service rapide et fiable">
    
    <!-- Styles -->
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="<?php echo $page ?? ''; ?>">
    <?php include 'views/components/header.php'; ?>
    
    <main class="main-content">
        <?php echo $content; ?>
    </main>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        <?php include 'views/components/footer.php'; ?>
    <?php endif; ?>
    
    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/public/assets/js/app.js"></script>
    <script src="/public/assets/js/map.js"></script>
    <script src="/public/assets/js/utils.js"></script>
    
    <?php if (isset($page) && $page === 'order'): ?>
        <script src="/public/assets/js/order.js"></script>
    <?php endif; ?>
    
    <?php if (isset($page) && strpos($page, 'station') !== false): ?>
        <script src="/public/assets/js/station.js"></script>
    <?php endif; ?>
    
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js');
        }
    </script>
</body>
</html>