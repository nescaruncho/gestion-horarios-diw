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
            $pdoStatement=$pdo->prepare("UPDATE ciclo SET codigo=?, name=? WHERE id_ciclo=?");
            $pdoStatement->bindParam(1, $_POST['codigoCiclo']);
            $pdoStatement->bindParam(2, $_POST["nombreCiclo"]);
            $pdoStatement->bindParam(3, $_POST['idCiclo']);
            break;
    
        case 'eliminar':
            $pdoStatement=$pdo->prepare("DELETE FROM ciclo WHERE id_ciclo=?;");
            $pdoStatement->bindParam(1, $_POST['idCiclo']);
            break;
        
        default:
            # code...
            break;
    }

    $pdoStatement->execute();

    header("Location: admin.php");
    exit();

} catch (Exception $e) {
    $errorInfo = $e->errorInfo;
    $mensaje = "Error: " . $e->getMessage();
    
    $_SESSION['error'] = $mensaje;
    header("Location: admin.php");
    exit();

}
