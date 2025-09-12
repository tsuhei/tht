<?php
// 1. Evitar warnings si no existiera nada
$categories = $categories ?? [];

// 2. Datos para layout
$active   = 'cat';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="<?= BASE_URL ?>favicon.ico" type="image/x-icon">
  <title>Categorías — Usuario | The Hands Talk</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/main.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/cat_user1.css">
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

  <main class="main-content">
    <h2 class="section-title">Explora Categorías</h2>
    
    <?php if (empty($categories)): ?>
      <div class="empty-state">
        <div class="empty-icon">📂</div>
        <h3>No hay categorías disponibles</h3>
        <p>Pronto agregaremos nuevas categorías para que explores.</p>
      </div>
    <?php else: ?>
      <div class="categories-grid">
        <?php foreach ($categories as $cat): ?>
          <?php
          $idCategoria = $cat['id'] ?? $cat['id_categoria'] ?? 0;
          $titulo   = $cat['nom_categoria'] ?? 'Sin título';
          $videoUrl = $cat['video_url']   ?? '';
          $iconPath = !empty($cat['icono']) ? BASE_URL . $cat['icono'] : BASE_URL . 'images/default-icon.png';
          $urlSenas = BASE_URL . "usuarios/senas/" . urlencode($idCategoria);
          ?>
          <a href="<?= htmlspecialchars($urlSenas, ENT_QUOTES) ?>" class="cat-card-link">
            <div class="cat-card">
              <div class="cat-header">
                <div class="icon">
                  <img
                    src="<?= htmlspecialchars($iconPath, ENT_QUOTES) ?>"
                    alt="Icono <?= htmlspecialchars($titulo, ENT_QUOTES) ?>"
                    onerror="this.src='<?= BASE_URL ?>images/default-icon.png'">
                </div>
                <h3><?= htmlspecialchars($titulo, ENT_QUOTES) ?></h3>
              </div>
              <div class="cat-media">
                <?php if ($videoUrl !== ''): ?>
                  <video muted loop preload="auto" playsinline>
                    <source src="<?= BASE_URL . htmlspecialchars($videoUrl, ENT_QUOTES) ?>" type="video/mp4">
                    Tu navegador no soporta video.
                  </video>
                <?php else: ?>
                  <div class="placeholder">
                    <span class="placeholder-icon">👐</span>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <script src="<?= BASE_URL ?>js/main.js" defer></script>
  <script src="<?= BASE_URL ?>js/theme.js" defer></script>
</body>
</html>