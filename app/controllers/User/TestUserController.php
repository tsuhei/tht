<?php

namespace App\Controllers\User;

use App\Core\Controller;
use App\Models\Categoria;
use App\Models\Pregunta;
use App\Models\Test;
use App\Models\ProgresoCategoria;

class TestUserController extends Controller
{
    private Categoria $categoriaModel;
    private Pregunta $preguntaModel;
    private Test $testModel;

    public function __construct()
    {
        parent::__construct();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['usuario'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->categoriaModel = new Categoria();
        $this->preguntaModel = new Pregunta();
        $this->testModel = new Test();
    }

    public function index(): void
    {
        $userName = $_SESSION['usuario']['nom_usuario'] ?? 'Usuario';
        $active = 'test';

        $categories = $this->categoriaModel->all();

        require __DIR__ . '/../../views/usuarios/test_user.php';
    }

    public function iniciarTest(int $id): void
    {
        error_log("Iniciando test para ID de categoría: " . $id);

        // 1. Obtener la categoría
        $categoria = $this->categoriaModel->find($id);
        error_log("Categoría encontrada: " . json_encode($categoria));

        // 2. Obtener los tests asociados a la categoría
        $testsForCategory = $this->testModel->getByCategory($id);
        error_log("Tests para la categoría: " . json_encode($testsForCategory));

        $selectedTest = null;
        if (!empty($testsForCategory)) {
            $selectedTest = $testsForCategory[0];
        }
        error_log("Test seleccionado: " . json_encode($selectedTest));

        // 3. Validar si la categoría o el test existen
        if (!$categoria || !$selectedTest) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'No se encontró el test para esta categoría o la categoría no existe.'
            ];
            header("Location: " . BASE_URL . "usuarios/test");
            exit;
        }

        // 4. Extraer los datos del test seleccionado
        $testId = (int) $selectedTest['id_test'];
        $nombreTest = $selectedTest['nombre_test'];
        error_log("ID del test: " . $testId . ", Nombre del test: " . $nombreTest);

        // 5. Obtener preguntas del test
        $preguntas = $this->preguntaModel->getPreguntasParaTest($testId);
        error_log("Preguntas generadas: " . json_encode($preguntas));

        if (empty($preguntas)) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'No hay suficientes señas para generar un test para esta categoría.'
            ];
            header("Location: " . BASE_URL . "usuarios/test");
            exit;
        }

        // Guardar datos en sesión para procesamiento posterior
        $_SESSION['preguntas_test'] = $preguntas;
        $_SESSION['test_id'] = $testId;
        $_SESSION['categoria_id'] = $categoria['id'];

        // 6. Renderizar la vista con los datos
        echo $this->view('usuarios/test_categorias', [
            'categoria'  => $categoria,
            'testId'     => $testId,
            'nombreTest' => $nombreTest,
            'preguntas'  => $preguntas,
            'userName'   => $_SESSION['usuario']['nom_usuario'] ?? 'Usuario',
            'active'     => 'test'
        ]);
    }

    public function enviarResultado(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'usuarios/test');
            exit;
        }

        $respuestasUsuario = json_decode($_POST['respuestas'] ?? '[]', true);

        // Recuperar preguntas y test guardados en sesión
        $preguntasDelTest = $_SESSION['preguntas_test'] ?? [];
        $testId = $_SESSION['test_id'] ?? 0;
        $categoriaId = $_SESSION['categoria_id'] ?? 0;
        $userId = $_SESSION['usuario']['id'] ?? 0;

        if (empty($respuestasUsuario) || empty($preguntasDelTest) || $testId === 0 || $userId === 0 || $categoriaId === 0) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos o sesión expirada.']);
            exit;
        }

        // Crear mapa pregunta_id => respuesta_correcta_id
        $respuestasCorrectasMap = [];
        foreach ($preguntasDelTest as $pregunta) {
            $respuestasCorrectasMap[$pregunta['id_pregunta']] = $pregunta['respuesta_correcta_id'];
        }

        $respuestasCorrectasCount = 0;

        foreach ($respuestasUsuario as $respuesta) {
            $preguntaId = $respuesta['pregunta_id'];
            $respuestaElegidaId = $respuesta['respuesta_elegida_id'];

            if (isset($respuestasCorrectasMap[$preguntaId]) && $respuestasCorrectasMap[$preguntaId] === $respuestaElegidaId) {
                $respuestasCorrectasCount++;
            }
        }

        $totalPreguntas = count($preguntasDelTest);
        $porcentajeAcierto = ($totalPreguntas > 0) ? ($respuestasCorrectasCount / $totalPreguntas) * 100 : 0;

        // Registrar progreso si aplica
        if ($porcentajeAcierto >= 70) {
            if (!ProgresoCategoria::exists($userId, $categoriaId)) {
                ProgresoCategoria::add($userId, $categoriaId);
            }
        }

        // Limpiar preguntas de sesión para evitar reutilización
        unset($_SESSION['preguntas_test'], $_SESSION['test_id'], $_SESSION['categoria_id']);

        echo json_encode([
            'success' => true,
            'score' => $respuestasCorrectasCount,
            'total' => $totalPreguntas,
            'percentage' => round($porcentajeAcierto, 2),
            'message' => ($porcentajeAcierto >= 70) ? '¡Felicidades! Has aprobado el test.' : 'Sigue practicando, ¡casi lo logras!',
        ]);
        exit;
    }
}
