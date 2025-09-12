<?php
$mode   = $mode   ?? 'login';
$errors = $errors ?? [];
$old    = $old    ?? [];
$containerClass = $mode === 'register' ? 'register-mode' : 'login-mode';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Auth | The Hands Talk</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/auth.css">
</head>

<body>
    <div class="auth-container <?= $containerClass ?>">
        <!-- PANEL AZUL -->
        <section class="info-panel">
            <div class="info-content">
                <img src="<?= BASE_URL ?>images/IconoTHTblanco.svg" alt="Logo" class="logo">
                <h1 class="app-name">The Hands Talk</h1>
                <p class="slogan">Conéctate sin palabras: ¡Habla con tus manos!</p>
            </div>
        </section>

        <!-- FORMULARIOS -->
        <section class="forms-side">
            <!-- LOGIN -->
            <div class="form-panel login-panel">
                <h2>Iniciar Sesión</h2>
                <form action="<?= BASE_URL ?>auth/login" method="post" novalidate>
                    <input type="hidden" name="action" value="login">

                    <label for="login-email">Correo</label>
                    <input id="login-email" type="email" name="correo" placeholder="Ingresar correo"
                        value="<?= htmlspecialchars($old['correo'] ?? '') ?>">
                    <span class="error"><?= $errors['correo'] ?? '' ?></span>

                    <label for="login-password">Contraseña</label>
                    <input id="login-password" type="password" name="contrasena" placeholder="Ingresar contraseña">
                    <span class="error"><?= $errors['contrasena'] ?? '' ?></span>
                    <?php if (!empty($errors['general'])): ?>
                        <div class="error"><?= $errors['general'] ?></div>
                        <?php endif; ?>

                    <a href="#" class="link forgot">¿Olvidaste tu contraseña?</a>
                    <button type="submit" class="btn">Iniciar Sesión</button>

                    <p class="switch-text">
                        ¿No tienes cuenta? <a href="#" class="switch" data-mode="register">Regístrate aquí</a>
                    </p>
                </form>
            </div>

            <!-- REGISTRO -->
            <div class="form-panel register-panel">
                <h2>Registro</h2>
                <form action="<?= BASE_URL ?>auth/register" method="post" novalidate>
                    <input type="hidden" name="action" value="register">

                    <label for="reg-username">Nombre de Usuario</label>
                    <input id="reg-username" type="text" name="nom_usuario" placeholder="Ingresar nombre de usuario"
                        value="<?= htmlspecialchars($old['nom_usuario'] ?? '') ?>">
                    <span class="error"><?= $errors['nom_usuario'] ?? '' ?></span>

                    <label for="reg-email">Correo</label>
                    <input id="reg-email" type="email" name="correo" placeholder="Ingresar correo"
                        value="<?= htmlspecialchars($old['correo'] ?? '') ?>">
                    <span class="error"><?= $errors['correo'] ?? '' ?></span>

                    <label for="reg-password">Contraseña</label>
                    <input id="reg-password" type="password" name="contrasena" placeholder="Ingresar contraseña">
                    <span class="error"><?= $errors['contrasena'] ?? '' ?></span>

                    <label for="reg-cpassword">Confirmar contraseña</label>
                    <input id="reg-cpassword" type="password" name="confirm_contra" placeholder="Confirmar contraseña">
                    <span class="error"><?= $errors['confirm_contra'] ?? '' ?></span>

                    <button type="submit" class="btn">Registrarse</button>
                    <p class="switch-text">
                        ¿Ya tienes cuenta? <a href="#" class="switch" data-mode="login">Inicia sesión aquí</a>
                    </p>
                </form>
            </div>
        </section>
    </div>

    <script>
        const container = document.querySelector('.auth-container');
        document.querySelectorAll('.switch').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const mode = link.dataset.mode;
                container.classList.remove('login-mode', 'register-mode');
                container.classList.add(`${mode}-mode`);
            });
        });
    </script>

</body>

</html>