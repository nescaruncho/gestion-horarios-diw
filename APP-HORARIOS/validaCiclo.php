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
    $pdoStatement=$pdo->prepare("INSERT INTO ciclo (codigo, name) VALUES (?,?)");
    $codigo=$_POST["codigoCiclo"];
    $nombre=$_POST["nombreCiclo"];
    
    $pdoStatement->bindParam(1, $codigo);
    $pdoStatement->bindParam(2, $nombre);
    $pdoStatement->execute();

    unset($_SESSION['csrf_token']);

    header("Location: admin.php");
    exit();

} catch (Exception $e) {
    $errorInfo = $e->errorInfo;
    if (isset($errorInfo[1]) && $errorInfo[1] == 1062) {
        $mensaje = "Error: El nombre de usuario o el email ya están en uso.";
    } else {
        $mensaje = "Error: " . $e->getMessage();
    }

    unset($_SESSION['csrf_token']);
    
    $_SESSION['error'] = $mensaje;
    header("Location: admin.php");
    exit();

}
