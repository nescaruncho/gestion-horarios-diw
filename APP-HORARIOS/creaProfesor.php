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
    <title>Crear profesor</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . ucfirst($_SESSION['usuario_nome']) . " (" . $_SESSION['usuario_rol'] . ")" . "</p>"; ?>        
        <a href="logout.php" class="logout">Logout</a>
    </nav> 
    <div class="form-container">
        <form action="validaProfesor.php" method="post">
            <fieldset>
                <label for="nombreProfesor">Nombre</label>
                <input type="text" name="nombreProfesor" maxlength="50" required>
            </fieldset>
            
            <fieldset>
                <label for="apellidoProfesor">Apellido</label>
                <input type="text" name="apellidoProfesor" maxlength="50" required>
            </fieldset>
            
            <fieldset>
                <label for="emailProfesor">Email</label>
                <input type="email" name="emailProfesor" maxlength="100" required>
            </fieldset>
            
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="botones">
                <button type="submit">Crear profesor</button>
                <a href='admin.php' class='volver'>Volver</a>
            </div>  
        </form>
    </div>

</body>
</html>