<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio en los Jugadores Activos</title>
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
        <div class="chart-container">
            <canvas id="graficoCambio"></canvas>
        </div>
        <div class="analysis-box">
            <h2>Análisis del Gráfico</h2>
            <p>Se puede observar en el gráfico que, en el transcurso de los últimos 5 años, tanto el promedio como el máximo de jugadores han tenido un comportamiento variable, con picos de subida y bajada. Durante inicios de 2022 se destacó un pico máximo de jugadores, lo cual coincide con el contexto de la pandemia de COVID-19, cuando muchas personas buscaron entretenimiento digital. Sin embargo, este comportamiento fue temporal, ya que el promedio de jugadores disminuyó considerablemente durante los siguientes meses de 2022 y a lo largo de 2023.</p>
        </div>

        <?php
        // Ruta del archivo CSV
        $archivo = 'datos/The Elder Scrolls Online.csv';
        $fechas = [];
        $promedios = [];
        $maximos = [];

        // Verificar si el archivo existe
        if (file_exists($archivo)) {
            // Leer el archivo CSV
            if (($handle = fopen($archivo, 'r')) !== false) {
                fgetcsv($handle, 1000, ',', "\"", "\\"); // Saltar encabezados
                while (($fila = fgetcsv($handle, 1000, ',', "\"", "\\")) !== false) {
                    $fechas[] = $fila[0];
                    $promedios[] = (float) $fila[1];
                    $maximos[] = (float) $fila[2];
                }
                fclose($handle);
            }
        } else {
            echo "<p>Error: El archivo no se encontró.</p>";
        }
        ?>

        <script>
            const ctx = document.getElementById('graficoCambio').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($fechas); ?>,
                    datasets: [
                        {
                            label: 'Promedio de Jugadores',
                            data: <?php echo json_encode($promedios); ?>,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            fill: true
                        },
                        {
                            label: 'Máximo de Jugadores',
                            data: <?php echo json_encode($maximos); ?>,
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
                                stepSize: 5000
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
