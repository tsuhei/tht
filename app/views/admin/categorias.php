<?php
// views/admin/categories.php

// Variables seguras
$categories   = $categories   ?? [];
$category     = $category     ?? null;
$term         = $term         ?? '';
$adminName    = $adminName    ?? 'Invitado';
$show         = $show         ?? false;

// Detectar nuevo vs edición
$showCreate   = isset($_GET['create']);
$showEdit     = $show && $category !== null;
$showModal    = $showCreate || $showEdit;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/users.css">
    <title>Admin | Categorías</title>
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
                <li><a href="<?= BASE_URL ?>admin/categorias" class="active">Categorías</a></li>
                <li><a href="<?= BASE_URL ?>admin/senas">Señas</a></li>
                <li><a href="<?= BASE_URL ?>admin/tests">Tests</a></li>
                <li><a href="<?= BASE_URL ?>admin/desbloqueos">Desbloqueos</a></li>
                <li><a href="<?= BASE_URL ?>admin/progreso">Progresos</a></li>
            </ul>
        </aside>

        <section class="main-content">
            <div class="header">
                <a
                    href="<?= BASE_URL ?>admin/categorias?create=1"
                    class="btn">
                    + Nueva Categoría
                </a>

                <form
                    id="form-search"
                    action="<?= BASE_URL ?>admin/categorias/search"
                    method="get">
                    <input
                        type="search"
                        name="q"
                        value="<?= htmlspecialchars($term, ENT_QUOTES) ?>"
                        placeholder="Buscar ID o nombre">
                    <button type="submit" class="btn">Buscar</button>
                </form>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Icono</th> <!-- NUEVA COLUMNA -->
                        <th>Categoría</th>
                        <th>Video</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($categories as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['id'], ENT_QUOTES) ?></td>
                            <td>
                                <?php if (!empty($c['icono'])): ?>
                                    <img src="<?= BASE_URL . htmlspecialchars($c['icono'], ENT_QUOTES) ?>"
                                        alt="Icono <?= htmlspecialchars($c['nom_categoria'], ENT_QUOTES) ?>"
                                        style="height:30px; width:auto;">
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($c['nom_categoria'], ENT_QUOTES) ?></td>
                            <td>
                                <?php if (!empty($c['video_url'])): ?>
                                    <a href="<?= BASE_URL . $c['video_url'] ?>" target="_blank">Ver video</a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Editar -->
                                <a
                                    href="<?= BASE_URL ?>admin/categorias?edit=<?= $c['id'] ?>"
                                    class="btn btn-sm btn-secondary">
                                    ✏️
                                </a>

                                <!-- Borrar -->
                                <form
                                    id="delete-form-<?= $c['id'] ?>"
                                    class="form-delete"
                                    action="<?= BASE_URL ?>admin/categorias/delete/<?= $c['id'] ?>"
                                    method="post"
                                    style="display:inline"
                                    data-name="<?= htmlspecialchars($c['nom_categoria'], ENT_QUOTES) ?>">
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

        <!-- Modal crear/editar categoría -->
        <div
            id="modal-form"
            class="modal <?= $showModal ? 'open' : 'hidden' ?>">
            <div class="modal-content">
                <a
                    href="<?= BASE_URL ?>admin/categorias"
                    class="close">&times;</a>

                <form
                    id="category-form"
                    method="post"
                    action="<?= $showEdit
                                ? BASE_URL . "admin/categorias/update/{$category['id']}"
                                : BASE_URL . 'admin/categorias/store' ?>"
                    enctype="multipart/form-data">
                    <?php if ($showEdit): ?>
                        <input
                            type="hidden"
                            name="id"
                            value="<?= htmlspecialchars($category['id'], ENT_QUOTES) ?>">
                    <?php endif; ?>

                    <label for="nom_categoria">Nombre de la categoría</label>
                    <input
                        type="text"
                        id="nom_categoria"
                        name="nom_categoria"
                        required
                        value="<?= htmlspecialchars($category['nom_categoria'] ?? '', ENT_QUOTES) ?>">

                    <label for="video_url">Video</label>
                    <input
                        type="file"
                        id="video_url"
                        name="video_url"
                        accept="video/*">
                    <?php if ($showEdit && ! empty($category['video_url'])): ?>
                        <small class="form-text text-muted">
                            Video actual:
                            <a
                                href="<?= BASE_URL . $category['video_url'] ?>"
                                target="_blank">Ver</a>
                        </small>
                    <?php endif; ?>

                    <label for="icono">Icono</label>
                    <input
                        type="file"
                        id="icono"
                        name="icono"
                        accept="image/*">
                    <?php if ($showEdit && !empty($category['icono'])): ?>
                        <small class="form-text text-muted">
                            Icono actual:
                            <img
                                src="<?= BASE_URL . htmlspecialchars($category['icono'], ENT_QUOTES) ?>"
                                alt="Icono de <?= htmlspecialchars($category['nom_categoria'], ENT_QUOTES) ?>"
                                style="height: 30px;">
                        </small>
                    <?php endif; ?>

                    <button type="submit" class="btn">
                        <?= $showEdit ? 'Actualizar' : 'Guardar' ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Modal de confirmación de borrado -->
        <div id="modal-delete" class="modal hidden">
            <div class="modal-backdrop"></div>
            <div class="modal-content">
                <button type="button" class="modal-close">&times;</button>
                <h2>Eliminar categoría</h2>
                <p id="modal-delete-message"></p>
                <div class="modal-actions">
                    <button
                        type="button"
                        class="btn btn-secondary modal-cancel">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        class="btn btn-danger modal-confirm">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>

        <script>
            window.BASE_URL = '<?= BASE_URL ?>';
        </script>
        <script src="<?= BASE_URL ?>js/user.js" defer></script>
    </div>
</body>

</html>