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
    <title>Editar usuario</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . $_SESSION['usuario_nome']." (".$_SESSION['usuario_rol'].")" . "</p>"; ?>
        <a href="logout.php">Logout</a>
    </nav>
    <h1>Edición del usuario <?=$_POST['nombreUsuario']?> <?=$_POST['apellidoUsuario']?></h1>
    <div class="form-container">
        <form action="gestionaUsuario.php" method="post">
            
            <label for="nombreUsuario">Nombre</label>
            <input type="text" name="nombreUsuario" maxlength="50" value="<?=$_POST['nombreUsuario']?>" required>

            <label for="apellidoUsuario">Apellidos</label>
            <input type="text" name="apellidoUsuario" maxlength="50" value="<?=$_POST['apellidoUsuario']?>" >

            <label for="emailUsuario">Email</label>
            <input type="email" name="emailUsuario" maxlength="100" value="<?=$_POST['emailUsuario']?>" required>

            <label for="dniUsuario">DNI</label>
            <input type="text" name="dniUsuario" maxlength="9" value="<?=$_POST['dniUsuario']?>"  required>

            <label for="loginUsuario">Login</label>
            <input type="text" name="loginUsuario" maxlength="9" value="<?=$_POST['loginUsuario']?>"  required>

            <input type='hidden' name='idUsuario' value='<?=$_POST['idUsuario']?>'>

            <button type="submit" name='boton' value='editar'>Editar usuario</button>
        </form>
    </div>
</body>
</html>