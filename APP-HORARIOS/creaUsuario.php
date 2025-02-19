<?php
session_start();

if (!empty($_SESSION['error'])) {
    echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
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
    <link rel="stylesheet" href="css/style.css">
    <title>Rexistro de usuarios</title>
</head>
<body>
    <div class="form-container">
        <form action="validaRexistro.php" method="post">
            <label for="nombreUsuario">Nombre</label>
            <input type="text" name="nombreUsuario" maxlength="50" required>

            <label for="apellidosUsuario">Apellidos</label>
            <input type="text" name="apellidosUsuario" maxlength="50">

            <label for="contraseña">Contraseña</label>
            <input type="password" name="contraseña" required>

            <label for="email">Email</label>
            <input type="email" name="email" maxlength="100" title="Por favor, ingrese un correo electrónico válido" required>

            <label for="dniUsuario">DNI</label>
            <input type="text" name="dniUsuario" maxlength="9" required>

            <label for="loginUsuario">Login</label>
            <input type="text" name="loginUsuario" maxlength="9" required>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <button type="submit">Crear usuario</button>
        </form>
    </div>
</body>
</html>