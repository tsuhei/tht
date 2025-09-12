<?php
$preguntas = $preguntas ?? [];
$categoria = $categoria ?? ['id' => 0, 'nom_categoria' => 'Categoría Desconocida'];
$testId = $testId ?? 0;
$nombreTest = $nombreTest ?? 'Test';
$active = $active ?? 'test';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= BASE_URL ?>favicon.ico" type="image/x-icon">
    <title>Test de <?= htmlspecialchars($categoria['nom_categoria'], ENT_QUOTES) ?> | The Hands Talk</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/test_categoria1.css">
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

    <main class="main-content test-main-content">
        <div class="test-header">
            <h2 class="section-title">Test: <?= htmlspecialchars($nombreTest, ENT_QUOTES) ?></h2>
            <p class="test-subtitle">Categoría: <?= htmlspecialchars($categoria['nom_categoria'], ENT_QUOTES) ?></p>
        </div>

        <div class="test-container">
            <div class="progress-info">
                <span id="progress-text">Pregunta 1 de <?= count($preguntas) ?></span>
                <div class="progress-bar-container">
                    <div class="progress-bar" id="progress-bar" style="width: 0%;"></div>
                </div>
            </div>

            <div id="question-display" class="question-display">
                <!-- Las preguntas se cargarán aquí dinámicamente con JavaScript -->
            </div>

            <div class="test-controls">
                <button id="next-question-btn" class="btn-next-question" style="display: none;">
                    <span class="btn-icon">→</span>
                    Siguiente Pregunta
                </button>
            </div>
        </div>

        <!-- Modal de Feedback -->
        <div id="feedback-modal" class="modal hidden">
            <div class="modal-overlay"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h3>🎉 Resultados del Test</h3>
                </div>
                <div class="modal-body">
                    <div class="score-display">
                        <div class="score-circle">
                            <span id="modal-score" class="score-number">0</span>
                            <span class="score-label">puntos</span>
                        </div>
                        <p id="modal-percentage" class="score-percentage">0%</p>
                    </div>
                    <p id="modal-message" class="result-message">¡Buen trabajo!</p>
                </div>
                <div class="modal-footer">
                    <button id="modal-close-btn" class="btn-close-modal">
                        <span class="btn-icon">←</span>
                        Volver a Tests
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        const preguntasData = <?= json_encode($preguntas) ?>;
        const testId = <?= $testId ?>;
        const categoriaId = <?= $categoria['id'] ?>;
        const baseUrl = '<?= BASE_URL ?>';
        const totalQuestions = <?= count($preguntas) ?>;
    </script>
    <script src="<?= BASE_URL ?>js/main.js" defer></script>
    <script src="<?= BASE_URL ?>js/test_logic.js" defer></script>
    <script src="<?= BASE_URL ?>js/theme.js" defer></script>
</body>

</html>