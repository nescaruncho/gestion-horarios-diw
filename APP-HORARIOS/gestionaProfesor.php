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
            $pdoStatement=$pdo->prepare("UPDATE profesor SET name=?, lastname=?, email=? WHERE id_profesor=?");
            $pdoStatement->bindParam(1, $_POST['nombreProfesor']);
            $pdoStatement->bindParam(2, $_POST['apellidoProfesor']);
            $pdoStatement->bindParam(3, $_POST['emailProfesor']);
            $pdoStatement->bindParam(4, $_POST['idProfesor']);
            break;
    
        case 'eliminar':
            $pdoStatement=$pdo->prepare("DELETE FROM profesor WHERE id_profesor=?;");
            $pdoStatement->bindParam(1, $_POST['idProfesor']);
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
