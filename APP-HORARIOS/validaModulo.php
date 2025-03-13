<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Error: la validación CSRF ha fallado. Por favor, inténtelo de nuevo.";
    header("Location: login.php");
    exit();
}

require_once "conexion.php";

try {
    $pdoStatement=$pdo->prepare("INSERT INTO modulo (name, curso, horas_totales) VALUES (?,?,?)");
    $pdoStatement->bindParam(1, $_POST["nombreModulo"]);
    $pdoStatement->bindParam(2, $_POST["cursoModulo"]);
    $pdoStatement->bindParam(3, $_POST["horasModulo"]);
    $pdoStatement->execute();

    unset($_SESSION['csrf_token']);

    header("Location: admin.php");
    exit();

} catch (PDOException $e) {
    $errorInfo = $e->errorInfo;
    $mensaje = "Error: " . $e->getMessage();

    unset($_SESSION['csrf_token']);
    
    $_SESSION['error'] = $mensaje;
    header("Location: admin.php");
    exit();

}
