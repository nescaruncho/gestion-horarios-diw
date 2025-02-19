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
        <form action="validaCiclo.php" method="post">
            <label for="codigoCiclo">Código</label>
            <input type="text" name="codigoCiclo" maxlength="50" required>

            <label for="nombreCiclo">Nombre</label>
            <input type="text" name="nombreCiclo" maxlength="100">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>