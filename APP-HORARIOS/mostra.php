<?php
session_start();

if (!empty($_SESSION['error'])) {
    echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['error'] = "Error: Inicie sesión para acceder a esta página";
    header("Location: login.php");
    exit();
}

if ($_SESSION['usuario_rol'] == 'administrador') {
    header("Location: admin.php");
    exit();
}

require_once "conexion.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Mostra</title>
</head>
<body>
    <nav class="navbar">
        <?php echo "<p>" . $_SESSION['usuario_nome']." (".$_SESSION['usuario_rol'].")" . "</p>"; ?>
        <a href="logout.php">Logout</a>
    </nav>
    <table class="product-table">
        <thead>
            <tr>
                <th>Imaxe</th>
                <th>Nome</th>
                <th>Descrición</th>
                <th>Familia</th>
                <th>Comentarios</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $pdoStatement = $pdo->prepare("SELECT * FROM produto");
            $pdoStatement->execute();
            $filas = $pdoStatement->fetchAll();

            foreach ($filas as $produto) {
                $pdoStatement2 = $pdo->prepare("SELECT * FROM comentarios WHERE idProduto=?");
                $pdoStatement2->bindParam(1, $produto['idProduto']);
                $pdoStatement2->execute();
                $filas2 = $pdoStatement2->fetchAll();

                echo "<tr>";
                echo "<td><img src='".$produto['imaxe']."' alt='imaxeproducto' width='100'></td>";
                echo "<td>".$produto['nome']."</td>";
                echo "<td>".$produto['descricion']."</td>";
                echo "<td>".$produto['familia']."</td>";
                echo "<td>";

                switch ($_SESSION['usuario_rol']) {
                    case 'usuario':
                        foreach ($filas2 as $comentario) {
                            if ($comentario['moderado'] == 'si') {
                                echo "<p class='comment'>".$comentario['comentario']." <span class='date'>(".$comentario['dataCreacion'].")</span></p>";
                            }
                        }
                        echo "<form action='comenta.php' method='post'>";
                        echo "<input type='hidden' name='idProduto' value='".$produto['idProduto']."'>";
                        echo "<input type='text' name='comentario' required><br>";
                        echo "<button type='submit'>Añadir comentario</button>";
                        echo "</form>";
                        break;
                    
                    case 'moderador':
                        foreach ($filas2 as $comentario) {
                            if ($comentario['moderado'] == 'non') {
                                echo "<p class='comment'>".$comentario['comentario']." <span class='date'>(".$comentario['dataCreacion'].")</span></p>";
                                echo "<form action='moderarComentario.php' method='post'>";
                                echo "<input type='hidden' name='idComentario' value='".$comentario['idComentario']."'>";
                                echo "<button type='submit'>Moderar comentario</button>";
                                echo "</form>";
                            }
                        }
                        break;
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>