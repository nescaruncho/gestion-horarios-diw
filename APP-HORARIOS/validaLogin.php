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
    $pdoStatement=$pdo->prepare("SELECT * FROM usuario WHERE name=?");
    $nome=$_POST["nomeUsuario"];
    $pdoStatement->bindParam(1, $nome);
    $pdoStatement->execute();

    if ($pdoStatement->rowCount() == 1) {
        $fila = $pdoStatement->fetch();
        if (password_verify($_POST["contrasinal"], $fila['password'])) {
            
            $_SESSION['usuario_nome'] = $fila['name'];
            $_SESSION['usuario_id'] = $fila['id_user'];
            $_SESSION['usuario_rol'] = $fila['rol'];
            $_SESSION['logged_in'] = true;

            unset($_SESSION['csrf_token']);
            
            header("Location: mostra.php");
            exit();
        }
    }

    unset($_SESSION['csrf_token']);
    
    $_SESSION['error'] = "Usuario o contraseña incorrectos";
    header("Location: login.php");
    exit();

} catch (Exception $e) {
    $mensaje = "Error: " . $e->getMessage();

    unset($_SESSION['csrf_token']);
    
    $_SESSION['error'] = $mensaje;
    header("Location: rexistro.php");
    exit();
}