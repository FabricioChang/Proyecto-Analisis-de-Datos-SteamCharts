<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio en los Jugadores Activos</title>
    <!-- Enlazar archivo CSS externo -->
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
        <h1>Cambio de Jugadores Activos en los Últimos 5 Años</h1>
        <!-- Gráfico -->
        <div class="chart-container">
            <canvas id="graficoCambio"></canvas>
        </div>

        <!-- Espacio para el análisis -->
        <div class="analysis-box">
            <h2>Análisis del Gráfico</h2>
            <p>Se puede observar en el gráfico que, en el transcurso de los últimos 5 años, tanto el promedio como el máximo de jugadores han tenido un comportamiento variable, con picos de subida y bajada. Durante inicios de 2022 se destacó un pico máximo de jugadores, lo cual coincide con el contexto de la pandemia de COVID-19, cuando muchas personas buscaron entretenimiento digital. Sin embargo, este comportamiento fue temporal, ya que el promedio de jugadores disminuyó considerablemente durante los siguientes meses de 2022 y a lo largo de 2023.</p>
        </div>

        <?php
        // Mapeo de meses en inglés a números
        $meses = [
            "January" => "01", "February" => "02", "March" => "03", "April" => "04",
            "May" => "05", "June" => "06", "July" => "07", "August" => "08",
            "September" => "09", "October" => "10", "November" => "11", "December" => "12"
        ];

        $directorio = "datos"; // Carpeta con los CSV
        $archivos = glob("$directorio/*.csv"); // Obtiene todos los archivos CSV automáticamente
        $datos_fechas = [];

        foreach ($archivos as $archivo) {
            if (($handle = fopen($archivo, 'r')) !== false) {
                while (($fila = fgetcsv($handle, 1000, ',', "\"", "\\")) !== false) {
                    if (count($fila) < 3 || empty($fila[0]) || empty($fila[1]) || empty($fila[2])) {
                        continue;
                    }

                    $fecha_texto = $fila[0]; // Ejemplo: "December 2024"
                    $promedio = (float) $fila[1];
                    $maximo = (float) $fila[2];

                    $partes = explode(" ", $fecha_texto);
                    if (count($partes) == 2) {
                        $mes_texto = $partes[0];
                        $ano = $partes[1];

                        if (isset($meses[$mes_texto])) {
                            $fecha_ordenable = "$ano-" . $meses[$mes_texto]; // Formato YYYY-MM
                            $datos_fechas[$fecha_ordenable] = [
                                "fecha" => $fecha_texto,
                                "promedio" => $promedio,
                                "maximo" => $maximo
                            ];
                        }
                    }
                }
                fclose($handle);
            }
        }

        // Ordenar fechas cronológicamente
        ksort($datos_fechas);
        $fechas = [];
        $datos_promedios = [];
        $datos_maximos = [];

        foreach ($datos_fechas as $item) {
            $fechas[] = $item["fecha"];
            $datos_promedios[] = $item["promedio"];
            $datos_maximos[] = $item["maximo"];
        }
        ?>

        <script>
            const ctx = document.getElementById('graficoCambio').getContext('2d');
            const graficoCambio = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($fechas); ?>,
                    datasets: [
                        {
                            label: 'Promedio de Jugadores',
                            data: <?php echo json_encode($datos_promedios); ?>,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true
                        },
                        {
                            label: 'Máximo de Jugadores',
                            data: <?php echo json_encode($datos_maximos); ?>,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1000
                            }
                        }
                    }
                }
            });
        </script>

        <div class="buttons">
            <a href="index.php" class="btn-volver">Volver al inicio</a>
        </div>
    </div>
</body>
</html>