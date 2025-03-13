<?php
session_start();

if (!empty($_SESSION['error'])) {
    echo "<div class='mnsjError'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/formularios.css">
    <link rel="stylesheet" href="css/formulariosMovil.css">
    <title>Edicion del ciclo <?=$_POST['codigoCiclo']?> <?=$_POST['nombreCiclo']?></title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . ucfirst($_SESSION['usuario_nome']) . " (" . $_SESSION['usuario_rol'] . ")" . "</p>"; ?>        
        <a href="logout.php" class="logout">Logout</a>
    </nav> 
    <div class="form-container">
    <h2>Edicion del ciclo <?=$_POST['codigoCiclo']?> <?=$_POST['nombreCiclo']?></h2>

        <form action="gestionaCiclo.php" method="post">
            <fieldset>
                <label for="codigoCiclo">Código</label>
                <input type="text" name="codigoCiclo" maxlength="50" value="<?=$_POST['codigoCiclo']?>" required>
            </fieldset>

            <fieldset>
                <label for="codigoCiclo">Código</label>
                <input type="text" name="codigoCiclo" maxlength="50" value="<?=$_POST['codigoCiclo']?>" required>
            </fieldset>
            
            <fieldset>
                <label for="nombreCiclo">Nombre</label>
                <input type="text" name="nombreCiclo" maxlength="100" value="<?=$_POST['nombreCiclo']?>" required>
            </fieldset>

            <input type='hidden' name='idCiclo' value='<?=$_POST['idCiclo']?>'>
            
            <div class="botones">
                <button type="submit" name='boton' value='editar'>Editar ciclo</button>
                <a href='admin.php' class='volver'>Volver</a>
            </div>  
            
        </form>
    </div>
</body>
</html>