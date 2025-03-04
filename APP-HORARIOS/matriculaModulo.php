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

if (!empty($_SESSION['error'])) {
    echo "<p style='color:red;'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once "conexion.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matrícula en módulos</title>
</head>
<body>
    <div class="form-container">
        <form action="validaMatriculaModulo.php" method="post" id="matriculaForm">
            <div class="form-group">
                <h2>Módulos disponibles</h2>
                <?php

                try {
                    $pdoStatement = $pdo->prepare("SELECT c.id_ciclo, c.name as ciclo_name
                                                    FROM ciclo c
                                                    JOIN usuario_ciclo uc ON c.id_ciclo = uc.id_ciclo
                                                    WHERE uc.id_user = ?");
                    $pdoStatement->bindParam(1, $_SESSION['usuario_id']);
                    $pdoStatement->execute();
                    $cicloUsuario = $pdoStatement->fetch(PDO::FETCH_ASSOC);

                    if ($cicloUsuario) {
                        echo "<h3>Ciclo: " . htmlspecialchars($cicloUsuario['ciclo_name']) . "</h3>";

                        $pdoStatement = $pdo->prepare("
                                        SELECT m.id_modulo, m.name, m.curso, m.horas_totales
                                        FROM modulo m
                                        INNER JOIN ciclo_tiene_modulo ctm ON m.id_modulo = ctm.id_modulo
                                        WHERE ctm.id_ciclo = ?
                                        AND m.id_modulo NOT IN (
                                            SELECT id_modulo
                                            FROM user_modulo
                                            WHERE id_user = ?
                        )");                        
                        $pdoStatement->bindParam(1, $cicloUsuario['id_ciclo']);
                        $pdoStatement->bindParam(2, $_SESSION['usuario_id']);
                        $pdoStatement->execute();
                        $modulos = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

                        if (count($modulos) > 0) {
                            foreach ($modulos as $modulo) {
                                echo "<div class='modulo-item'>";
                                echo "<input type='checkbox' name='modulos[]' value='" . 
                                     htmlspecialchars($modulo['id_modulo']) . "' id='mod_" . 
                                     htmlspecialchars($modulo['id_modulo']) . "'>";
                                echo "<label for='mod_" . htmlspecialchars($modulo['id_modulo']) . "'>" .
                                     htmlspecialchars($modulo['name']) . " - Curso: " . 
                                     htmlspecialchars($modulo['curso']) . " (" . 
                                     htmlspecialchars($modulo['horas_totales']) . " horas)</label><br>";
                                echo "</div>";                            
                            }

                            echo "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>";
                            echo "<input type='submit' value='Matricular en módulos seleccionados'>";
                        } else {
                            echo "<p>No hay módulos disponibles para matricular</p>";
                        }
                    } else {
                        echo "<p>No estás matriculado en ningún ciclo</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p style='color:red;'>Error: No se pudieron cargar los módulos</p>";
                    error_log("Error en la consulta: " . $e->getMessage());
                    error_log("SQL State: " . $e->errorInfo[0]);
                    error_log("Error Code: " . $e->errorInfo[1]);
                    error_log("Error Message: " . $e->errorInfo[2]);
                }
                ?>
    </div>
</body>
</html>