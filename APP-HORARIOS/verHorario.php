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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once "conexion.php";


// 1) Obtener datos del usuario y módulos matriculados

$pdoStatement = $pdo->prepare("SELECT id_modulo FROM user_modulo WHERE id_user = ?");
$pdoStatement->bindParam(1, $_SESSION['usuario_id']);
$pdoStatement->execute();
$modulos = $pdoStatement->fetchAll(PDO::FETCH_COLUMN);

// 2) Obtener las sesiones de los módulos matriculados (sigla y horario)
$horario_formateado = [];

foreach ($modulos as $modulo) {

    $pdoStatement = $pdo->prepare("
        SELECT m.name AS modulo, s.dia_semana AS dia, s.hora_inicio, s.hora_fin
        FROM modulo m
        JOIN ciclo_tiene_modulo ctm ON m.id_modulo = ctm.id_modulo
        JOIN sesion s ON ctm.id_ciclo_modulo = s.id_ciclo_modulo
        WHERE m.id_modulo = ?
        ORDER BY s.hora_inicio, s.dia_semana
    ");
    $pdoStatement->execute([$modulo]);
    $horario = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

    // Formatear el horario para mostrarlo en la tabla
    foreach ($horario as $sesion) {
        $hora_inicio = $sesion['hora_inicio'];
        $hora_fin = $sesion['hora_fin'];
        $dia = $sesion['dia'];
        $modulo = $sesion['modulo'];

        // Crear un array multidimensional con la estructura [hora][día] = módulo
        $horario_formateado[$hora_inicio][$dia] = $modulo;
        $horario_formateado[$hora_fin][$dia] = $modulo;
    }
}

// Fix: Obtener y ordenar horas únicas
$horas_unicas = array_keys($horario_formateado);
sort($horas_unicas);

// 3) Definir días de la semana según tu BD
$dias_semana = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"];

// 4) Definir una paleta de 15 colores pastel (para asignación secuencial)
$paleta = [
    '#A3C1DA', // Azul pastel
    '#F6A4A4', // Rojo pastel
    '#B2D3C2', // Verde pastel
    '#FFF2A6', // Amarillo pastel
    '#C1A6E4', // Morado pastel
];

// Fix: Crear asignación de colores para módulos
$asignacionColores = [];
$modulos_unicos = array_unique(array_merge(...array_values(array_map('array_filter', $horario_formateado))));
$i = 0;
foreach ($modulos_unicos as $modulo) {
    $asignacionColores[$modulo] = $paleta[$i % count($paleta)];
    $i++;
}

// Añadir antes de pasar variables a JavaScript
$pdoStatement = $pdo->prepare("SELECT name, lastname FROM usuario WHERE id_user = ?");
$pdoStatement->execute([$_SESSION['usuario_id']]);
$usuario = $pdoStatement->fetch(PDO::FETCH_ASSOC);
$nombre_alumno = $usuario['name'] . ' ' . $usuario['lastname'];

$pdoStatement = $pdo->prepare("
    SELECT c.name as ciclo 
    FROM ciclo c 
    JOIN usuario_ciclo uc ON uc.id_ciclo = c.id_ciclo 
    WHERE uc.id_user = ? 
    LIMIT 1");
$pdoStatement->execute([$_SESSION['usuario_id']]);
$ciclo = $pdoStatement->fetch(PDO::FETCH_ASSOC);
$ciclo_formativo = $ciclo['ciclo'] ?? 'No especificado';

?>
<!DOCTYPE html>
<html>

<head>
    <title>Ver Horario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jsPDF y AutoTable -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <style>
        table {
            text-align: center;
        }

        th,
        td {
            vertical-align: middle !important;
            padding: 8px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <p><?php echo htmlspecialchars($nombre_alumno) . " (Alumno)"; ?></p>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container mt-3">
        <h2>Tu Horario</h2>
        <!-- Vista completa para tablet y desktop -->
        <div id="vistaCompleta">
            <div class="table-responsive">
                <table id="horarioTabla" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Horario</th>
                            <?php foreach ($dias_semana as $dia): ?>
                                <th><?php echo $dia; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horas_unicas as $hora): ?>
                            <tr>
                                <td><strong><?php echo $hora; ?></strong></td>
                                <?php foreach ($dias_semana as $dia): ?>
                                    <?php
                                    $modulo = $horario_formateado[$hora][$dia] ?? null;
                                    $color = $modulo ? ($asignacionColores[$modulo] ?? '') : '';
                                    ?>
                                    <td <?php echo $color ? 'style="background-color:' . $color . '; color: #000;"' : ''; ?>>
                                        <?php echo $modulo ? htmlspecialchars($modulo) : "-"; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-primary mt-3" onclick="exportarPDF()"
                style="background-color: #448AA6; border-color: #448AA6;">
                Exportar a PDF
            </button>
        </div>

        <!-- Pasamos variables a JavaScript en un solo bloque -->
        <script>
            const nombreAlumno = "<?php echo htmlspecialchars($nombre_alumno); ?>";
            const cicloFormativo = "<?php echo htmlspecialchars($ciclo_formativo); ?>";
            const asignacionColores = <?php echo json_encode($asignacionColores); ?>;
            const scheduleData = <?php echo json_encode($horario_formateado); ?>;
            const dias_semana = <?php echo json_encode($dias_semana); ?>;
        </script>

        <script>
            // Verificar que las librerías se cargan correctamente
            window.onload = function() {
                if (!window.jspdf || !window.jspdf.jsPDF) {
                    console.error('Error: jsPDF no está disponible');
                    document.querySelector('button[onclick="exportarPDF()"]').disabled = true;
                }
            }
        </script>

        <script>


            // Función para exportar a PDF
            function exportarPDF() {
                try {
                    // Verificar que tenemos los datos necesarios
                    if (!window.jspdf) {
                        console.error('Error: jsPDF no está cargado');
                        alert('Error al generar el PDF. Por favor, recarga la página.');
                        return;
                    }

                    if (!nombreAlumno || !cicloFormativo || !scheduleData || Object.keys(scheduleData).length === 0) {
                        alert('No hay datos suficientes para generar el PDF');
                        return;
                    }

                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF('l', 'mm', 'a4');
                    
                    // Resto del código de generación PDF igual...
                    doc.setFontSize(14);

                    // Título del documento
                    doc.text("Horario de Clases", 14, 10);

                    // Datos del alumno
                    doc.setFontSize(12);
                    doc.text(`Alumno: ${nombreAlumno}`, 14, 20);
                    doc.text(`Ciclo Formativo: ${cicloFormativo}`, 14, 30);

                    // Construimos los headers y el body para autoTable
                    const headers = ["Horario"].concat(dias_semana);
                    // Ej: ["Horario", "Luns", "Martes", "Mércores", "Xoves", "Venres"]

                    // Ordenamos las horas
                    const horas = Object.keys(scheduleData).sort();
                    const body = [];

                    for (const hora of horas) {
                        // Primera columna es la hora
                        const row = [hora];

                        // Para cada día, añadimos el módulo o "-"
                        for (const dia of dias_semana) {
                            const modulo = scheduleData[hora][dia] || "-";
                            row.push(modulo);
                        }

                        body.push(row);
                    }

                    // Generar la tabla en el PDF
                    doc.autoTable({
                        head: [headers],
                        body: body,
                        startY: 40,
                        theme: 'striped',
                        styles: {
                            fontSize: 10,
                            cellPadding: 2,
                            halign: 'center'
                        },
                        headStyles: {
                            fillColor: [68, 138, 166],
                            textColor: [255, 255, 255]
                        },
                        didParseCell: function (data) {
                            // Evitamos la primera fila (encabezados) y la primera columna (horario)
                            if (data.section === 'body' && data.column.index !== 0) {
                                const cellText = data.cell.raw.trim();
                                if (asignacionColores[cellText]) {
                                    // Convertir hex pastel a RGB
                                    const hex = asignacionColores[cellText].replace('#', '');
                                    const r = parseInt(hex.substring(0, 2), 16);
                                    const g = parseInt(hex.substring(2, 4), 16);
                                    const b = parseInt(hex.substring(4, 6), 16);
                                    data.cell.styles.fillColor = [r, g, b];
                                    // Texto en negro
                                    data.cell.styles.textColor = [0, 0, 0];
                                }
                            }
                        }
                    });

                    // Añadir catch para manejar errores
                    doc.save('Horario.pdf').catch(error => {
                        console.error('Error al guardar el PDF:', error);
                        alert('Error al guardar el PDF');
                    });
                } catch (error) {
                    console.error('Error al generar el PDF:', error);
                    alert('Error al generar el PDF');
                }
            }

        </script>

        <div class="container mt-3">
            <p><a href="mostra.php">Volver a inicio</a></p>
        </div>
</body>

</html>