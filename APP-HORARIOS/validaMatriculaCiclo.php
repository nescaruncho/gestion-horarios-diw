<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Error: la validación CSRF ha fallado. Por favor, inténtelo de nuevo.";
    header("Location: login.php");
    exit();
}

require_once "conexion.php";

try {
    $id_user = $_SESSION["usuario_id"];
    
    // Verifica si se seleccionó al menos un ciclo
    if (!isset($_POST['ciclos']) || empty($_POST['ciclos'])) {
        $_SESSION['error'] = "Error: Debes seleccionar al menos un ciclo.";
        header("Location: mostra.php");
        exit();
    }
    
    // Prepara la consulta una sola vez
    $pdoStatement = $pdo->prepare("INSERT INTO usuario_ciclo (id_user, id_ciclo) VALUES (?, ?)");
    
    // Itera sobre cada ciclo seleccionado
    foreach ($_POST['ciclos'] as $codigo_ciclo) {
        // Primero obtén el id_ciclo correspondiente al código
        $stmt = $pdo->prepare("SELECT id_ciclo FROM ciclo WHERE codigo = ?");
        $stmt->execute([$codigo_ciclo]);
        $id_ciclo = $stmt->fetchColumn();
        
        if (!$id_ciclo) {
            continue; // Omite ciclos inválidos
        }
        
        // Ejecuta la inserción para cada ciclo
        $pdoStatement->bindParam(1, $id_user);
        $pdoStatement->bindParam(2, $id_ciclo);
        $pdoStatement->execute();
    }
    
    unset($_SESSION['csrf_token']);
    header("Location: mostra.php");
    exit();
    
} catch (Exception $e) {
    $errorInfo = $e->getMessage();
    if (strpos($errorInfo, '1062') !== false) {
        $mensaje = "Error: Ya estás matriculado en uno o más de los ciclos seleccionados.";
    } else if (strpos($errorInfo, '1048') !== false) {
        $mensaje = "Error: Faltan datos necesarios para la matrícula.";
    } else {
        $mensaje = "Error: " . $e->getMessage();
    }
    
    unset($_SESSION['csrf_token']);
    $_SESSION['error'] = $mensaje;
    header("Location: mostra.php");
    exit();
}