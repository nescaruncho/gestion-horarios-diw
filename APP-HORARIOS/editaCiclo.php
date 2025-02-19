<?php
session_start();

if (!empty($_SESSION['error'])) {
    echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Edicion del ciclo <?=$_POST['codigoCiclo']?> <?=$_POST['nombreCiclo']?></title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . $_SESSION['usuario_nome']." (".$_SESSION['usuario_rol'].")" . "</p>"; ?>
        <a href="logout.php">Logout</a>
    </nav>
    <h1>Edicion del ciclo <?=$_POST['codigoCiclo']?> <?=$_POST['nombreCiclo']?></h1>
    <div class="form-container">
        <form action="gestionaCiclo.php" method="post">
            <label for="codigoCiclo">Código</label>
            <input type="text" name="codigoCiclo" maxlength="50" value="<?=$_POST['codigoCiclo']?>" required>

            <label for="nombreCiclo">Nombre</label>
            <input type="text" name="nombreCiclo" maxlength="100" value="<?=$_POST['nombreCiclo']?>" required>

            <input type='hidden' name='idCiclo' value='<?=$_POST['idCiclo']?>'>

            <button type="submit" name='boton' value='editar'>Editar ciclo</button>
        </form>
    </div>
</body>
</html>