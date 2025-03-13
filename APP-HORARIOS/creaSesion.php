<?php
session_start();

if (!empty($_SESSION['error'])) {
    echo "<div class='mnsjError'>" . $_SESSION['error'] . "</div>";
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
    <link rel="stylesheet" href="css/formularios.css">
    <link rel="stylesheet" href="css/formulariosMovil.css">
    <title>Crear sesion</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . ucfirst($_SESSION['usuario_nome']) . " (" . $_SESSION['usuario_rol'] . ")" . "</p>"; ?>        
        <a href="logout.php" class="logout">Logout</a>
    </nav> 
    <div class="form-container">
        <form action="validaSesion.php" method="post">
            <fieldset>
                <label for="horaInicio">Hora de inicio</label>
                <select name="horaInicio" required>
                    <option value="08:45">08:45</option>
                    <option value="09:35">09:35</option>
                    <option value="10:25">10:25</option>
                    <option value="11:15">11:15</option>
                    <option value="12:05">12:05</option>
                    <option value="12:55">12:55</option>
                    <option value="13:45">13:45</option>
                </select>
            </fieldset>
            
            <fieldset>
                <label for="diaSemana">Día de la semana</label>
                <select name="diaSemana">
                    <option value="lunes">Lunes</option>
                    <option value="martes">Martes</option>
                    <option value="miercoles">Miércoles</option>
                    <option value="jueves">Jueves</option>
                    <option value="viernes">Viernes</option>
                </select>
            </fieldset>

            <fieldset>
                <label for="aula">Aula</label>
                <input type="text" name="aula" maxlength="25" required>
            </fieldset>
            
            <input type="hidden" name="idCicloModulo" value="<?=$_POST["idCicloModulo"]?>">
            <input type="hidden" name="idCiclo" value="<?=$_POST["idCiclo"]?>">

            <div class="botones">
                <button type="submit">Crear sesión</button>
                <a href='admin.php' class='volver'>Volver</a>
            </div>  
        </form>
    </div>
</body>
</html>