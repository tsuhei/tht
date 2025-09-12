<?php
// Variables seguras
$senas      = $senas     ?? [];
$sena       = $sena      ?? null;
$term       = $term      ?? '';
$adminName  = $adminName ?? 'Invitado';
$showCreate = $showCreate ?? false; // Asegurar que esté definida
$showEdit   = $showEdit   ?? false; // Asegurar que esté definida
$showModal  = $showModal  ?? false; // Asegurar que esté definida
$cats       = $cats      ?? []; // Asegurar que esté definida

// Mensajes flash
$error   = $error   ?? null;
$success = $success ?? null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/users.css">
    <title>Admin | Señas</title>
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
                <li><a href="<?= BASE_URL ?>admin/senas"class="active">Señas</a></li>
                <li><a href="<?= BASE_URL ?>admin/tests">Tests</a></li>
                <li><a href="<?= BASE_URL ?>admin/desbloqueos">Desbloqueos</a></li>
                <li><a href="<?= BASE_URL ?>admin/progreso">Progresos</a></li>
            </ul>
        </aside>

        <section class="main-content">
            <div class="header">
                <a href="<?= BASE_URL ?>admin/senas?create=1" class="btn">Nueva Seña</a>
                <form action="<?= BASE_URL ?>admin/senas/search" method="get">
                    <input
                        type="search"
                        name="q"
                        value="<?= htmlspecialchars($term, ENT_QUOTES) ?>"
                        placeholder="Buscar palabra o categoría">
                    <button class="btn">Buscar</button>
                </form>
            </div>

            <?php if ($error): ?>
                <div class="flash-message flash-error">
                    <?= htmlspecialchars($error, ENT_QUOTES) ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="flash-message flash-success">
                    <?= htmlspecialchars($success, ENT_QUOTES) ?>
                </div>
            <?php endif; ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Categoría</th>
                        <th>Palabra</th>
                        <th>Descripción</th>
                        <th>Video</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($senas)): ?>
                        <tr>
                            <td colspan="6">No hay señas registradas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($senas as $s): ?>
                            <tr>
                                <td><?= $s['id'] ?></td>
                                <td><?= htmlspecialchars($s['nom_categoria'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($s['palabra'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($s['descripcion'], ENT_QUOTES) ?></td>
                                <td>
                                    <?php if (! empty($s['media_url'])): ?>
                                        <a href="<?= BASE_URL . $s['media_url'] ?>" target="_blank">Ver</a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a
                                        href="<?= BASE_URL ?>admin/senas?edit=<?= $s['id'] ?>"
                                        class="btn btn-sm btn-secondary">✏️</a>
                                    <form
                                        action="<?= BASE_URL ?>admin/senas/delete/<?= $s['id'] ?>"
                                        method="post"
                                        class="form-delete"
                                        style="display:inline"
                                        data-name="<?= htmlspecialchars($s['palabra'], ENT_QUOTES) ?>">
                                        <button type="submit" class="btn-delete">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Modal crear/editar -->
        <div id="modal-form" class="modal <?= $showModal ? 'open' : 'hidden' ?>">
            <div class="modal-content">
                <a href="<?= BASE_URL ?>admin/senas" class="close">&times;</a>
                <form
                    action="<?= $showEdit
                                ? BASE_URL . "admin/senas/update/{$sena['id']}"
                                : BASE_URL . 'admin/senas/store' ?>"
                    method="post"
                    enctype="multipart/form-data">
                    <?php if ($showEdit): ?>
                        <input type="hidden" name="id" value="<?= $sena['id'] ?>">
                    <?php endif; ?>

                    <label for="id_categoria">Categoría</label>
                    <select name="id_categoria" id="id_categoria" required>
                        <option value="">-- Selecciona --</option>
                        <?php foreach ($cats as $c): ?>
                            <option
                                value="<?= $c['id'] ?>"
                                <?= $showEdit && $sena && $c['id'] == $sena['id_categoria'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nom_categoria'], ENT_QUOTES) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="palabra">Palabra</label>
                    <input
                        type="text"
                        name="palabra"
                        id="palabra"
                        required
                        value="<?= htmlspecialchars($sena['palabra'] ?? '', ENT_QUOTES) ?>">

                    <label for="descripcion">Descripción</label>
                    <textarea
                        name="descripcion"
                        id="descripcion"
                        rows="2"><?= htmlspecialchars($sena['descripcion'] ?? '', ENT_QUOTES) ?></textarea>

                    <label for="media_url">Video</label>
                    <input
                        type="file"
                        name="media_url"
                        id="media_url"
                        accept="video/*">
                    <?php if ($showEdit && ! empty($sena['media_url'])): ?>
                        <small>Archivo actual:
                            <a href="<?= BASE_URL . $sena['media_url'] ?>" target="_blank">Ver</a>
                        </small>
                    <?php endif; ?>

                    <button type="submit" class="btn">
                        <?= $showEdit ? 'Actualizar' : 'Guardar' ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Modal confirm delete -->
        <div id="modal-delete" class="modal hidden">
            <div class="modal-backdrop"></div>
            <div class="modal-content">
                <button class="close-delete">&times;</button>
                <h2>Eliminar seña</h2>
                <p id="modal-delete-message"></p>
                <div class="modal-actions">
                    <button class="btn btn-secondary close-delete">Cancelar</button>
                    <button class="btn btn-danger confirm-delete">Eliminar</button>
                </div>
            </div>
        </div>

        <script>
            window.BASE_URL = '<?= BASE_URL ?>';
        </script>
        <script src="<?= BASE_URL ?>js/senas.js" defer></script>
    </div>
</body>

</html>
