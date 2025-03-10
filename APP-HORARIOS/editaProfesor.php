<?php
session_start();

if (!empty($_SESSION['error'])) {
    echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Editar profesor</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . $_SESSION['usuario_nome']." (".$_SESSION['usuario_rol'].")" . "</p>"; ?>
        <a href="logout.php">Logout</a>
    </nav>
    <h1>Edición del profesor <?=$_POST['nombreProfesor']?> <?=$_POST['apellidoProfesor']?></h1>
    <div class="form-container">
        <form action="gestionaProfesor.php" method="post">
            
            <label for="nombreProfesor">Nombre</label>
            <input type="text" name="nombreProfesor" maxlength="50" value="<?=$_POST['nombreProfesor']?>" required>

            <label for="apellidoProfesor">Apellido</label>
            <input type="text" name="apellidoProfesor" maxlength="50" value="<?=$_POST['apellidoProfesor']?>" >

            <label for="emailProfesor">Email</label>
            <input type="email" name="emailProfesor" maxlength="100" value="<?=$_POST['emailProfesor']?>" required>

            <input type='hidden' name='idProfesor' value='<?=$_POST['idProfesor']?>'>

            <button type="submit" name='boton' value='editar'>Actualizar</button>
        </form>
    </div>

</body>
</html>