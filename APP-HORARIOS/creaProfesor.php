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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Crear profesor</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . $_SESSION['usuario_nome']." (".$_SESSION['usuario_rol'].")" . "</p>"; ?>
        <a href="logout.php">Logout</a>
    </nav>
<div class="form-container">
        <form action="validaProfesor.php" method="post">
            <label for="nombreProfesor">Nombre</label>
            <input type="text" name="nombreProfesor" maxlength="50" required>

            <label for="apellidoProfesor">Apellido</label>
            <input type="text" name="apellidoProfesor" maxlength="50" required>

            <label for="emailProfesor">Email</label>
            <input type="email" name="emailProfesor" maxlength="100" required>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <button type="submit">Enviar</button>
        </form>
    </div>

</body>
</html>