<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

require_once "conexion.php";

try {
    $pdoStatement=$pdo->prepare("INSERT INTO sesion (hora_inicio, hora_fin, dia_semana, aula, id_ciclo_modulo) VALUES (?,?,?,?,?)");
    $pdoStatement->bindParam(1, $_POST["horaInicio"]);
    $pdoStatement->bindParam(2, $_POST["horaFin"]);
    $pdoStatement->bindParam(3, $_POST["diaSemana"]);
    $pdoStatement->bindParam(4, $_POST["aula"]);
    $pdoStatement->bindParam(5, $_POST["idCicloModulo"]);
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
