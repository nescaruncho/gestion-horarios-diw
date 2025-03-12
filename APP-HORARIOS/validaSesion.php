<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

require_once "conexion.php";

try {
    $pdoStatement = $pdo->prepare("SELECT COUNT(*) FROM SESION S
                                    JOIN CICLO_TIENE_MODULO CTM ON S.id_ciclo_modulo = CTM.id_ciclo_modulo
                                    WHERE CTM.id_ciclo = ? AND S.dia_semana = ? AND S.hora_inicio = ?
                                ");
    $pdoStatement->execute([$_POST["idCiclo"], $_POST["diaSemana"], $_POST["horaInicio"]]);
    $existe = $pdoStatement->fetchColumn();

} catch (PDOException $e) {
    $errorInfo = $e->errorInfo;
    
    $mensaje = "Error: " . $e->getMessage();
    
    $_SESSION['error'] = $mensaje;
    header("Location: admin.php");
    exit();
}

if ($existe > 0) {
    $mensaje = "Error: Ya existe una sesión con esa hora de inicio en ese día para este ciclo.";
    $_SESSION['error'] = $mensaje;
    header("Location: admin.php");
    exit();
}

try {
    $pdoStatement=$pdo->prepare("INSERT INTO sesion (hora_inicio, hora_fin, dia_semana, aula, id_ciclo_modulo) VALUES (?,?,?,?,?)");
    
    $horaInicio = $_POST["horaInicio"];
    $horaInicioObj = DateTime::createFromFormat('H:i', $horaInicio);
    $horaInicioObj->modify('+50 minutes');
    $horaFin = $horaInicioObj->format('H:i');
    
    $pdoStatement->bindParam(1, $horaInicio);
    $pdoStatement->bindParam(2, $horaFin);
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
