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
    $pdoStatement=$pdo->prepare("INSERT INTO usuario (name, lastname, email, dni, login, password) VALUES (?,?,?,?,?,?)");
    $nombre=$_POST["nombreUsuario"];
    $apellido=$_POST["apellidosUsuario"];
    $email=$_POST["email"];
    $dni=$_POST["dniUsuario"];
    $login=$_POST["loginUsuario"];
    $contraseña=password_hash($_POST["contraseña"], PASSWORD_DEFAULT);
    
    $pdoStatement->bindParam(1, $nombre);
    $pdoStatement->bindParam(2, $apellido);
    $pdoStatement->bindParam(3, $email);
    $pdoStatement->bindParam(4, $dni);
    $pdoStatement->bindParam(5, $login);
    $pdoStatement->bindParam(6, $contraseña);
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
