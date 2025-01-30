<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Especiales y Aniversarios</title>
    <link rel="stylesheet" href="expansiones.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .navegacion {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            gap: 20px;
        }
        .chart-container {
            width: 90%;
            max-width: 800px;
        }
        .btn-small {
            padding: 10px;
            font-size: 1.5em;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-small:hover {
            background-color: #45a049;
        }
        .chart-container {
            margin: 0 auto;
        }
        .analysis-box p {
            text-align: justify;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        $juegos = [
            "World of Warcraft" => ["archivo" => "datos/World of Warcraft.csv", "aniversario" => ["11", "12"]],
            "The Elder Scrolls Online" => ["archivo" => "datos/The Elder Scrolls Online.csv", "aniversario" => ["04", "05"]],
            "Final Fantasy XIV" => ["archivo" => "datos/FINAL FANTASY XIV Online.csv", "aniversario" => ["08", "09"]],
            "Guild Wars 2" => ["archivo" => "datos/Guild Wars 2.csv", "aniversario" => ["08", "09"]],
            "The Lord of the Rings Online" => ["archivo" => "datos/The Lord of the Rings Online™.csv", "aniversario" => ["04"]],
            "Path of Exile" => ["archivo" => "datos/Path of Exile.csv", "aniversario" => ["10"]]
        ];

        $indicesJuegos = array_keys($juegos);
        $indiceJuego = isset($_GET['indice']) ? (int)$_GET['indice'] : 0;
        $indiceJuego = max(0, min($indiceJuego, count($indicesJuegos) - 1));
        $nombreJuego = $indicesJuegos[$indiceJuego];
        $archivoJuego = $juegos[$nombreJuego]["archivo"];
        $aniversarios = $juegos[$nombreJuego]["aniversario"];

        $eventos_especiales = ["12", "01", "02", "10"]; // Diciembre, Enero, Febrero, Octubre

        function obtenerDatos($archivo) {
            $datos = [];
            if (($handle = fopen($archivo, 'r')) !== false) {
                fgetcsv($handle);
                while (($fila = fgetcsv($handle)) !== false) {
                    $fecha = DateTime::createFromFormat('F Y', $fila[0]);
                    if ($fecha) {
                        $mes = $fecha->format('m');
                        $anio_mes = $fecha->format('Y-m');
                        $promedio = (float) $fila[1];
                        $datos[$anio_mes] = ["mes" => $mes, "promedio" => $promedio];
                    }
                }
                fclose($handle);
            }
            ksort($datos); // Ordenar de más antiguo a más reciente
            return $datos;
        }

        $datos = obtenerDatos($archivoJuego);
        $labels = array_keys($datos);
        $promedios = array_column($datos, 'promedio');

        // Determinar qué barras resaltar
        $coloresBarras = array_map(function($anio_mes) use ($datos, $eventos_especiales, $aniversarios) {
            $mes = $datos[$anio_mes]["mes"];
            if (in_array($mes, $eventos_especiales) || in_array($mes, $aniversarios)) {
                return [
                    "backgroundColor" => 'rgba(255, 99, 132, 0.5)',
                    "borderColor" => 'rgba(255, 99, 132, 1)'
                ];
            } else {
                return [
                    "backgroundColor" => 'rgba(54, 162, 235, 0.5)',
                    "borderColor" => 'rgba(54, 162, 235, 1)'
                ];
            }
        }, $labels);
        ?>

        <h1>Eventos Especiales y Aniversarios: <?php echo $nombreJuego; ?></h1>

        <div class="navegacion">
            <a href="?indice=<?php echo ($indiceJuego - 1 + count($indicesJuegos)) % count($indicesJuegos); ?>" class="btn-small">←</a>
            <div class="chart-container">
                <canvas id="eventChart"></canvas>
            </div>
            <a href="?indice=<?php echo ($indiceJuego + 1) % count($indicesJuegos); ?>" class="btn-small">→</a>
        </div>

        <div class="analysis-box">
            <h2>Análisis del Gráfico</h2>
            <p>Los gráficos muestran cómo ciertos eventos temáticos y aniversarios tienen un impacto notable en la cantidad de jugadores activos, ya que proporcionan contenido adicional, como misiones especiales, recompensas únicas y promociones, lo que incrementa la participación de la comunidad. Sin embargo, también se observa que algunos eventos pasan desapercibidos, mostrando poca o ninguna variación en la actividad de los jugadores. Esto podría deberse a que ciertas festividades, como Navidad o Año Nuevo, limitan el tiempo libre de los usuarios, reduciendo su disponibilidad para disfrutar del contenido en comparación con otros meses donde la rutina permite mayor dedicación al juego.</p>
        </div>

        <a href="index.php" class="btn-volver">Volver al inicio</a>

        <script>
            const ctx = document.getElementById('eventChart').getContext('2d');
            const data = {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Fechas de eventos temáticos',
                    data: <?php echo json_encode($promedios); ?>,
                    backgroundColor: <?php echo json_encode(array_column($coloresBarras, "backgroundColor")); ?>,
                    borderColor: <?php echo json_encode(array_column($coloresBarras, "borderColor")); ?>,
                    borderWidth: 1
                }]
            };

            new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                boxWidth: 40,
                                boxHeight: 20,
                                generateLabels: function (chart) {
                                    const labels = chart.data.datasets.map(dataset => {
                                        return {
                                            text: dataset.label,
                                            fillStyle: 'rgba(255, 99, 132, 0.5)', // Fondo rojo claro
                                            strokeStyle: 'rgba(255, 99, 132, 1)', // Borde rojo
                                            lineWidth: 1
                                        };
                                    });
                                    return labels;
                                }
                            }
                        }
                    },
                    scales: { y: { beginAtZero: true } }
                }
            });
        </script>


    </div>
</body>
</html>