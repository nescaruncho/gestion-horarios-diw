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
    <title>Editar usuario</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . ucfirst($_SESSION['usuario_nome']) . " (" . $_SESSION['usuario_rol'] . ")" . "</p>"; ?>        
        <a href="logout.php" class="logout">Logout</a>
    </nav> 
    <div class="form-container">
    <h2>Edición del usuario <?=$_POST['nombreUsuario']?> <?=$_POST['apellidoUsuario']?></h2>

        <form action="gestionaUsuario.php" method="post">
            <fieldset>
                <label for="nombreUsuario">Nombre</label>
                <input type="text" name="nombreUsuario" maxlength="50" value="<?=$_POST['nombreUsuario']?>" required>
            </fieldset>
            
            <fieldset>
                <label for="apellidoUsuario">Apellidos</label>
                <input type="text" name="apellidoUsuario" maxlength="50" value="<?=$_POST['apellidoUsuario']?>" >
            </fieldset>
            
            <fieldset>
                <label for="emailUsuario">Email</label>
                <input type="email" name="emailUsuario" maxlength="100" value="<?=$_POST['emailUsuario']?>" required>    
            </fieldset>
            
            <fieldset>
                <label for="dniUsuario">DNI</label>
                <input type="text" name="dniUsuario" maxlength="9" value="<?=$_POST['dniUsuario']?>"  required>
            </fieldset>
            
            <fieldset>
                <label for="loginUsuario">Login</label>
                <input type="text" name="loginUsuario" maxlength="9" value="<?=$_POST['loginUsuario']?>"  required>
            </fieldset>

            <fieldset>
                <label for="cursoUsuario">Curso</label>
                <select name="cursoUsuario" required>
                    <option value="1º">1º</option>
                    <option value="2º">2º</option>
                </select>
            </fieldset>
            
            <input type='hidden' name='idUsuario' value='<?=$_POST['idUsuario']?>'>
            <div class="botones">
                <button type="submit" name='boton' value='editar'>Editar usuario</button>
                <a href='admin.php' class='volver'>Volver</a>
            </div> 
        </form>
    </div>
</body>
</html>