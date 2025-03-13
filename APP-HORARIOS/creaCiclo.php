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
    <title>Crear ciclo</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . ucfirst($_SESSION['usuario_nome']) . " (" . $_SESSION['usuario_rol'] . ")" . "</p>"; ?>        
        <a href="logout.php" class="logout">Logout</a>
    </nav>
    <div class="form-container">
        <form action="validaCiclo.php" method="post" style="grid-template-columns: repeat(2, 1fr);">
            <fieldset>
                <label for="codigoCiclo">Código</label>
                <input type="text" name="codigoCiclo" maxlength="50" required>
            </fieldset>
            
            <fieldset>
                <label for="nombreCiclo">Nombre</label>
                <input type="text" name="nombreCiclo" maxlength="100">
            </fieldset>
            
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="botones">
                <button type="submit">Crear ciclo</button>
                <a href='admin.php' class='volver'>Volver</a>
            </div>            
        </form>
    </div>
</body>
</html>