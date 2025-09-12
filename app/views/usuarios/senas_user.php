<?php
// Las variables $userName, $active, $categoria, $senas vienen del controlador SenasUserController
$userName  = $userName  ?? 'Usuario';
$active    = $active    ?? 'cat'; // Por defecto 'cat' para mantener el menú de categorías activo
$categoria = $categoria ?? ['nom_categoria' => 'Categoría Desconocida'];
$senas     = $senas     ?? [];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= BASE_URL ?>favicon.ico" type="image/x-icon">
    <title>Señas de <?= htmlspecialchars($categoria['nom_categoria'], ENT_QUOTES) ?> | The Hands Talk</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/senas_user2.css">
    <script>
        (function() {
            try {
                var tema = localStorage.getItem('tema_usuario') || 'claro';
                document.documentElement.classList.remove('claro', 'oscuro');
                document.documentElement.classList.add(tema);
                document.addEventListener('DOMContentLoaded', function() {
                    document.body.classList.remove('claro', 'oscuro');
                    document.body.classList.add(tema);
                });
            } catch (e) {}
        })();
    </script>
    <script>
    const BASE_URL = '<?= BASE_URL ?>';
    </script>
</head>

<body data-active="<?= $active ?>" class="senas-page">

    <header class="user-header">
        <div class="logo">The Hands Talk</div>
        <div class="welcome-container">
            <div class="welcome-message">¡Hola, <?= htmlspecialchars($userName, ENT_QUOTES) ?>! 👋</div>
        </div>
        <button class="menu-toggle" aria-label="Abrir menú">☰</button>
    </header>

    <main class="main-content senas-main-content">
        <div class="header-section">
            <h2 class="section-title">Señas de la categoría: <?= htmlspecialchars($categoria['nom_categoria'], ENT_QUOTES) ?></h2>
            <a href="<?= BASE_URL ?>usuarios/categorias" class="back-btn">← Volver a Categorías</a>
        </div>

        <div class="senas-container">
            <nav class="senas-menu" id="senasMenu">
                <div class="menu-header">
                    <h3>Señas disponibles</h3>
                    <button class="menu-toggle-btn" id="menuToggle">☰</button>
                </div>
                <div class="senas-list-container">
                    <div class="senas-list">
                        <?php foreach ($senas as $index => $sena): ?>
                            <button type="button" class="sena-btn <?= $index === 0 ? 'active' : '' ?>" data-id="<?= $sena['id'] ?>">
                                <span class="sena-icon">👐</span>
                                <?= htmlspecialchars($sena['palabra'], ENT_QUOTES) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </nav>

            <section class="sena-details">
                <?php foreach ($senas as $index => $sena): ?>
                    <article class="sena-info" id="sena-<?= $sena['id'] ?>" style="display: <?= $index === 0 ? 'flex' : 'none' ?>">
                        <div class="sena-text">
                            <h3 class="sena-word"><?= htmlspecialchars($sena['palabra'], ENT_QUOTES) ?></h3>
                            <div class="sena-description">
                                <h4>Descripción</h4>
                                <p><?= htmlspecialchars($sena['descripcion'], ENT_QUOTES) ?></p>
                            </div>
                        </div>
                        <div class="sena-media-container">
    <video autoplay loop muted playsinline class="sena-video">
        <source src="<?= BASE_URL . htmlspecialchars($sena['media_url'], ENT_QUOTES) ?>" type="video/mp4">
        Tu navegador no soporta el elemento de video.
    </video>
</div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        </div>
    </main>
    <input type="hidden" id="inputIdCategoria" value="<?= htmlspecialchars($categoria['id'], ENT_QUOTES) ?>">

    <script src="<?= BASE_URL ?>js/user_menu.js"></script>
    <script src="<?= BASE_URL ?>js/main.js" defer></script>
    <script src="<?= BASE_URL ?>js/theme.js" defer></script>
    <script src="<?= BASE_URL ?>js/senas_user.js?v=1.2"></script>

</body>

</html>