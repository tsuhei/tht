<?php
// Asegurar variables
$usuarios  = $usuarios  ?? [];
$usuario   = $usuario   ?? null;
$term      = $term      ?? '';
$adminName = $adminName ?? 'Invitado';
$show      = $show      ?? false;

// Detectar create vs edit si también usas ?create=1
$showCreate = isset($_GET['create']);
$showEdit   = $show && $usuario !== null;
$showModal  = $showCreate || $showEdit;
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/users.css">
    <title>Admin | Usuarios</title>
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
                <li><a href="<?= BASE_URL ?>admin/users" class="active">Usuarios</a></li>
                <li><a href="<?= BASE_URL ?>admin/categorias">Categorías</a></li>
                <li><a href="<?= BASE_URL ?>admin/senas">Señas</a></li>
                <li><a href="<?= BASE_URL ?>admin/tests">Tests</a></li>
                <li><a href="<?= BASE_URL ?>admin/desbloqueos">Desbloqueos</a></li>
                <li><a href="<?= BASE_URL ?>admin/progreso">Progresos</a></li>
            </ul>
        </aside>

        <section class="main-content">
            <div class="header">
                <!-- Nuevo Usuario abre el modal en blanco -->
                <a href="<?= BASE_URL ?>admin/users?create=1" class="btn">Nuevo Usuario</a>

                <form id="form-search" action="<?= BASE_URL ?>admin/users/search" method="get">
                    <input
                        type="search"
                        name="q"
                        value="<?= htmlspecialchars($term ?? '', ENT_QUOTES) ?>"
                        placeholder="Buscar ID, usuario o rol">
                    <button type="submit" class="btn">Buscar</button>
                </form>

                <div class="actions">
                    <a href="<?= BASE_URL ?>admin/users/export/excel" class="btn">Excel</a>
                    <a href="<?= BASE_URL ?>admin/users/export/pdf" class="btn">PDF</a>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Rol</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['id'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($u['nom_rol'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($u['nom_usuario'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($u['correo'], ENT_QUOTES) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>admin/users/edit/<?= htmlspecialchars($u['id'], ENT_QUOTES) ?>"
                                    class="btn btn-sm btn-primary">Editar</a>

                                <form
                                    class="form-delete"
                                    action="<?= BASE_URL ?>admin/users/delete/<?= $u['id'] ?>"
                                    method="post"
                                    style="display:inline"
                                    data-username="<?= htmlspecialchars($u['nom_usuario'], ENT_QUOTES) ?>">
                                    <button
                                        type="submit"
                                        style="background:none;border:none;color:#c00;cursor:pointer;">
                                        🗑
                                    </button>
                                </form>


                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Modal compartido: Create vs Edit -->
        <div id="modal-form" class="modal <?= $showModal ? 'open' : 'hidden' ?>">
            <div class="modal-content">
                <a href="<?= BASE_URL ?>admin/users" class="close">&times;</a>

                <form
                    id="user-form"
                    method="post"
                    action="<?= $showEdit
                                ? BASE_URL . "admin/users/update/{$usuario['id']}"
                                : BASE_URL . 'admin/users/store' ?>">
                    <?php if ($showEdit): ?>
                        <input
                            type="hidden"
                            name="id"
                            value="<?= htmlspecialchars($usuario['id'], ENT_QUOTES) ?>">
                    <?php endif; ?>

                    <label for="id_rol">Rol</label>
                    <select name="id_rol" id="id_rol">
                        <option
                            value="1"
                            <?= $showEdit && $usuario['id_rol'] == 1 ? 'selected' : '' ?>>Administrador</option>
                        <option
                            value="2"
                            <?= $showEdit && $usuario['id_rol'] == 2 ? 'selected' : '' ?>>Usuario</option>
                    </select>

                    <label for="nom_usuario">Usuario</label>
                    <input
                        type="text"
                        name="nom_usuario"
                        id="nom_usuario"
                        required
                        value="<?= htmlspecialchars($usuario['nom_usuario'] ?? '', ENT_QUOTES) ?>">

                    <label for="correo">Correo</label>
                    <input
                        type="email"
                        name="correo"
                        id="correo"
                        required
                        value="<?= htmlspecialchars($usuario['correo'] ?? '', ENT_QUOTES) ?>">

                    <label for="contrasena">Contraseña</label>
                    <input
                        type="password"
                        name="contrasena"
                        id="contrasena"
                        <?= $showCreate ? 'required' : '' ?>
                        placeholder="<?= $showEdit ? 'Dejar vacío para no cambiar' : '' ?>">

                    <button type="submit" class="btn">Guardar</button>
                </form>
            </div>
        </div>

        <!-- Modal de confirmación -->
        <!-- Modal de confirmación -->
        <div id="modal-delete" class="modal hidden">
            <div class="modal-backdrop"></div>
            <div class="modal-content">
                <button type="button" class="modal-close">&times;</button>
                <h2>Eliminar usuario</h2>
                <p id="modal-delete-message"></p>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary modal-cancel">Cancelar</button>
                    <button type="button" class="btn btn-danger modal-confirm">Eliminar</button>
                </div>
            </div>
        </div>


        <script>
            window.BASE_URL = '<?= BASE_URL ?>';
        </script>
        <script src="<?= BASE_URL ?>js/user.js" defer></script>
</body>

</html>