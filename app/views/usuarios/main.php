<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="<?= BASE_URL ?>favicon.ico" type="image/x-icon">
  <title>Inicio — Usuario | The Hands Talk</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/main.css">
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
    <h2 class="section-title">Datos Destacados</h2>
    <div class="cards-grid">
      <div class="info-card">
        <div class="card-icon">👂</div>
        <h3>“Sordos” o “sordomudos/as”</h3>
        <p>“Sordomudo” es peyorativo. Usa “sordo/a” o “persona con discapacidad auditiva”.</p>
      </div>
      <div class="info-card">
        <div class="card-icon">👐</div>
        <h3>“Lengua de señas” vs “lenguaje de seña”</h3>
        <p>La “lengua de señas” es un idioma completo con gramática y sintaxis propias.</p>
      </div>
      <div class="info-card">
        <div class="card-icon">⭐</div>
        <h3>Tu seña representativa</h3>
        <p>Se asigna según tu identidad por un miembro reconocido de la comunidad sorda.</p>
      </div>
      <div class="info-card">
        <div class="card-icon">📜</div>
        <h3>Ley 3024 de 1996</h3>
        <p>Primera norma en Colombia para inclusión y derechos de personas con discapacidad.</p>
      </div>
      <div class="info-card">
        <div class="card-icon">⚖️</div>
        <h3>Ley 982 de 2005</h3>
        <p>Promueve igualdad de oportunidades y elimina barreras en educación y empleo.</p>
      </div>
      <div class="info-card">
        <div class="card-icon">🎓</div>
        <h3>Ley 2049 de 2020</h3>
        <p>Reconoce y promueve la lengua de señas colombiana en comunicación y educación.</p>
      </div>
    </div>
  </main>
  
  <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
  <script src="<?= BASE_URL ?>js/main.js" defer></script>
  <script src="<?= BASE_URL ?>js/theme.js" defer></script>
</body>
</html>