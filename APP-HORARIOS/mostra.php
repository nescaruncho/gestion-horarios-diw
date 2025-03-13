<?php
session_start();

if (!empty($_SESSION['error'])) {
    echo "<div class='mnsjError'>" . $_SESSION['error'] . "</div>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/adminMovil.css">
    <title>Panel de alumno</title>
</head>
<body>
    <nav class="navbar">
        <?php
        $pdoStatement = $pdo->prepare("SELECT name, lastname FROM usuario WHERE id_user = ?");
        $pdoStatement->bindParam(1, $_SESSION['usuario_id']);
        $pdoStatement->execute();
        $usuario = $pdoStatement->fetch();
        echo "<p>" . ucfirst($usuario['name']) . " " . ucwords($usuario['lastname']) . "</p>";
        ?>
        <a href="logout.php" class="logout">Logout</a>
    </nav>

    <div class="sidebar" id="sidebar">
        <i class="fas fa-bars toggle-btn" id="toggle-btn"></i>
        <ul class="menu opciones-container">
            <li>
                <a href="#" class="menu-item opcion" data-tabla="ciclos">
                    <span class="icon"><i class="fas fa-book"></i></span>
                    <span class="text">Ciclos</span>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item opcion" data-tabla="modulos">
                    <span class="icon"><i class="fas fa-chart-bar"></i></span>
                    <span class="text">Modulos</span>
                </a>
            </li>
        </ul>
    </div>


    <div id="contenedor-tabla" class="tabla-container">
    </div>
    
    <template id="template-ciclos">
        <div id="ciclos" class="divTabla">
            <h2>Ciclos</h2>
            <form method="post">
                <button type="submit" formaction="matriculaCiclo.php" class="crear">Matricularse</button>
            </form>
            <form action="verHorario.php" method="post">
                <button type="submit" class="vincular">Ver horario</button>
            </form>
            <div class="product-table">
                <table>
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
                            echo "<button type='submit' class='eliminar' formaction='desmatriculaCiclo.php'>Desmatricularse</button>";
                            echo "</form>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
                </table>
            </div>            
        </div>
    </template>

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
            echo "<template id='template-modulos'>";
            echo "<div id='modulos' class='divTabla'>";
            echo "<h2>Módulos de " . htmlspecialchars($cicloMatriculado['name']) . "</h2>";
            
            // Botón para matricularse en módulos
            echo "<form method='post'>";
            echo "<button type='submit' class='crear' formaction='matriculaModulo.php'>Matricularse en módulos</button>";
            echo "</form>";

            // Tabla de módulos
            echo "<div class='product-table'>";
            echo "<table>";
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
                    echo "<button type='submit' class='eliminar' formaction='desmatriculaModulo.php'>Desmatricularse</button>";
                    echo "</form>";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div></div></template>";
        }
        ?>
    <script src="js/tablas.js"></script>
    <script src="js/barraLateral.js"></script>
</body>
</html>