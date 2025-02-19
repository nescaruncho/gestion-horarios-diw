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
    <title>Login</title>
</head>
<body>
    <div class="form-container">
        <form action="validaLogin.php" method="post">
            <label for="nomeUsuario">Nome de usuario</label>
            <input type="text" name="nomeUsuario" maxlength="20" required>

            <label for="contrasinal">Contrasinal</label>
            <input type="password" name="contrasinal" required>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>