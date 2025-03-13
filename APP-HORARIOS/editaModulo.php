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
    <title>Edicion del módulo "<?=$_POST['nombreModulo']?>"</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . ucfirst($_SESSION['usuario_nome']) . " (" . $_SESSION['usuario_rol'] . ")" . "</p>"; ?>        
        <a href="logout.php" class="logout">Logout</a>
    </nav> 
    <div class="form-container">
    <h2>Edicion del módulo "<?=$_POST['nombreModulo']?>"</h2>

        <form action="gestionaModulo.php" method="post">
            <fieldset>
                <label for="nombreModulo">Nombre</label>
                <input type="text" name="nombreModulo" maxlength="100" value="<?=$_POST['nombreModulo']?>" required>
            </fieldset>
            
            <fieldset>
                <label for="cursoModulo">Curso</label>
                <select name="cursoModulo" value="<?=$_POST['cursoModulo']?>" required>
                    <option value="1º">1º</option>
                    <option value="2º">2º</option>
                </select>
            </fieldset>
            
            <fieldset>
                <label for="horasModulo">Horas totales</label>
                <input type="number" name="horasModulo" maxlength="11" value="<?=$_POST['horasModulo']?>">
            </fieldset>
            
            <input type='hidden' name='idModulo' value='<?=$_POST['idModulo']?>'>

            <div class="botones">
                <button type="submit" name='boton' value='editar'>Editar módulo</button>
                <a href='admin.php' class='volver'>Volver</a>
            </div> 
        </form>
    </div>
</body>
</html>