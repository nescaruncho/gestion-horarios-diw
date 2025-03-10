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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Matrícula</title>
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

    <div class="form-container">
        <form action="validaMatriculaCiclo.php" method="post" id="matriculaForm">
            <div class="form-group">
                <label for="ciclo">Selecciona un ciclo:</label>


                    <?php
                    require_once "conexion.php";

                    $pdoStatement2 = $pdo->prepare("SELECT id_ciclo FROM usuario_ciclo WHERE id_user = ?");
                    $pdoStatement2->bindParam(1, $_SESSION['usuario_id']);
                    $pdoStatement2->execute();

                    $ciclosUsuario = $pdoStatement2->fetchAll(PDO::FETCH_ASSOC);

                    if (count($ciclosUsuario) == 0) {
                        try {
                            $pdoStatement = $pdo->prepare("SELECT id_ciclo, codigo, name FROM ciclo ORDER BY name");
                            $pdoStatement->execute();
                            $ciclos = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
    
                            foreach ($ciclos as $ciclo) {
                                echo "<input type='radio' name='ciclo' value='" . htmlspecialchars($ciclo['id_ciclo']) . "'>" .
                                     htmlspecialchars($ciclo['codigo'] . " - " . $ciclo['name']) . 
                                     "<br>";
                            }

                            echo "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>";
                            echo "<input type='submit' value='Matricular'>";
                            
                        } catch (PDOException $e) {
                            echo "<p>Error al cargar los ciclos</p>";
                            error_log("Error en la consulta: " . $e->getMessage());
                        }
    
                    } else {
                        echo "<h3>Ya estás matriculado en un ciclo</h3>";
                        echo "<p>Si deseas cambiar de ciclo, ponte en contacto con el administrador</p>";
                        echo "<a href='mostra.php'>Volver</a>";
                    }

                    ?>


            </div>
        </form>
    </div>
</body>
</html>