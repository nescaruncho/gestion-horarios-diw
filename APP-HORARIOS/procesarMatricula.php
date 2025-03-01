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
    
    // Verifica si se seleccionó al menos un módulo
    if (!isset($_POST['modulos']) || empty($_POST['modulos'])) {
        $_SESSION['error'] = "Error: Debes seleccionar al menos un módulo.";
        header("Location: matriculaCiclo.php");
        exit();
    }

    // Verifica si se seleccionó un ciclo
    if (!isset($_POST['ciclo']) || empty($_POST['ciclo'])) {
        $_SESSION['error'] = "Error: Debes seleccionar un ciclo.";
        header("Location: matriculaCiclo.php");
        exit();
    }
    
    // Prepara la consulta una sola vez
    $pdoStatement = $pdo->prepare("INSERT INTO user_modulo (id_user, id_modulo) VALUES (?, ?)");
    
    // Itera sobre cada módulo seleccionado
    foreach ($_POST['modulos'] as $id_modulo) {
        // Ejecuta la inserción para cada módulo
        $pdoStatement->execute([$id_user, $id_modulo]);
    }
    
    unset($_SESSION['csrf_token']);
    header("Location: mostra.php");
    exit();
    
} catch (Exception $e) {
    $errorInfo = $e->getMessage();
    if (strpos($errorInfo, '1062') !== false) {
        $mensaje = "Error: Ya estás matriculado en uno o más de los módulos seleccionados.";
    } else if (strpos($errorInfo, '1048') !== false) {
        $mensaje = "Error: Faltan datos necesarios para la matrícula.";
    } else {
        $mensaje = "Error: " . $e->getMessage();
    }
    
    unset($_SESSION['csrf_token']);
    $_SESSION['error'] = $mensaje;
    header("Location: matriculaCiclo.php");
    exit();
}