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
    $pdoStatement=$pdo->prepare("INSERT INTO usuario_ciclo (id_user, id_ciclo) VALUES (?,?)");
    $idUsuario=$_SESSION["usuario_id"];
    $idCiclo=$_POST["ciclo"];

    $pdoStatement->bindParam(1, $idUsuario);
    $pdoStatement->bindParam(2, $idCiclo);
    $pdoStatement->execute();

    unset($_SESSION['csrf_token']);

    header("Location: mostra.php");
    exit();
} catch (PDOException $e) {
    $errorInfo = $e->errorInfo;
    if (isset($errorInfo[1]) && $errorInfo[1] == 1062) {
        $mensaje = "Error: El usuario ya está matriculado en este ciclo.";
    } else {
        $mensaje = "Error: " . $e->getMessage();
    }

    unset($_SESSION['csrf_token']);
    
    $_SESSION['error'] = $mensaje;
    header("Location: mostra.php");
    exit();
}