<?php
// Asegúrate de que estas variables estén definidas, si no, asigna valores por defecto
$userName = $userName ?? 'Usuario';
$active = $active ?? 'perfil';
$user = $user ?? []; // Asegúrate de que el objeto/array de usuario esté disponible

// Iniciar sesión si no está activa (aunque el controlador ya lo hace, es buena práctica)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Perfil de Usuario</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/main.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/perfil_user.css">
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

  <style>
    /* Temas claro y oscuro */
    html.claro, body.claro {
      background-color: #fff;
      color: #000;
    }

    html.oscuro, body.oscuro {
      background-color: #121212;
      color: #eee;
    }
    /* Ajustes para elementos específicos en tema oscuro */
    html.oscuro .perfil-main, html.oscuro .categoria, html.oscuro .modal-content {
        background-color: #1e1e1e;
        color: #eee;
    }
    html.oscuro .perfil-header h1, html.oscuro .perfil-progreso h2 {
        color: #eee;
    }
    html.oscuro .categoria-nombre, html.oscuro .porcentaje {
        color: #eee;
    }
    html.oscuro input[type="text"], html.oscuro input[type="password"], html.oscuro select, html.oscuro textarea {
        background-color: #333;
        color: #eee;
        border-color: #555;
    }
    html.oscuro input::placeholder, html.oscuro textarea::placeholder {
        color: #bbb;
    }
    html.oscuro .settings-popup {
        background: #333;
        border-color: #555;
    }
    html.oscuro .popup-btn {
        color: #eee;
    }
    html.oscuro .popup-btn.logout-btn {
        color: #ff6666;
    }
    html.oscuro .barra-progreso {
        background: #555;
    }
  </style>
</head>

<body>
  <header class="user-header">
    <div class="logo">The Hands Talk</div>
    <div class="welcome">¡Hola, <?= htmlspecialchars($userName, ENT_QUOTES) ?>!</div>
    <button class="menu-toggle">☰</button>
  </header>

  <nav class="user-menu">
    <ul>
      <li><a href="<?= BASE_URL ?>usuarios/main" data-key="main" class="<?= ($active === 'main') ? 'active' : '' ?>">Información</a></li>
      <li><a href="<?= BASE_URL ?>usuarios/categorias" data-key="cat" class="<?= ($active === 'cat') ? 'active' : '' ?>">Categorías</a></li>
      <li><a href="<?= BASE_URL ?>usuarios/test" data-key="test" class="<?= ($active === 'test') ? 'active' : '' ?>">Test</a></li>
      <li><a href="<?= BASE_URL ?>usuarios/perfil" data-key="perfil" class="<?= ($active === 'perfil') ? 'active' : '' ?>">Perfil</a></li>
    </ul>
  </nav>

  <main class="perfil-main">
    <!-- Mensajes flash -->
    <?php if (isset($_SESSION['flash'])): ?>
      <div class="flash-message flash-<?= htmlspecialchars($_SESSION['flash']['type']) ?>">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Encabezado del perfil -->
    <section class="perfil-header">
      <h1 id="userNameDisplay"><?= htmlspecialchars($userName, ENT_QUOTES) ?></h1>
      <form id="formEditarNombre" action="<?= BASE_URL ?>usuarios/perfil/editarNombre" method="POST" style="display:none;">
        <input type="text" name="nombre_usuario" id="inputNombreUsuario" value="<?= htmlspecialchars($userName, ENT_QUOTES) ?>" required>
        <button type="submit" class="btn">Guardar</button>
        <button type="button" id="cancelarEditarNombre" class="btn btn-secondary">Cancelar</button>
      </form>
      <div class="perfil-actions">
        <button class="edit-btn" id="editNameBtn">✏️ Editar</button>
        <button class="settings-btn" id="settingsBtn">⚙ Ajustes</button>
      </div>
    </section>

    <!-- Pop-up de ajustes -->
    <div class="settings-popup" id="settingsPopup" aria-hidden="true">
      <ul>
        <li>
          <label for="selectTema" style="cursor:pointer;">🌙 Cambiar tema:</label>
          <select id="selectTema" style="margin-left: 10px;">
            <option value="claro">Claro</option>
            <option value="oscuro">Oscuro</option>
          </select>
        </li>
        <li><button class="popup-btn" id="changePassBtn">🔑 Cambiar contraseña</button></li>
        <li>
          <form action="<?= BASE_URL ?>auth/logout" method="POST" style="margin:0;">
            <button type="submit" class="popup-btn logout-btn">🚪 Cerrar sesión</button>
          </form>
        </li>
      </ul>
    </div>

    <!-- Formulario cambiar contraseña (oculto inicialmente) -->
    <section id="cambiarPasswordSection" style="display:none; margin-top:20px;">
      <h3>Cambiar Contraseña</h3>
      <form id="formCambiarPassword" action="<?= BASE_URL ?>usuarios/perfil/cambiarPassword" method="POST">
        <label for="password_actual">Contraseña actual:</label><br>
        <input type="password" name="password_actual" id="password_actual" required><br><br>

        <label for="password_nueva">Nueva contraseña:</label><br>
        <input type="password" name="password_nueva" id="password_nueva" required><br><br>

        <label for="password_confirmar">Confirmar nueva contraseña:</label><br>
        <input type="password" name="password_confirmar" id="password_confirmar" required><br><br>

        <button type="submit" class="btn">Cambiar contraseña</button>
        <button type="button" id="cancelarCambiarPassword" class="btn btn-secondary">Cancelar</button>
      </form>
    </section>

    <!-- Progreso -->
    <section class="perfil-progreso" style="margin-top: 30px;">
      <h2>Progreso de categorías</h2>
      <div class="categorias-container">
        <!-- Aquí se cargarán dinámicamente las categorías con su progreso -->
        <?php if (!empty($categoriasProgreso)): ?>
            <?php foreach ($categoriasProgreso as $cat): ?>
                <div class="categoria">
                    <span class="categoria-icon">
                        <?php if (!empty($cat['icono'])): ?>
                            <img src="<?= BASE_URL . htmlspecialchars($cat['icono'], ENT_QUOTES) ?>" alt="Icono <?= htmlspecialchars($cat['nom_categoria'], ENT_QUOTES) ?>" style="height: 30px;">
                        <?php else: ?>
                            📚 <!-- Icono por defecto si no hay imagen -->
                        <?php endif; ?>
                    </span>
                    <span class="categoria-nombre"><?= htmlspecialchars($cat['nom_categoria'], ENT_QUOTES) ?></span>
                    <div class="barra-progreso">
                        <div class="barra" style="width: <?= (int)$cat['progreso'] ?>%;"></div>
                    </div>
                    <span class="porcentaje"><?= (int)$cat['progreso'] ?>%</span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay progreso de categorías disponible.</p>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <script src="<?= BASE_URL ?>js/main.js" defer></script>
  <script src="<?= BASE_URL ?>js/perfil.js" defer></script>
  <script src="<?= BASE_URL ?>js/theme.js" defer></script>
</body>

</html>
