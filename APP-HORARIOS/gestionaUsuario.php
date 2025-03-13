<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}
require_once "conexion.php";

try {
    switch ($_POST["boton"]) {
        case 'editar':
            $pdoStatement=$pdo->prepare("UPDATE usuario SET name=?, lastname=?, email=?, dni=?, login=? WHERE id_user=?");
            $pdoStatement->bindParam(1, $_POST['nombreUsuario']);
            $pdoStatement->bindParam(2, $_POST['apellidoUsuario']);
            $pdoStatement->bindParam(3, $_POST['emailUsuario']);
            $pdoStatement->bindParam(4, $_POST['dniUsuario']);
            $pdoStatement->bindParam(5, $_POST['loginUsuario']);
            $pdoStatement->bindParam(6, $_POST['idUsuario']);
            break;
    
        case 'eliminar':
            $pdoStatement=$pdo->prepare("DELETE FROM usuario WHERE id_user=?;");
            $pdoStatement->bindParam(1, $_POST['idUsuario']);
            break;
        
        default:
            # code...
            break;
    }

    $pdoStatement->execute();

    header("Location: admin.php");
    exit();

} catch (PDOException $e) {
    $errorInfo = $e->errorInfo;
    $mensaje = "Error: " . $e->getMessage();
    
    $_SESSION['error'] = $mensaje;
    header("Location: admin.php");
    exit();

}
