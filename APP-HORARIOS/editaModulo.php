<?php
session_start();

if (!empty($_SESSION['error'])) {
    echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Edicion del módulo "<?=$_POST['nombreModulo']?>"</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . $_SESSION['usuario_nome']." (".$_SESSION['usuario_rol'].")" . "</p>"; ?>
        <a href="logout.php">Logout</a>
    </nav>
    <h1>Edicion del módulo "<?=$_POST['nombreModulo']?>"</h1>
    <div class="form-container">
        <form action="gestionaModulo.php" method="post">

            <label for="nombreModulo">Nombre</label>
            <input type="text" name="nombreModulo" maxlength="100" value="<?=$_POST['nombreModulo']?>" required>

            <label for="cursoModulo">Curso</label>
            <select name="cursoModulo" value="<?=$_POST['cursoModulo']?>" required>
                <option value="1º">1º</option>
                <option value="2º">2º</option>
            </select>

            <label for="horasModulo">Horas totales</label>
            <input type="number" name="horasModulo" maxlength="11" value="<?=$_POST['horasModulo']?>">

            <input type='hidden' name='idModulo' value='<?=$_POST['idModulo']?>'>

            <button type="submit" name='boton' value='editar'>Editar módulo</button>
        </form>
    </div>
</body>
</html>