<?php
/**
 * Actividad Formativa 2 - Gestión de Estudiantes (POO en PHP)
 * Versión compatible con PHP sin propiedades/retornos tipados (OnlineGDB).
 */

/* =========================
 * 1) MODELO DE DOMINIO
 * ========================= */

class Persona {
    protected $nombre;
    protected $email;

    public function __construct($nombre, $email) {
        $this->nombre = $nombre;
        $this->email  = $email;
    }

    public function getNombre() { return $this->nombre; }
    public function getEmail()  { return $this->email; }

    public function resumen() {
        return "Persona: {$this->nombre} ({$this->email})";
    }
}

class Estudiante extends Persona {
    private $matricula;
    private $calificaciones; // [materia => float]

    public function __construct($nombre, $email, $matricula) {
        parent::__construct($nombre, $email);
        $this->matricula = $matricula;
        $this->calificaciones = array();
    }

    public function getMatricula() { return $this->matricula; }

    public function agregarCalificacion($materia, $calificacion) {
        // Validación mínima
        if (!is_numeric($calificacion) || $calificacion < 0 || $calificacion > 100) {
            throw new InvalidArgumentException("La calificación debe estar entre 0 y 100.");
        }
        $this->calificaciones[$materia] = floatval($calificacion);
    }

    public function promedio() {
        if (empty($this->calificaciones)) return 0.0;
        return array_sum($this->calificaciones) / count($this->calificaciones);
    }

    public function resumen() {
        $prom = number_format($this->promedio(), 2);
        return "Estudiante: {$this->nombre} [{$this->matricula}] - Email: {$this->email} - Promedio: {$prom}";
    }
}

class GestorEstudiantes {
    // índice por matrícula
    private $estudiantes = array();

    public function agregar(Estudiante $e) {
        $id = $e->getMatricula();
        if (isset($this->estudiantes[$id])) {
            throw new RuntimeException("La matrícula $id ya existe.");
        }
        $this->estudiantes[$id] = $e;
    }

    public function obtenerPorMatricula($matricula) {
        return isset($this->estudiantes[$matricula]) ? $this->estudiantes[$matricula] : null;
    }

    public function listar() {
        return array_values($this->estudiantes);
    }

    public function eliminar($matricula) {
        if (!isset($this->estudiantes[$matricula])) return false;
        unset($this->estudiantes[$matricula]);
        return true;
    }
}

/* =========================
 * 2) PRUEBAS / "MAIN"
 * ========================= */

$gestor = new GestorEstudiantes();

$e1 = new Estudiante("Ana López",    "ana@example.com",   "A001");
$e2 = new Estudiante("Luis Ramírez", "luis@example.com",  "A002");
$e3 = new Estudiante("Marta Díaz",   "marta@example.com", "A003");

$e1->agregarCalificacion("POO", 95);
$e1->agregarCalificacion("BD",  88);

$e2->agregarCalificacion("POO", 72);
$e2->agregarCalificacion("BD",  80);
$e2->agregarCalificacion("Redes", 90);

$e3->agregarCalificacion("POO", 100);

$gestor->agregar($e1);
$gestor->agregar($e2);
$gestor->agregar($e3);

echo "=== LISTA DE ESTUDIANTES ===\n";
foreach ($gestor->listar() as $est) {
    echo $est->resumen() . "\n";
}

echo "\n=== BUSCAR A002 ===\n";
$buscado = $gestor->obtenerPorMatricula("A002");
echo $buscado ? $buscado->resumen() . "\n" : "No encontrado\n";

echo "\n=== ELIMINAR A003 ===\n";
$gestor->eliminar("A003");
foreach ($gestor->listar() as $est) {
    echo $est->resumen() . "\n";
}

echo "\n=== PROBAR ERROR DE MATRÍCULA DUPLICADA ===\n";
try {
    $gestor->agregar(new Estudiante("Otra Persona", "otra@example.com", "A001"));
} catch (RuntimeException $ex) {
    echo "Error capturado: " . $ex->getMessage() . "\n";
}
