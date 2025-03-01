<?php

session_start();

if (!empty($_SESSION['error'])) {
    echo "<p style='color:red;'>" . htmlspecialchars($_SESSION['error']) . "</p>";
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
    <title>Matrícula</title>
</head>
<body>
    <div class="form-container">
        <form action="procesarMatricula.php" method="post" id="matriculaForm">
            <div class="form-group">
                <label for="ciclo">Selecciona un ciclo:</label>
                <select name="ciclo" id="ciclo">
                    <option value="">-- Selecciona un ciclo --</option>
                    <?php
                    require_once "conexion.php";

                    try {
                        $pdoStatement = $pdo->prepare("SELECT id_ciclo, codigo, name FROM ciclo ORDER BY name");
                        $pdoStatement->execute();
                        $ciclos = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($ciclos as $ciclo) {
                            echo "<option value='" . htmlspecialchars($ciclo['id_ciclo']) . "' " .
                                 "data-codigo='" . htmlspecialchars($ciclo['codigo'] . " - " . $ciclo['name']) . "'>" .
                                 htmlspecialchars($ciclo['codigo'] . " - " . $ciclo['name']) . 
                                 "</option>";
                        }
                    } catch (PDOException $e) {
                        echo "<option value=''>Error al cargar los ciclos</option>";
                        error_log("Error en la consulta: " . $e->getMessage());
                    }
                    ?>
                </select>
            </div>

            <div class="modulos-container" id="modulosContainer" style="display: none;">
                <label for="modulos">Selecciona los módulos:</label>
                <div id="modulosList">
                    <!-- Aquí se insertarán los módulos -->
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <button type="submit">Matricularme</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const cicloSelect = document.getElementById('ciclo');
            const modulosContainer = document.getElementById('modulosContainer');
            const modulosList = document.getElementById('modulosList');

            cicloSelect.addEventListener('change', function(){
                const cicloId = this.value;

                if (cicloId === '') {
                    modulosContainer.style.display = 'none';
                    return;
                }

                fetch('obtenerModulos.php?ciclo_id=' + encodeURIComponent(cicloId))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    modulosList.innerHTML = '';

                    if (data.length === 0) {
                        modulosList.innerHTML = '<p>No hay módulos disponibles para este ciclo</p>';
                    } else {
                        data.forEach(modulo => {
                            const checkboxItem = document.createElement('div');
                            checkboxItem.className = 'checkbox-item';
                            
                            checkboxItem.innerHTML = `
                                <input type="checkbox" id="modulo_${modulo.id_modulo}" 
                                    name="modulos[]" value="${modulo.id_modulo}">
                                <label for="modulo_${modulo.id_modulo}">
                                    ${modulo.nombre} (${modulo.curso})
                                </label>
                            `;
                            
                            modulosList.appendChild(checkboxItem);
                        });
                    }

                    modulosContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    modulosList.innerHTML = '<p>Ha ocurrido un error al cargar los módulos</p>';
                    modulosContainer.style.display = 'block';
                });
            });

            document.getElementById('matriculaForm').addEventListener('submit', function(e){
                if (cicloSelect.value !== '') {
                    const checkedModulos = document.querySelectorAll('input[name="modulos[]"]:checked');
                    if (checkedModulos.length === 0) {
                        e.preventDefault();
                        alert('Debes seleccionar al menos un módulo');
                    }
                }
            });
        });
    </script>
</body>
</html>