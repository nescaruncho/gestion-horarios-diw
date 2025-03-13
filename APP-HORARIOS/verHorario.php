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

$idUsuario = $_SESSION['usuario_id'];

$pdoStatement = $pdo->prepare("SELECT id_ciclo FROM USUARIO_CICLO WHERE id_user = ?");
$pdoStatement->execute([$idUsuario]);
$idCiclo = $pdoStatement->fetchColumn();

$diasSemana = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"];
$horas = ["08:45", "09:35", "10:25", "11:15", "12:05", "12:55", "13:45"];

echo "<table border='1' style='border-collapse: collapse; text-align: center;'>";
echo "<thead>";
echo "<tr>";
echo "<th>Horario</th>"; 
foreach ($diasSemana as $dia) {
    echo "<th>$dia</th>";
}
echo "</tr>";
echo "</thead>";
echo "<tbody>";

foreach ($horas as $hora) {
    echo "<tr>";
    echo "<td><b>$hora</b></td>";

    foreach ($diasSemana as $dia) {
        $pdoStatement = $pdo->prepare("
            SELECT M.name 
            FROM SESION S
            JOIN CICLO_TIENE_MODULO CTM ON S.id_ciclo_modulo = CTM.id_ciclo_modulo
            JOIN MODULO M ON CTM.id_modulo = M.id_modulo
            WHERE CTM.id_ciclo = ? AND S.dia_semana = ? AND S.hora_inicio = ?
        ");
        $pdoStatement->execute([$idCiclo, $dia, $hora]);
        $modulo = $pdoStatement->fetchColumn(); 

        echo "<td>".($modulo ?: "")."</td>";
    }

    echo "</tr>";
}

echo "</tbody>";
echo "</table>";

// Obtener datos del alumno y ciclo para el PDF
$pdoStatement = $pdo->prepare("SELECT name, lastname FROM usuario WHERE id_user = ?");
$pdoStatement->execute([$idUsuario]);
$usuario = $pdoStatement->fetch(PDO::FETCH_ASSOC);
$nombre_alumno = $usuario['name'] . ' ' . $usuario['lastname'];

$pdoStatement = $pdo->prepare("SELECT name FROM ciclo WHERE id_ciclo = ?");
$pdoStatement->execute([$idCiclo]);
$ciclo = $pdoStatement->fetch(PDO::FETCH_ASSOC);
$ciclo_formativo = $ciclo['name'] ?? 'No especificado';

// Recogemos los datos de módulos para colores
$pdoStatement = $pdo->prepare("
    SELECT DISTINCT M.name 
    FROM SESION S
    JOIN CICLO_TIENE_MODULO CTM ON S.id_ciclo_modulo = CTM.id_ciclo_modulo
    JOIN MODULO M ON CTM.id_modulo = M.id_modulo
    WHERE CTM.id_ciclo = ?
");
$pdoStatement->execute([$idCiclo]);
$modulos = $pdoStatement->fetchAll(PDO::FETCH_COLUMN);

// Definir paleta de colores pastel
$paleta = [
    '#A3C1DA', // Azul pastel
    '#F6A4A4', // Rojo pastel
    '#B2D3C2', // Verde pastel
    '#FFF2A6', // Amarillo pastel
    '#C1A6E4', // Morado pastel
];

// Asignar colores a los módulos
$asignacionColores = [];
$i = 0;
foreach ($modulos as $modulo) {
    $asignacionColores[$modulo] = $paleta[$i % count($paleta)];
    $i++;
}

// Recopilar datos para el PDF
$horario_formateado = [];
foreach ($horas as $hora) {
    foreach ($diasSemana as $dia) {
        $pdoStatement = $pdo->prepare("
            SELECT M.name 
            FROM SESION S
            JOIN CICLO_TIENE_MODULO CTM ON S.id_ciclo_modulo = CTM.id_ciclo_modulo
            JOIN MODULO M ON CTM.id_modulo = M.id_modulo
            WHERE CTM.id_ciclo = ? AND S.dia_semana = ? AND S.hora_inicio = ?
        ");
        $pdoStatement->execute([$idCiclo, $dia, $hora]);
        $modulo = $pdoStatement->fetchColumn();
        
        if ($modulo) {
            $horario_formateado[$hora][$dia] = $modulo;
        }
    }
}
?>

<!-- Botón para exportar a PDF -->
<br>
<button onclick="exportarPDF()">
    Exportar a PDF
</button>
<br><br>

<!-- Scripts necesarios para exportar a PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<!-- Pasamos variables a JavaScript -->
<script>
    const nombreAlumno = "<?php echo htmlspecialchars($nombre_alumno); ?>";
    const cicloFormativo = "<?php echo htmlspecialchars($ciclo_formativo); ?>";
    const asignacionColores = <?php echo json_encode($asignacionColores); ?>;
    const scheduleData = <?php echo json_encode($horario_formateado); ?>;
    const dias_semana = <?php echo json_encode($diasSemana); ?>;
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

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');
            
            doc.setFontSize(14);
            // Título del documento
            doc.text("Horario de Clases", 14, 10);

            // Datos del alumno
            doc.setFontSize(12);
            doc.text(`Alumno: ${nombreAlumno}`, 14, 20);
            doc.text(`Ciclo Formativo: ${cicloFormativo}`, 14, 30);

            // Construimos los headers y el body para autoTable
            const headers = ["Horario"].concat(dias_semana);

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

            // Guardar el PDF
            doc.save('Horario.pdf');
        } catch (error) {
            console.error('Error al generar el PDF:', error);
            alert('Error al generar el PDF');
        }
    }
</script>
