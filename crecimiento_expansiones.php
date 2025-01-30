<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crecimiento Porcentual de Jugadores (Todas las Expansiones)</title>
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
        <h1>Crecimiento Porcentual de Jugadores (Todas las Expansiones)</h1>
        <div class="chart-container">
            <canvas id="comparativeChart"></canvas>
        </div>
        <div class="analysis-box">
            <h2>Análisis del Gráfico</h2>
            <p>El gráfico de crecimiento porcentual muestra que Final Fantasy XIV destaca con un crecimiento cercano al 40%, mientras que The Lord of the Rings Online y The Elder Scrolls Online presentan aumentos moderados. En contraste, juegos como Guild Wars 2 y Path of Exile enfrentan estancamientos o disminuciones, en parte debido a la saturación de pequeñas actualizaciones entre expansiones mayores. Además, lanzamientos apresurados y con bugs, como en el caso de World of Warcraft, han provocado caídas temporales en su base de jugadores. Esto demuestra que el éxito de una expansión depende no solo del contenido, sino también de una buena ejecución técnica y una estrategia bien equilibrada.</p>
        </div>
        <a href="index.php" class="btn-volver">Volver al inicio</a>
    </div>

    <?php
    $juegos = [
        "World of Warcraft" => [
            "archivo" => "datos/World of Warcraft.csv",
            "expansiones" => [
                "2021-06" => "The Burning Crusade Classic",
                "2022-09" => "Wrath of the Lich King Classic",
                "2022-11" => "Dragonflight",
                "2024-08" => "The War Within"
            ]
        ],
        "The Elder Scrolls Online" => [
            "archivo" => "datos/The Elder Scrolls Online.csv",
            "expansiones" => [
                "2017-05" => "Morrowind",
                "2018-05" => "Summerset",
                "2019-05" => "Elsweyr",
                "2020-06" => "Greymoor",
                "2021-06" => "Blackwood",
                "2022-06" => "High Isle",
                "2023-06" => "Necrom",
                "2024-06" => "Gold Road"
            ]
        ],
        "Final Fantasy XIV" => [
            "archivo" => "datos/FINAL FANTASY XIV Online.csv",
            "expansiones" => [
                "2015-06" => "Heavensward",
                "2017-06" => "Stormblood",
                "2019-07" => "Shadowbringers",
                "2021-12" => "Endwalker",
                "2024-07" => "Dawntrail"
            ]
        ],
        "Guild Wars 2" => [
            "archivo" => "datos/Guild Wars 2.csv",
            "expansiones" => [
                "2022-03" => "End of Dragons",
                "2023-08" => "Secrets of the Obscure",
                "2024-08" => "Janthir Wilds"
            ]
        ],
        "The Lord of the Rings Online" => [
            "archivo" => "datos/The Lord of the Rings Online™.csv",
            "expansiones" => [
                "2012-10" => "Riders of Rohan",
                "2013-11" => "Helm’s Deep",
                "2017-08" => "Mordor",
                "2019-11" => "Minas Morgul",
                "2020-10" => "War of Three Peaks",
                "2021-11" => "Fate of Gundabad"
            ]
        ],
        "Path of Exile" => [
            "archivo" => "datos/Path of Exile.csv",
            "expansiones" => [
                "2023-08" => "Trial of the Ancestors",
                "2023-04" => "Crucible",
                "2022-12" => "The Forbidden Sanctum",
                "2022-08" => "Lake of Kalandra",
                "2022-05" => "Sentinel",
                "2022-02" => "Siege of the Atlas"
            ]
        ]
    ];

    function obtenerCrecimientoTotal($archivo, $expansiones) {
        $data = [];
        if (($handle = fopen($archivo, 'r')) !== false) {
            fgetcsv($handle);  // Saltar encabezado
            while (($fila = fgetcsv($handle)) !== false) {
                if (!empty($fila[0]) && !empty($fila[1])) {
                    $fecha = DateTime::createFromFormat('F Y', $fila[0]);
                    if ($fecha) {
                        $data[$fecha->format('Y-m')] = (float) $fila[1];
                    }
                }
            }
            fclose($handle);
        }

        $total_crecimiento = 0;
        $cantidad_expansiones = count($expansiones);

        foreach ($expansiones as $fecha_expansion => $nombre_expansion) {
            $exp_date = new DateTime($fecha_expansion);
            $antes = $despues = [];

            for ($i = -3; $i <= 3; $i++) {
                $mod_date = (clone $exp_date)->modify("$i months")->format('Y-m');
                $valor = $data[$mod_date] ?? 0;
                if ($i < 0) {
                    $antes[] = $valor;
                } else {
                    $despues[] = $valor;
                }
            }

            $promedio_antes = !empty($antes) ? array_sum($antes) / count($antes) : 0;
            $promedio_despues = !empty($despues) ? array_sum($despues) / count($despues) : 0;

            if ($promedio_antes > 0) {
                $total_crecimiento += (($promedio_despues - $promedio_antes) / $promedio_antes) * 100;
            }
        }

        return $cantidad_expansiones > 0 ? round($total_crecimiento / $cantidad_expansiones, 2) : 0;
    }

    $resultados = [];
    foreach ($juegos as $juego => $detalles) {
        $resultados[$juego] = obtenerCrecimientoTotal($detalles["archivo"], $detalles["expansiones"]);
    }

    $labels = array_keys($resultados);
    $crecimientos_porcentuales = array_values($resultados);
    $colores = [
        'rgba(255, 99, 132, 0.5)',
        'rgba(54, 162, 235, 0.5)',
        'rgba(255, 206, 86, 0.5)',
        'rgba(75, 192, 192, 0.5)',
        'rgba(153, 102, 255, 0.5)',
        'rgba(255, 159, 64, 0.5)'
    ];
    $colores_bordes = [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
    ];
    ?>

    <script>
        const ctx = document.getElementById('comparativeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Crecimiento Porcentual (%)',
                    data: <?php echo json_encode($crecimientos_porcentuales); ?>,
                    backgroundColor: <?php echo json_encode($colores); ?>,
                    borderColor: <?php echo json_encode($colores_bordes); ?>,
                    borderWidth: 1
                }]
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
                            callback: function(value) {
                                return value + "%";  // Mostrar porcentaje
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
