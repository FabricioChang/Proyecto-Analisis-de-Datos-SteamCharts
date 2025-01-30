<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patrones Anuales de Jugadores</title>
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
        <h1>Patrones Anuales de Jugadores en los MMORPGs</h1>
        <div class="chart-container">
            <canvas id="graficoPatrones"></canvas>
        </div>
        <div class="analysis-box">
            <h2>Análisis del Gráfico</h2>
            <p>En este gráfico se puede observar como durante cada año existen picos de crecimiento y disminución, generando asi un comportamiento el cual se puede ir analizando, quitando el pico de jugadores promedio generado por la pandemia mundial del covid 19, podemos observar que el patron de jugadores promedio se ve aumentando cuando se llega a los ultimos meses del año, generando de esta manera un aumento de jugadores promedio, dandonos asi un patron de la jugabilidad de los jugadores y nos da entender porque las empresas durante esas fechas generan promociones o productos para lograr la mayor cantidad de ventas, y es gracias al analisis de este comportamiento.</p>
        </div>

        <?php
        $meses = [
            "January" => "01", "February" => "02", "March" => "03", "April" => "04",
            "May" => "05", "June" => "06", "July" => "07", "August" => "08",
            "September" => "09", "October" => "10", "November" => "11", "December" => "12"
        ];

        $directorio = "datos";
        $archivos = glob("$directorio/*.csv");
        $patrones_anuales = [];

        foreach ($archivos as $archivo) {
            if (($handle = fopen($archivo, 'r')) !== false) {
                fgetcsv($handle, 1000, ",", "\"", "\\");
                while (($fila = fgetcsv($handle, 1000, ",", "\"", "\\")) !== false) {
                    $fecha_texto = $fila[0];
                    $promedio = (float) $fila[1];
                    $partes = explode(" ", $fecha_texto);
                    if (count($partes) == 2) {
                        $mes_texto = $partes[0];
                        $ano = $partes[1];
                        $mes_numero = $meses[$mes_texto] ?? null;
                        if ($mes_numero) {
                            $clave = "$ano-$mes_numero";
                            $patrones_anuales[$clave][] = $promedio;
                        }
                    }
                }
                fclose($handle);
            }
        }

        $promedios_mensuales = [];
        foreach ($patrones_anuales as $clave => $valores) {
            $promedios_mensuales[$clave] = array_sum($valores) / count($valores);
        }

        ksort($promedios_mensuales);
        $labels = array_keys($promedios_mensuales);
        $datos = array_values($promedios_mensuales);
        ?>

        <script>
            const ctx = document.getElementById('graficoPatrones').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Promedio de Jugadores',
                        data: <?php echo json_encode($datos); ?>,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        </script>

        <div class="buttons">
            <a href="index.php" class="btn-volver">Volver al inicio</a>
        </div>
    </div>
</body>
</html>
