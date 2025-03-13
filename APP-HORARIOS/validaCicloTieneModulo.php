<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: rexistro.php");
    exit();
}

require_once "conexion.php";

try {
    $pdoStatement=$pdo->prepare("INSERT INTO ciclo_tiene_modulo (id_ciclo, id_modulo, id_profesor) VALUES (?,?,?)");
    $ciclo=$_POST["idCiclo"];
    $modulo=$_POST["idModulo"];
    $profesor=$_POST["idProfesor"];
    
    $pdoStatement->bindParam(1, $ciclo);
    $pdoStatement->bindParam(2, $modulo);
    $pdoStatement->bindParam(3, $profesor);
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
