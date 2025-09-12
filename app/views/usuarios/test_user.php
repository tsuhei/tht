<?php
$categories = $categories ?? [];
$active = 'test';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= BASE_URL ?>favicon.ico" type="image/x-icon">
    <title>Tests — Usuario | The Hands Talk</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/test_user.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/flash_messages.css">
    <script>
  (function(){
    try {
      var tema = localStorage.getItem('tema_usuario') || 'claro';
      document.documentElement.classList.remove('claro','oscuro');
      document.documentElement.classList.add(tema);
      document.addEventListener('DOMContentLoaded', function(){
        document.body.classList.remove('claro','oscuro');
        document.body.classList.add(tema);
      });
    } catch(e) {}
  })();
</script>

</head>

<body data-active="<?= $active ?>">

    <header class="user-header">
        <div class="logo">The Hands Talk</div>
        <div class="welcome-container">
            <div class="welcome-message">¡Hola, <?= htmlspecialchars($userName, ENT_QUOTES) ?>! 👋</div>
        </div>
        <button class="menu-toggle" aria-label="Abrir menú">☰</button>
    </header>

    <nav class="user-menu">
        <ul>
            <li><a href="<?= BASE_URL ?>usuarios/main" class="<?= ($active === 'main') ? 'active' : '' ?>">Información</a></li>
            <li><a href="<?= BASE_URL ?>usuarios/categorias" class="<?= ($active === 'cat') ? 'active' : '' ?>">Categorías</a></li>
            <li><a href="<?= BASE_URL ?>usuarios/test" class="<?= ($active === 'test') ? 'active' : '' ?>">Test</a></li>
            <li><a href="<?= BASE_URL ?>usuarios/perfil" class="<?= ($active === 'perfil') ? 'active' : '' ?>">Perfil</a></li>
        </ul>
    </nav>

    <main class="main-content test-main-content">
        <h2 class="section-title">Tests Disponibles</h2>
        <p class="section-subtitle">Evalúa tus conocimientos en lengua de señas</p>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="flash-message flash-<?= $_SESSION['flash']['type'] ?>">
                <?= htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <h3>No hay tests disponibles</h3>
                <p>Completa más categorías para desbloquear tests.</p>
            </div>
        <?php else: ?>
            <div class="tests-grid">
                <?php foreach ($categories as $cat): ?>
                    <?php
                    $idCategoria = $cat['id'] ?? $cat['id_categoria'] ?? 0;
                    $nombre = $cat['nom_categoria'] ?? 'Sin nombre';
                    $icono = !empty($cat['icono']) ? BASE_URL . $cat['icono'] : BASE_URL . 'images/default-icon.png';
                    $urlTest = BASE_URL . "usuarios/test/iniciar/" . urlencode($idCategoria);
                    ?>
                    <div class="test-card">
                        <div class="test-icon-container">
                            <div class="test-icon">
                                <img src="<?= htmlspecialchars($icono, ENT_QUOTES) ?>" 
                                     alt="Icono <?= htmlspecialchars($nombre, ENT_QUOTES) ?>"
                                     onerror="this.src='<?= BASE_URL ?>images/default-icon.png'">
                            </div>
                        </div>
                        <div class="test-content">
                            <h3 class="test-title"><?= htmlspecialchars($nombre, ENT_QUOTES) ?></h3>
                            <p class="test-description">Pon a prueba tus conocimientos</p>
                            <a href="<?= htmlspecialchars($urlTest, ENT_QUOTES) ?>" class="btn-test">
                                <span class="btn-icon">✏️</span>
                                Iniciar Test
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="<?= BASE_URL ?>js/main.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const flashMessage = document.querySelector('.flash-message');
            if (flashMessage) {
                setTimeout(() => {
                    flashMessage.style.opacity = '0';
                    flashMessage.style.transition = 'opacity 0.5s ease-out';
                    setTimeout(() => flashMessage.remove(), 500);
                }, 5000);
            }
        });
    </script>
    <script src="<?= BASE_URL ?>js/theme.js" defer></script>
</body>
</html>