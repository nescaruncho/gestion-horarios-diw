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

require_once "conexion.php";

try {
    $pdo->beginTransaction();
    $pdoStatement=$pdo->prepare("INSERT INTO user_modulo (id_user, id_modulo) VALUES (?,?)");
    foreach ($_POST['modulos'] as $idModulo) {
        $pdoStatement->bindParam(1, $_SESSION["usuario_id"]);
        $pdoStatement->bindParam(2, $idModulo);
        $pdoStatement->execute();
    }

    $pdo->commit();
    unset($_SESSION['csrf_token']);

    $_SESSION['mensaje'] = "Módulos matriculados correctamente.";
    header("Location: mostra.php");
    exit();
} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: mostra.php");
    exit();
}