<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php");
    exit();
}
require_once "conexion.php";

try {
    $pdoStatement=$pdo->prepare("SELECT C.id_ciclo AS ciclo_id, C.name AS ciclo_name
                                        FROM CICLO C
                                        WHERE NOT EXISTS (
                                            SELECT 1
                                            FROM CICLO_TIENE_MODULO CTM
                                            WHERE CTM.id_ciclo = C.id_ciclo AND CTM.id_modulo = ? );
                                        ");
    $pdoStatement->bindParam(1, $_POST['idModulo']);
    $pdoStatement->execute();
    $filas = $pdoStatement->fetchAll();

    $pdoStatement2=$pdo->prepare("SELECT * FROM profesor");
    $pdoStatement2->execute();
    $filas2 = $pdoStatement2->fetchAll();
    
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/formularios.css">
    <link rel="stylesheet" href="css/formulariosMovil.css">
        <title>Administración</title>
    </head>
    <body>
        <nav class="navbar">
            <?php echo "<p>" . ucfirst($_SESSION['usuario_nome']) . " (" . $_SESSION['usuario_rol'] . ")" . "</p>"; ?>        
            <a href="logout.php" class="logout">Logout</a>
        </nav> 
        <div class="form-container">
            <form action="validaCicloTieneModulo.php" method="post">
                <fieldset>
                    <label for="idCiclo">Ciclo</label>
                    <select name="idCiclo">
                        <?php
                        foreach ($filas as $CTM) {
                            echo "<option value='".$CTM['ciclo_id']."'>".$CTM['ciclo_name']."</option>";
                        }
                        ?>
                    </select>
                </fieldset>
                
                <fieldset>
                    <label for="idProfesor">Profesor</label>
                    <select name="idProfesor">
                        <?php
                        foreach ($filas2 as $profesor) {
                            echo "<option value='".$profesor['id_profesor']."'>".$profesor['name']."</option>";
                        }
                        ?>
                    </select>    
                </fieldset>
                
                <input type='hidden' name='idModulo' value='<?=$_POST['idModulo']?>' >

                <button type="submit">Enviar</button>
            </form>
        </div>

    </body>

    <?php


} catch (PDOException $e) {
    $errorInfo = $e->errorInfo;
    $mensaje = "Error: " . $e->getMessage();
    
    $_SESSION['error'] = $mensaje;
    header("Location: admin.php");
    exit();

}
