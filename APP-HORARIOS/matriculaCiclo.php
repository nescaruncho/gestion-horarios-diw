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
    <title>Document</title>
</head>
<body>
    <div class="form-container">
        <form action="validaMatriculaCiclo.php" method="post">
            <label for="codigoCiclo">Selecciona el ciclo o ciclos:</label>

            <?php
            require_once "conexion.php";

            $pdoStatement = $pdo->prepare("SELECT codigo,name FROM ciclo");

            $pdoStatement->execute();

            $filas = $pdoStatement->fetchAll();

            foreach ($filas as $ciclo) {
                echo "<input type='checkbox' name='ciclos[]' value='" . $ciclo['codigo'] . "'>" . 
                     $ciclo['codigo'] . " - " . $ciclo['name'] . "<br>";
            }
            ?>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <button type="submit">Enviar</button>
        </form>

    </div>
</body>
</html>