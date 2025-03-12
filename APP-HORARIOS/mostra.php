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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once "conexion.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Panel de alumno</title>
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
    <form action="verHorario.php" method="post">
        <button type="submit">Ver horario</button>
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
                $pdoStatement2 = $pdo->prepare("
                    SELECT id_user 
                    FROM usuario_ciclo
                    WHERE id_user = ? AND id_ciclo = ?");
                $pdoStatement2->bindParam(1, $_SESSION['usuario_id']);
                $pdoStatement2->bindParam(2, $ciclo['id_ciclo']);
                $pdoStatement2->execute();
                $matriculado = $pdoStatement2->fetch();
                
                if ($matriculado) {
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='ciclo' value='".$ciclo['id_ciclo']."'>";
                    echo "<input type='hidden' name='csrf_token' value='".$_SESSION['csrf_token']."'>";
                    echo "<button type='submit' formaction='desmatriculaCiclo.php'>Desmatricularse</button>";
                    echo "</form>";
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
        // Obtener el ciclo en el que está matriculado el usuario
        $pdoStatement = $pdo->prepare("
            SELECT c.* 
            FROM ciclo c
            INNER JOIN usuario_ciclo uc ON c.id_ciclo = uc.id_ciclo
            WHERE uc.id_user = ?
        ");
        $pdoStatement->bindParam(1, $_SESSION['usuario_id']);
        $pdoStatement->execute();
        $cicloMatriculado = $pdoStatement->fetch();

        if ($cicloMatriculado) {
            echo "<h3>Módulos de " . htmlspecialchars($cicloMatriculado['name']) . "</h3>";
            
            // Botón para matricularse en módulos
            echo "<form method='post'>";
            echo "<button type='submit' formaction='matriculaModulo.php'>Matricularse en módulos</button>";
            echo "</form>";

            // Tabla de módulos
            echo "<table class='product-table'>";
            echo "<thead><tr><th>Nombre</th><th>Curso</th><th>Horas totales</th><th></th></tr></thead>";
            echo "<tbody>";

            // Obtener módulos del ciclo
            $pdoStatement = $pdo->prepare("
                SELECT m.* 
                FROM modulo m
                INNER JOIN ciclo_tiene_modulo ctm ON m.id_modulo = ctm.id_modulo
                WHERE ctm.id_ciclo = ?
            ");
            $pdoStatement->bindParam(1, $cicloMatriculado['id_ciclo']);
            $pdoStatement->execute();
            $modulos = $pdoStatement->fetchAll();

            foreach ($modulos as $modulo) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($modulo['name']) . "</td>";
                echo "<td>" . htmlspecialchars($modulo['curso']) . "</td>";
                echo "<td>" . htmlspecialchars($modulo['horas_totales']) . "</td>";
                echo "<td>";
                
                // Verificar si el usuario está matriculado en este módulo
                $pdoStatement2 = $pdo->prepare("
                    SELECT id_user_modulo 
                    FROM user_modulo 
                    WHERE id_user = ? AND id_modulo = ?
                ");
                $pdoStatement2->bindParam(1, $_SESSION['usuario_id']);
                $pdoStatement2->bindParam(2, $modulo['id_modulo']);
                $pdoStatement2->execute();
                $matriculadoModulo = $pdoStatement2->fetch();

                if ($matriculadoModulo) {
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='modulo' value='" . $modulo['id_modulo'] . "'>";
                    echo "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>";
                    echo "<button type='submit' formaction='desmatriculaModulo.php'>Desmatricularse</button>";
                    echo "</form>";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        }
        ?>
</body>
</html>