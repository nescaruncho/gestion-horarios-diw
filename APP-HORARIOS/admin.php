<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['usuario_rol'] != 'administrador') {
    $_SESSION['error'] = "Error: No puede acceder a esta página";
    header("Location: login.php");
    exit();
}

if (!empty($_SESSION['error'])) {
    echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}

require_once "conexion.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Administración</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . $_SESSION['usuario_nome']." (".$_SESSION['usuario_rol'].")" . "</p>"; ?>
        <a href="logout.php">Logout</a>
    </nav>
    <div>
        <h2>Alumnos</h2>
        <form action="creaUsuario.php" method="post">
            <button type="submit">Crear nuevo</button>
        </form>
        <table class="product-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>DNI</th>
                    <th>User</th>
                    <th>Administrar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $pdoStatement = $pdo->prepare("SELECT * FROM usuario WHERE rol='alumno'");
                $pdoStatement->execute();
                $filas = $pdoStatement->fetchAll();

                foreach ($filas as $usuario) {
                    echo "<tr>";
                    echo "<td>".$usuario['name']."</td>";
                    echo "<td>".$usuario['email']."</td>";
                    echo "<td>".$usuario['dni']."</td>";
                    echo "<td>".$usuario['login']."</td>";
                    echo "<td>";
                        echo "<form action='editaUsuario.php' method='post'>";
                        echo "<input type='hidden' name='idUsuario' value='".$usuario['id_user']."'>";
                        echo "<input type='hidden' name='nombreUsuario' value='".$usuario['name']."'>";
                        echo "<input type='hidden' name='apellidoUsuario' value='".$usuario['lastname']."'>";
                        echo "<input type='hidden' name='emailUsuario' value='".$usuario['email']."'>";
                        echo "<input type='hidden' name='dniUsuario' value='".$usuario['dni']."'>";
                        echo "<input type='hidden' name='loginUsuario' value='".$usuario['login']."'>";
                        echo "<button type='submit'>Editar</button>";
                        echo "</form>";
                        echo "<form action='gestionaUsuario.php' method='post'>";
                        echo "<input type='hidden' name='idUsuario' value='".$usuario['id_user']."'>";
                        echo "<button type='submit' name='boton' value='eliminar'>Eliminar</button>";
                        echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <br><hr>

    <div>
        <h2>Ciclos formativos</h2>
        <form action="creaCiclo.php" method="post">
            <button type="submit">Crear nuevo</button>
        </form>
        <table class="product-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Administrar</th>
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
                        echo "<form action='editaCiclo.php' method='post'>";
                        echo "<input type='hidden' name='idCiclo' value='".$ciclo['id_ciclo']."'>";
                        echo "<input type='hidden' name='codigoCiclo' value='".$ciclo['codigo']."'>";
                        echo "<input type='hidden' name='nombreCiclo' value='".$ciclo['name']."'>";
                        echo "<button type='submit'>Editar</button>";
                        echo "</form>";
                        echo "<form action='gestionaCiclo.php' method='post'>";
                        echo "<input type='hidden' name='idCiclo' value='".$ciclo['id_ciclo']."'>";
                        echo "<button type='submit' name='boton' value='eliminar'>Eliminar</button>";
                        echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <br><hr>

    <div>
        <h2>Módulos</h2>
        <form action="creaModulo.php" method="post">
            <button type="submit">Crear nuevo</button>
        </form>
        <table class="product-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Curso</th>
                    <th>Horas totales</th>
                    <th>Ciclos vinculados</th>
                    <th>Administrar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $pdoStatement = $pdo->prepare("SELECT * FROM modulo");
                $pdoStatement->execute();
                $filas = $pdoStatement->fetchAll();

                foreach ($filas as $modulo) {
                    echo "<tr>";
                    echo "<td>".$modulo['name']."</td>";
                    echo "<td>".$modulo['curso']."</td>";
                    echo "<td>".$modulo['horas_totales']."</td>";

                    $pdoStatement2 = $pdo->prepare("SELECT C.name AS ciclo_name,
                                                           P.name AS profesor_name,
                                                           P.lastname AS profesor_lastname
                                                           FROM CICLO_TIENE_MODULO CTM
                                                           JOIN CICLO C ON CTM.id_ciclo = C.id_ciclo
                                                           LEFT JOIN PROFESOR P ON CTM.id_profesor = P.id_profesor
                                                           WHERE CTM.id_modulo = ?;
                                                           ");
                    $pdoStatement2->bindParam(1, $modulo['id_modulo']);
                    $pdoStatement2->execute();
                    $filas2 = $pdoStatement2->fetchAll();
                    echo "<td>";
                        foreach ($filas2 as $ciclo_asociado) {
                            echo "<p>".$ciclo_asociado["ciclo_name"]." (Profesor: ".$ciclo_asociado["profesor_name"]." ".$ciclo_asociado["profesor_lastname"].") </p>";
                        }
                        echo "<form action='gestionaCicloTieneModulo.php' method='post'>";
                        echo "<input type='hidden' name='idModulo' value='".$modulo['id_modulo']."'>";
                        echo "<button type='submit'>Vincular otro</button>";
                        echo "</form>";
                    echo "</td>";

                    echo "<td>";
                        echo "<form action='editaModulo.php' method='post'>";
                        echo "<input type='hidden' name='idModulo' value='".$modulo['id_modulo']."'>";
                        echo "<input type='hidden' name='nombreModulo' value='".$modulo['name']."'>";
                        echo "<input type='hidden' name='cursoModulo' value='".$modulo['curso']."'>";
                        echo "<input type='hidden' name='horasModulo' value='".$modulo['horas_totales']."'>";
                        echo "<button type='submit'>Editar</button>";
                        echo "</form>";
                        echo "<form action='gestionaModulo.php' method='post'>";
                        echo "<input type='hidden' name='idModulo' value='".$modulo['id_modulo']."'>";
                        echo "<button type='submit' name='boton' value='eliminar'>Eliminar</button>";
                        echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
</body>
</html>