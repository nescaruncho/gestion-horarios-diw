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
            $pdoStatement=$pdo->prepare("UPDATE modulo SET name=?, curso=?, horas_totales=? WHERE id_modulo=?");
            $pdoStatement->bindParam(1, $_POST['nombreModulo']);
            $pdoStatement->bindParam(2, $_POST["cursoModulo"]);
            $pdoStatement->bindParam(3, $_POST["horasModulo"]);
            $pdoStatement->bindParam(4, $_POST['idModulo']);
            break;
    
        case 'eliminar':
            $pdoStatement=$pdo->prepare("DELETE FROM modulo WHERE id_modulo=?;");
            $pdoStatement->bindParam(1, $_POST['idModulo']);
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
