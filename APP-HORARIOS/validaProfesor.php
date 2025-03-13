<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: rexistro.php");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Error: la validación CSRF ha fallado. Por favor, inténtelo de nuevo.";
    header("Location: rexistro.php");
    exit();
}

require_once "conexion.php";

try {

    $pdoStatement=$pdo->prepare("INSERT INTO profesor (name, lastname, email) VALUES (?,?,?)");

    $nombre=$_POST["nombreProfesor"];
    $apellido=$_POST["apellidoProfesor"];
    $email=$_POST["emailProfesor"];

    $pdoStatement->bindParam(1, $nombre);
    $pdoStatement->bindParam(2, $apellido);
    $pdoStatement->bindParam(3, $email);
    $pdoStatement->execute();

    unset($_SESSION['csrf_token']);

    header("Location: admin.php");
    exit();

} catch (PDOException $e) {

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