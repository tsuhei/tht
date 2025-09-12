<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Pregunta
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getPreguntasParaTest(int $testId): array
{
    // Obtener la categoría del test
    $this->db->query("SELECT id_categoria FROM tests WHERE id = :test_id");
    $this->db->bind(':test_id', $testId, PDO::PARAM_INT);
    $testInfo = $this->db->single();

    if (!$testInfo) {
        return []; // No se encontró el test
    }

    $categoriaId = $testInfo['id_categoria'];

    // Obtener todas las señas de la categoría
    $this->db->query("SELECT id AS sena_id, palabra, media_url FROM senas WHERE id_categoria = :id_categoria");
    $this->db->bind(':id_categoria', $categoriaId, PDO::PARAM_INT);
    $senasDeCategoria = $this->db->resultSet();

    if (count($senasDeCategoria) < 2) {
        // No hay suficientes señas para generar preguntas con opciones incorrectas
        return [];
    }

    // Mezclar todas las señas para seleccionar aleatoriamente
    shuffle($senasDeCategoria);

    // Seleccionar máximo 10 preguntas
    $numPreguntas = min(10, count($senasDeCategoria));
    $seleccionadas = array_slice($senasDeCategoria, 0, $numPreguntas);

    $preguntas = [];

    foreach ($seleccionadas as $senaCorrecta) {
        // Opciones incorrectas: todas menos la correcta
        $opcionesIncorrectas = array_filter($senasDeCategoria, function($sena) use ($senaCorrecta) {
            return $sena['sena_id'] !== $senaCorrecta['sena_id'];
        });

        // Seleccionar una opción incorrecta aleatoria
        $senaIncorrecta = $opcionesIncorrectas[array_rand($opcionesIncorrectas)];

        // Crear opciones mezcladas
        $opciones = [
            ['sena_id' => $senaCorrecta['sena_id'], 'video_url' => $senaCorrecta['media_url'], 'correcta' => true],
            ['sena_id' => $senaIncorrecta['sena_id'], 'video_url' => $senaIncorrecta['media_url'], 'correcta' => false],
        ];
        shuffle($opciones);

        $preguntas[] = [
            'id_pregunta' => $senaCorrecta['sena_id'],
            'texto_pregunta' => '¿Cuál es la seña de "' . htmlspecialchars($senaCorrecta['palabra'], ENT_QUOTES) . '"?',
            'opciones' => $opciones,
            'respuesta_correcta_id' => $senaCorrecta['sena_id'],
        ];
    }

    // Mezclar las preguntas para que no salgan siempre en el mismo orden
    shuffle($preguntas);

    return $preguntas;
}

    
}
