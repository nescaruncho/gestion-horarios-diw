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
            break;
    
        case 'eliminar':
            $pdoStatement=$pdo->prepare("DELETE FROM sesion WHERE id_sesion=?;");
            $pdoStatement->bindParam(1, $_POST['idSesion']);
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
