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

if (!isset($_POST['ciclo']) || empty($_POST['ciclo'])) {
    $_SESSION['error'] = "Error: No hay ciclo seleccionado.";
    header("Location: mostra.php");
    exit();
}

require_once "conexion.php";

try {
    $pdoStatement = $pdo->prepare("DELETE FROM usuario_ciclo WHERE id_user = ? and id_ciclo = ?");
    $idUsuario = $_SESSION["usuario_id"];
    $idCiclo = $_POST["ciclo"];

    $pdoStatement->bindParam(1, $idUsuario);
    $pdoStatement->bindParam(2, $idCiclo);
    $pdoStatement->execute();

    if ($pdoStatement->rowCount() > 0) {
        $_SESSION['mensaje'] = "Desmatriculado correctamente.";
        header("Location: mostra.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: No se ha podido desmatricular.";
        header("Location: mostra.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: mostra.php");
    exit();
}