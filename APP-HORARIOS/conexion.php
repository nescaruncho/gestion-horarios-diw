<?php

$servidor = "localhost";
$port = "3306";
$usuario = "horarios";
$passwd = "abc123.";
$base = "gestion_ciclos";

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$base;charset=utf8mb4", $usuario, $passwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {
    echo "Erro ao conectar co servidor MySQL: " . $e->getMessage();
}

?>