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

if (!empty($_SESSION['error'])) {
    echo "<div class='mnsjError'>" . htmlspecialchars($_SESSION['error']) . "</div>";
    unset($_SESSION['error']);
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
    <link rel="stylesheet" href="css/formularios.css">
    <link rel="stylesheet" href="css/formulariosMovil.css">
    <title>Matrícula</title>
</head>
<body>
    <nav class="navbar">
        <?php
        try {
            $pdoStatement = $pdo->prepare("SELECT name, lastname FROM usuario WHERE id_user = ?");
            $pdoStatement->bindParam(1, $_SESSION['usuario_id']);
            $pdoStatement->execute();
            $usuario = $pdoStatement->fetch();
            echo "<p>" . ucfirst($usuario['name']) . " " . ucwords($usuario['lastname']) . "</p>";
        } catch (PDOException $e) {
            echo "<p>Error al cargar los ciclos</p>";
            error_log("Error en la consulta: " . $e->getMessage());
        }
        ?>
        <a href="logout.php" class="logout">Logout</a>
    </nav>
    <div class="form-container">
    <?php
        try {
            $pdoStatement2 = $pdo->prepare("SELECT id_ciclo FROM usuario_ciclo WHERE id_user = ?");
            $pdoStatement2->bindParam(1, $_SESSION['usuario_id']);
            $pdoStatement2->execute();

            $ciclosUsuario = $pdoStatement2->fetchAll(PDO::FETCH_ASSOC);
            if (count($ciclosUsuario) == 0) {
                
                echo "<h2>Selecciona un ciclo:</h2>";
            }
        } catch (PDOException $e) {
            echo "<p>Error al cargar los ciclos</p>";
            error_log("Error en la consulta: " . $e->getMessage());
        }
    ?>
    
        <form action="validaMatriculaCiclo.php" method="post" id="matriculaForm" style="grid-template-columns: repeat(1, 1fr);">
                    <?php
                    if (count($ciclosUsuario) == 0) {
                        try {
                            $pdoStatement = $pdo->prepare("SELECT id_ciclo, codigo, name FROM ciclo ORDER BY name");
                            $pdoStatement->execute();
                            $ciclos = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
                            
                            
                            foreach ($ciclos as $ciclo) {   
                                echo "<fieldset class='radioFieldeset'>";                             
                                echo "<input type='radio' name='ciclo' 
                                        id='" . htmlspecialchars($ciclo['id_ciclo']) . "'
                                        value='" . htmlspecialchars($ciclo['id_ciclo']) . "'>";
                                echo "<label for='" . htmlspecialchars($ciclo['id_ciclo']) . "'>
                                        " . htmlspecialchars($ciclo['codigo'] . " - " . $ciclo['name']) ."</label><br>";
                                echo "</fieldset>";
                            }
                            
                            echo "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>";
                            
                            echo "<div class='botones' style='left:42%'>";
                            echo "<input type='submit' value='Matricular'>";
                            echo "<a href='mostra.php' class='volver'>Volver</a>";
                            echo "</div>"; 
                            
                            
                        } catch (PDOException $e) {
                            echo "<p>Error al cargar los ciclos</p>";
                            error_log("Error en la consulta: " . $e->getMessage());
                        }
    
                    } else {
                        echo "<h2>Ya estás matriculado en un ciclo</h2>";
                        echo "<div class='botones' style='left:45%'>";
                        echo "<a href='mostra.php' class='volver'>Volver</a>";
                        echo "</div>"; 
                    }

                    ?>

        </form>
    </div>
</body>
</html>