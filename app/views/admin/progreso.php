<?php
$progresos = $progresos ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Admin | Progresos Usuarios</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/users.css">
</head>

<body>

    <nav class="top-nav">
        <div class="logo">The Hands Talk</div>
        <div class="user-info">
            Hola de nuevo, <?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <a href="<?= BASE_URL ?>auth/logout" class="btn-logout">Cerrar sesión</a>
    </nav>

    <div class="layout">

        <!-- Sidebar -->
        <aside class="sidebar">
            <ul>
                <li><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
                <li><a href="<?= BASE_URL ?>admin/users">Usuarios</a></li>
                <li><a href="<?= BASE_URL ?>admin/categorias">Categorías</a></li>
                <li><a href="<?= BASE_URL ?>admin/senas">Señas</a></li>
                <li><a href="<?= BASE_URL ?>admin/tests">Tests</a></li>
                <li><a href="<?= BASE_URL ?>admin/desbloqueos">Desbloqueos</a></li>
                <li><a href="<?= BASE_URL ?>admin/progreso" class="active">Progresos</a></li>
            </ul>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <h2>Progresos de Usuarios</h2>

            <!-- Aplica la clase que define tu CSS para tablas -->
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Categoría</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($progresos)): ?>
                        <tr>
                            <td colspan="4">No hay progresos.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($progresos as $p): ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td><?= htmlspecialchars($p['usuario'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($p['categoria'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($p['created_at'], ENT_QUOTES) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>

    </div> <!-- /.layout -->

</body>

</html>