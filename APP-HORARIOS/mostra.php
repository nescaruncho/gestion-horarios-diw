<?php
session_start();

if (!empty($_SESSION['error'])) {
    echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['error'] = "Error: Inicie sesión para acceder a esta página";
    header("Location: login.php");
    exit();
}

if ($_SESSION['usuario_rol'] == 'administrador') {
    header("Location: admin.php");
    exit();
}

require_once "conexion.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Mostra</title>
</head>
<body>
    <nav class="navbar">
        <?php
        $pdoStatement = $pdo->prepare("SELECT name, lastname FROM usuario WHERE id_user = ?");
        $pdoStatement->bindParam(1, $_SESSION['usuario_id']);
        $pdoStatement->execute();
        $usuario = $pdoStatement->fetch();
        echo "<p>" . $usuario['name'] . " " . $usuario['lastname'] . " (" . $_SESSION['usuario_rol'] . ")" . "</p>";
        ?>
        <a href="logout.php">Logout</a>
    </nav>
    <h2>Ciclos</h2>
    <form method="post">
        <button type="submit" formaction="matriculaCiclo.php">Matricularse</button>
    </form>
    <table class="product-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $pdoStatement = $pdo->prepare("SELECT * FROM ciclo");
            $pdoStatement->execute();
            $filas = $pdoStatement->fetchAll();

            foreach ($filas as $ciclo) {
                echo "<tr>";
                echo "<td>".$ciclo['codigo']."</td>";
                echo "<td>".$ciclo['name']."</td>";
                echo "<td>";
                echo "<form method='post'>";
                // Si el alumno está matriculado en algún módulo del ciclo
                $pdoStatement3 = $pdo->prepare("
                    SELECT DISTINCT um.id_user 
                    FROM user_modulo um
                    JOIN ciclo_tiene_modulo ctm ON um.id_modulo = ctm.id_modulo
                    WHERE um.id_user = ? AND ctm.id_ciclo = ?");
                $pdoStatement3->bindParam(1, $_SESSION['usuario_id']);
                $pdoStatement3->bindParam(2, $ciclo['id_ciclo']);
                $pdoStatement3->execute();
                $matriculado = $pdoStatement3->fetch();
                
                if ($matriculado) {
                    echo "<input type='hidden' name='ciclo_id' value='".$ciclo['id_ciclo']."'>";
                    echo "<button type='submit' formaction='desmatriculaCiclo.php'>Desmatricularse</button>";
                }
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
    // Consulta para obtener los módulos del usuario
    $pdoStatement = $pdo->prepare("
        SELECT DISTINCT m.*, c.name AS ciclo_name, p.name AS profesor_name, p.lastname AS profesor_lastname
        FROM modulo m
        JOIN ciclo_tiene_modulo ctm ON m.id_modulo = ctm.id_modulo
        JOIN ciclo c ON ctm.id_ciclo = c.id_ciclo
        JOIN user_modulo um ON m.id_modulo = um.id_modulo
        LEFT JOIN profesor p ON ctm.id_profesor = p.id_profesor
        WHERE um.id_user = ?
        ORDER BY c.name, m.curso, m.name
    ");
    
    $pdoStatement->bindParam(1, $_SESSION['usuario_id']);
    $pdoStatement->execute();
    $modulos = $pdoStatement->fetchAll();

    if (!empty($modulos)) {
        echo "<h2>Módulos</h2>";
        echo '<table class="product-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Ciclo</th>
                    <th>Curso</th>
                    <th>Horas</th>
                    <th>Profesor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($modulos as $modulo) {
            echo "<tr>";
            echo "<td>".$modulo['name']."</td>";
            echo "<td>".$modulo['ciclo_name']."</td>";
            echo "<td>".$modulo['curso']."</td>";
            echo "<td>".$modulo['horas_totales']."</td>";
            echo "<td>".($modulo['profesor_name'] ? $modulo['profesor_name'].' '.$modulo['profesor_lastname'] : 'Sin asignar')."</td>";
            echo "<td>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='modulo_id' value='".$modulo['id_modulo']."'>";
            echo "<button type='submit' formaction='desmatriculaModulo.php'>Desmatricularse</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }

        echo '</tbody></table>';
    }
    ?>
</body>
</html>