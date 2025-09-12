<?php /** @var string $adminName */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin | Dashboard</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard.css">
</head>
<body>

  <nav class="top-nav">
    <div class="logo">The Hands Talk</div>
    <div class="user-info">
      Hola de nuevo, <?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <a href="<?= BASE_URL ?>/auth/logout" class="btn-logout">
      Cerrar sesión
    </a>
  </nav>

  <div class="layout">
    <aside class="sidebar">
      <ul>
        <li><a href="<?= BASE_URL ?>admin/dashboard" class="active">Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>admin/users">Usuarios</a></li>
        <li><a href="<?= BASE_URL ?>admin/categorias">Categorías</a></li>
        <li><a href="<?= BASE_URL ?>admin/senas">Señas</a></li>
        <li><a href="<?= BASE_URL ?>admin/tests">Tests</a></li>
        <li><a href="<?= BASE_URL ?>admin/desbloqueos">Desbloqueos</a></li>
        <li><a href="<?= BASE_URL ?>admin/progreso">Progresos</a></li>
      </ul>
    </aside>
  </div>

</body>
</html>
