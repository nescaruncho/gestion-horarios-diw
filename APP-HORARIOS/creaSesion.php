<?php
session_start();

if (!empty($_SESSION['error'])) {
    echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Crear sesion</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . $_SESSION['usuario_nome']." (".$_SESSION['usuario_rol'].")" . "</p>"; ?>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="form-container">
        <form action="validaSesion.php" method="post">
            <label for="horaInicio">Hora de inicio</label>
            <input type="time" name="horaInicio" required>

            <label for="horaFin">Hora de fin</label>
            <input type="time" name="horaFin" required>

            <label for="diaSemana">Día de la semana</label>
            <select name="diaSemana">
                <option value="lunes">Lunes</option>
                <option value="martes">Martes</option>
                <option value="miercoles">Miércoles</option>
                <option value="jueves">Jueves</option>
                <option value="viernes">Viernes</option>
            </select>

            <label for="aula">Aula</label>
            <input type="text" name="aula" maxlength="25" required>

            <input type="hidden" name="idCicloModulo" value="<?=$_POST["idCicloModulo"]?>">

            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>