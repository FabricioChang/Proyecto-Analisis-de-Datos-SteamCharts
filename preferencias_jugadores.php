<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preferencias de los Jugadores</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .content {
            text-align: center;
            max-width: 800px;
            width: 100%;
            padding: 20px;
        }
        .chart-container {
            margin: 0 auto;
        }
        .analysis-box p {
            text-align: justify;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="content">
        <h1>Preferencias de los Jugadores en MMORPGs</h1>
        <div class="chart-container">
            <canvas id="graficoPreferencias"></canvas>
        </div>
        <div class="analysis-box">
            <h2>Análisis del Gráfico</h2>
            <p>Por como se puede observar en el gráfico, el juego con un mayor promedio de jugadores activos es Path of Exile 2 con un total de 251,505.64 y por el otro lado el juego Adventure Quest 3D que es el que tiene menor cantidad de jugadores promedio activos el cual es de 252.3 jugadores, dándonos a entender que los jugadores prefieren el juego Path of Exile 2, siguiéndole Lost Ark con 133,779.75 y en 3er lugar Throne and Liberty con 78,808.31. Ese es el análisis.</p>
        </div>
        <div class="buttons">
            <a href="index.php" class="btn-volver">Volver al inicio</a>
        </div>
    </div>

    <?php
    // Directorio donde están los archivos CSV
    $directorio = 'datos';
    $promedios_juegos = [];
    $nombres_juegos = [];

    // Leer cada archivo CSV
    foreach (scandir($directorio) as $archivo) {
        if (pathinfo($archivo, PATHINFO_EXTENSION) === 'csv') {
            $ruta = $directorio . '/' . $archivo;
            $datos = [];
            if (($handle = fopen($ruta, 'r')) !== false) {
                fgetcsv($handle, 1000, ",", "\"", "\\"); // Saltar encabezados
                while (($fila = fgetcsv($handle, 1000, ",", "\"", "\\")) !== false) {
                    $datos[] = $fila;
                }
                fclose($handle);
            }
            // Calcular el promedio total para el juego
            if (!empty($datos)) {
                $columnas = array_column($datos, 1);
                $numeros = array_filter($columnas, 'is_numeric'); // Asegurar que sean números
                if (!empty($numeros)) {
                    $promedio_total = array_sum($numeros) / count($numeros);
                    $promedios_juegos[] = round($promedio_total, 2);
                    $nombres_juegos[] = pathinfo($archivo, PATHINFO_FILENAME);
                }
            }
        }
    }
    ?>

    <script>
        const ctx = document.getElementById('graficoPreferencias').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($nombres_juegos); ?>,
                datasets: [{
                    label: 'Promedio de Jugadores Activos',
                    data: <?php echo json_encode($promedios_juegos); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</body>
</html>
