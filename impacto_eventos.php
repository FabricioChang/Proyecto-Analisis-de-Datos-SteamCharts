<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impacto de Eventos Festivos</title>
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
        <h1>Impacto de Eventos Festivos en los MMORPGs</h1>
        <div class="chart-container">
            <canvas id="graficoEventos"></canvas>
        </div>
        <div class="analysis-box">
            <h2>Análisis del Gráfico</h2>
            <p>En esta tabla podemos observar como los días festivos como son: Navidad, Año Nuevo, Enero, San Valentin y Octubre afectan el tiempo de juego promedio de los jugadores y esto debido que las empresas de videojuegos estan acostumbradas a generar diferente tipo de eventos para las fechas especiales generando un mayor ingreso y generando un marketing y aumentando las horas de juego de los usuarios como se puede observar que en días festivos el promedio de jugadores es de 15.320,792 y el resto del año el promedio es de 13.422,71, demostrando que durante las festividades aumenta la cantidad de jugadores promedio.</p>
        </div>

        <?php
        $meses = [
            "January" => "01", "February" => "02", "March" => "03", "April" => "04",
            "May" => "05", "June" => "06", "July" => "07", "August" => "08",
            "September" => "09", "October" => "10", "November" => "11", "December" => "12"
        ];
        $fechas_festivas = ["12", "01", "02", "10"];
        $directorio = "datos";
        $archivos = glob("$directorio/*.csv");
        $promedios_festivos = [];
        $promedios_generales = [];

        foreach ($archivos as $archivo) {
            if (($handle = fopen($archivo, 'r')) !== false) {
                fgetcsv($handle, 1000, ",", "\"", "\\");
                while (($fila = fgetcsv($handle, 1000, ",", "\"", "\\")) !== false) {
                    $fecha_texto = $fila[0];
                    $promedio = (float) $fila[1];
                    $partes = explode(" ", $fecha_texto);
                    if (count($partes) == 2) {
                        $mes_texto = $partes[0];
                        $mes_numero = $meses[$mes_texto] ?? null;
                        if ($mes_numero) {
                            if (in_array($mes_numero, $fechas_festivas)) {
                                $promedios_festivos[] = $promedio;
                            } else {
                                $promedios_generales[] = $promedio;
                            }
                        }
                    }
                }
                fclose($handle);
            }
        }

        $promedio_festivo = !empty($promedios_festivos) ? array_sum($promedios_festivos) / count($promedios_festivos) : 0;
        $promedio_general = !empty($promedios_generales) ? array_sum($promedios_generales) / count($promedios_generales) : 0;
        ?>

        <script>
            const ctx = document.getElementById('graficoEventos').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Días Festivos', 'Resto del Año'],
                    datasets: [{
                        label: 'Promedio de Jugadores',
                        data: [<?php echo $promedio_festivo; ?>, <?php echo $promedio_general; ?>],
                        backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)'],
                        borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)'],
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

        <div class="buttons">
            <a href="index.php" class="btn-volver">Volver al inicio</a>
        </div>
    </div>
</body>
</html>
