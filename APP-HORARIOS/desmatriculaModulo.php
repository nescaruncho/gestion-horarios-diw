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

if (!isset($_POST['modulo']) || empty($_POST['modulo'])) {
    $_SESSION['error'] = "Error: No hay módulo seleccionado.";
    header("Location: mostra.php");
    exit();
}

require_once "conexion.php";

try {
    $pdoStatement = $pdo->prepare("DELETE FROM user_modulo WHERE id_user = ? and id_modulo = ?");
    $idUsuario = $_SESSION["usuario_id"];
    $idModulo = $_POST["modulo"];

    $pdoStatement->bindParam(1, $idUsuario);
    $pdoStatement->bindParam(2, $idModulo);
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