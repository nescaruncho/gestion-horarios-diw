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
    <title>Nuevo modulo</title>
</head>
<body>
    <div class="form-container">
        <form action="validaModulo.php" method="post">
            <label for="nombreModulo">Nombre</label>
            <input type="text" name="nombreModulo" maxlength="100" required>

            <label for="cursoModulo">Curso</label>
            <select name="cursoModulo" required>
                <option value="1º">1º</option>
                <option value="2º">2º</option>
            </select>

            <label for="horasModulo">Horas totales</label>
            <input type="number" name="horasModulo" maxlength="11">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>