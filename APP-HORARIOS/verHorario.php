<?php
session_start();

require_once "conexion.php";

$idUsuario = $_SESSION['usuario_id'];

$pdoStatement = $pdo->prepare("SELECT id_ciclo FROM USUARIO_CICLO WHERE id_user = ?");
$pdoStatement->execute([$idUsuario]);
$idCiclo = $pdoStatement->fetchColumn();

$diasSemana = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"];
$horas = ["08:45", "09:35", "10:25", "11:15", "12:05", "12:55", "13:45"];

echo "<table border='1' style='border-collapse: collapse; text-align: center;'>";
echo "<thead>";
echo "<tr>";
echo "<th>Horario</th>"; 
foreach ($diasSemana as $dia) {
    echo "<th>$dia</th>";
}
echo "</tr>";
echo "</thead>";
echo "<tbody>";

foreach ($horas as $hora) {
    echo "<tr>";
    echo "<td><b>$hora</b></td>";

    foreach ($diasSemana as $dia) {
        $pdoStatement = $pdo->prepare("
            SELECT M.name 
            FROM SESION S
            JOIN CICLO_TIENE_MODULO CTM ON S.id_ciclo_modulo = CTM.id_ciclo_modulo
            JOIN MODULO M ON CTM.id_modulo = M.id_modulo
            WHERE CTM.id_ciclo = ? AND S.dia_semana = ? AND S.hora_inicio = ?
        ");
        $pdoStatement->execute([$idCiclo, $dia, $hora]);
        $modulo = $pdoStatement->fetchColumn(); 

        echo "<td>".($modulo ?: "")."</td>";
    }

    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
?>
