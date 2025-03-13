<?php
session_start();

if (!empty($_SESSION['error'])) {
    echo "<div class='mnsjError'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/formularios.css">
    <link rel="stylesheet" href="css/formulariosMovil.css">
    <title>Rexistro de usuarios</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . ucfirst($_SESSION['usuario_nome']) . " (" . $_SESSION['usuario_rol'] . ")" . "</p>"; ?>        
        <a href="logout.php" class="logout">Logout</a>
    </nav>
    <div class="form-container">
        <form action="validaUsuario.php" method="post">
            <fieldset>
                <label for="nombreUsuario">Nombre</label>
                <input type="text" name="nombreUsuario" maxlength="50" required>
            </fieldset>
            
            <fieldset>
                <label for="apellidosUsuario">Apellidos</label>
                <input type="text" name="apellidosUsuario" maxlength="50">
            </fieldset>
            
            <fieldset>
                <label for="contraseña">Contraseña</label>
                <input type="password" name="contraseña" required>
            </fieldset>
            
            <fieldset>
                <label for="email">Email</label>
                <input type="email" name="email" maxlength="100" title="Por favor, ingrese un correo electrónico válido" required>
            </fieldset>
            
            <fieldset>
                <label for="dniUsuario">DNI</label>
                <input type="text" name="dniUsuario" maxlength="9" required>
            </fieldset>
            
            <fieldset>
                <label for="loginUsuario">Login</label>
                <input type="text" name="loginUsuario" maxlength="9" required>
            </fieldset>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="botones">
                <button type="submit">Crear usuario</button>
                <a href='admin.php' class='volver'>Volver</a>
            </div>  
        </form>
    </div>
</body>
</html>