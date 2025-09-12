<?php
// Variables por defecto
$tests       = $tests       ?? [];
$categories  = $categories  ?? [];
$test        = $test        ?? null;
$adminName   = $adminName   ?? 'Invitado';
$showModal   = $showModal   ?? false;
$showCreate  = $showCreate  ?? false;
$showEdit    = $showEdit    ?? false;
$errors      = $errors      ?? [];
$old         = $old         ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/users.css">
    <title>Admin | Tests</title>
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
                <li><a href="<?= BASE_URL ?>admin/tests" class="active">Tests</a></li>
                <li><a href="<?= BASE_URL ?>admin/desbloqueos">Desbloqueos</a></li>
                <li><a href="<?= BASE_URL ?>admin/progreso">Progresos</a></li>
            </ul>
        </aside>

        <section class="main-content">
            <div class="header">
                <a href="<?= BASE_URL ?>admin/tests?create=1" class="btn">+ Nuevo Test</a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tests as $t): ?>
                        <tr>
                            <td><?= $t['id'] ?></td>
                            <td><?= htmlspecialchars($t['nombre_test'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($t['nom_categoria'], ENT_QUOTES) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>admin/tests?edit=<?= $t['id'] ?>"
                                   class="btn btn-sm btn-secondary">✏️</a>
                                <form action="<?= BASE_URL ?>admin/tests/delete/<?= $t['id'] ?>"
                                      method="post"
                                      class="form-delete"
                                      style="display:inline">
                                    <button type="submit" class="btn-delete">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Modal Crear/Editar -->
        <div id="modal-form" class="modal <?= $showModal ? 'open' : 'hidden' ?>">
            <div class="modal-content">
                <a href="<?= BASE_URL ?>admin/tests" class="close">&times;</a>

                <?php if ($errors): ?>
                    <div class="errors">
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err, ENT_QUOTES) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= $showEdit
                                   ? BASE_URL . "admin/tests/update/{$test['id']}"
                                   : BASE_URL . 'admin/tests/store' ?>"
                      method="post">
                    <?php if ($showEdit): ?>
                        <input type="hidden" name="id" value="<?= $test['id'] ?>">
                    <?php endif; ?>

                    <label for="nombre_test">Nombre del test</label>
                    <input
                        type="text"
                        id="nombre_test"
                        name="nombre_test"
                        required
                        value="<?= htmlspecialchars(
                            $old['nombre_test'] 
                            ?? $test['nombre_test'] 
                            ?? '', ENT_QUOTES
                        ) ?>">

                    <label for="id_categoria">Categoría</label>
                    <select name="id_categoria" id="id_categoria" required>
                        <option value="">-- Selecciona categoría --</option>
                        <?php foreach ($categories as $c): ?>
                            <?php
                                $selOld = $old['id_categoria'] ?? null;
                                $selDb  = $test['id_categoria'] ?? null;
                                $selected = ($selOld !== null)
                                    ? ($selOld == $c['id'])
                                    : ($selDb == $c['id']);
                            ?>
                            <option value="<?= $c['id'] ?>"
                                <?= $selected ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nom_categoria'], ENT_QUOTES) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn">
                        <?= $showEdit ? 'Actualizar' : 'Guardar' ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Modal Confirmar Borrado -->
        <div id="modal-delete" class="modal hidden">
            <div class="modal-backdrop"></div>
            <div class="modal-content">
                <button class="close-delete">&times;</button>
                <h2>Eliminar test</h2>
                <p id="modal-delete-message">¿Seguro que deseas eliminar este test?</p>
                <div class="modal-actions">
                    <button class="btn btn-secondary close-delete">Cancelar</button>
                    <button class="btn btn-danger confirm-delete">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <script src="<?= BASE_URL ?>js/tests.js" defer></script>
</body>
</html>
