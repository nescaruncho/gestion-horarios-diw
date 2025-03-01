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
                $pdoStatement2 = $pdo->prepare("SELECT * FROM ciclo WHERE id_ciclo=?");
                $pdoStatement2->bindParam(1, $ciclo['id_ciclo']);
                $pdoStatement2->execute();
                $filas2 = $pdoStatement2->fetchAll();

                echo "<tr>";
                echo "<td>".$ciclo['codigo']."</td>";
                echo "<td>".$ciclo['name']."</td>";
                echo "<td>";
                // Si el alumno está matriculado en el ciclo, mostrar el botón de desmatricularse
                $pdoStatement3 = $pdo->prepare("SELECT * FROM usuario_ciclo WHERE id_user = ? AND id_ciclo = ?");
                $pdoStatement3->bindParam(1, $_SESSION['usuario_id']);
                $pdoStatement3->bindParam(2, $ciclo['id_ciclo']);
                $pdoStatement3->execute();
                $matriculado = $pdoStatement3->fetch();
                if ($matriculado) {
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
    $pdoStatement = $pdo->prepare("SELECT * FROM usuario_ciclo WHERE id_user = ?");
    $pdoStatement->bindParam(1, $_SESSION['usuario_id']);
    $pdoStatement->execute();
    $matriculas = $pdoStatement->fetchAll();

    if (!empty($matriculas)) {
        echo "<h2>Módulos</h2>";
        echo "<form method='post'>";
        echo "<button type='submit' formaction='matriculaModulo.php'>Matricularse</button>";
        echo "</form>";
        echo '<table class="product-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Ciclo</th>
                    <th>Curso</th>
                    <th>Horas</th>
                    <th>Profesor</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($matriculas as $matricula) {
            $pdoStatement2 = $pdo->prepare("SELECT m.name AS modulo_name, c.name AS ciclo_name, p.name AS profesor_name, p.lastname AS profesor_lastname, m.curso, m.horas_totales 
                                            FROM modulo m 
                                            JOIN ciclo_tiene_modulo ctm ON m.id_modulo = ctm.id_modulo 
                                            JOIN ciclo c ON ctm.id_ciclo = c.id_ciclo 
                                            LEFT JOIN profesor p ON ctm.id_profesor = p.id_profesor
                                            WHERE c.id_ciclo = ?");
            $pdoStatement2->bindParam(1, $matricula['id_ciclo']);
            $pdoStatement2->execute();
            $modulos = $pdoStatement2->fetchAll();

            foreach ($modulos as $modulo) {
                echo "<tr>";
                echo "<td>".$modulo['modulo_name']."</td>";
                echo "<td>".$modulo['ciclo_name']."</td>";
                echo "<td>".$modulo['curso']."</td>";
                echo "<td>".$modulo['horas_totales']."</td>";
                echo "<td>".$modulo['profesor_name'].' '.$modulo['profesor_lastname']."</td>";
                echo "</tr>";
            }
        }

        echo '</tbody>
        </table>';
    }
    ?>
</body>
</html>