<?php
$desbloqueos = $desbloqueos ?? [];
$adminName   = $adminName   ?? 'Invitado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/users.css">
    <title>Admin | Desbloqueos</title>
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
        <aside class="sidebar">
            <ul>
                <li><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
                <li><a href="<?= BASE_URL ?>admin/users">Usuarios</a></li>
                <li><a href="<?= BASE_URL ?>admin/categorias">Categorías</a></li>
                <li><a href="<?= BASE_URL ?>admin/senas">Señas</a></li>
                <li><a href="<?= BASE_URL ?>admin/tests">Tests</a></li>
                <li><a href="<?= BASE_URL ?>admin/desbloqueos" class="active">Desbloqueos</a></li>
                <li><a href="<?= BASE_URL ?>admin/progreso">Progreso</a></li>
            </ul>
        </aside>

        <section class="main-content">
            <h2>Panel de Desbloqueos</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Test Aprobado</th>
                        <th>Fecha Aprobación</th>
                        <th>Categoría Desbloqueada</th>
                        <th>Test Desbloqueado</th>
                        <th>Fecha Desbloqueo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($desbloqueos)): ?>
                        <tr>
                            <td colspan="6">No hay registros de desbloqueos.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($desbloqueos as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['usuario'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['test_aprobado'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['fecha_aprobado'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['categoria_desbloqueada'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['test_desbloqueado'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['fecha_desbloqueo'], ENT_QUOTES) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>

    <script>
        window.BASE_URL = '<?= BASE_URL ?>';
    </script>
</body>
</html>
