<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis de Expansiones de MMORPGs</title>
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
                    "2022-02" => "Siege of the Atlas",
                    "2021-10" => "Scourge",
                    "2021-07" => "Expedition",
                    "2021-04" => "Ultimatum",
                    "2021-01" => "Echoes of the Atlas",
                    "2020-09" => "Heist",
                    "2020-06" => "Harvest",
                    "2020-03" => "Delirium",
                    "2019-12" => "Conquerors of the Atlas",
                    "2019-09" => "Blight",
                    "2019-06" => "Legion",
                    "2019-03" => "Synthesis",
                    "2018-12" => "Betrayal",
                    "2018-08" => "Delve",
                    "2017-12" => "War for the Atlas",
                    "2017-08" => "The Fall of Oriath",
                    "2016-09" => "Atlas of Worlds",
                    "2016-03" => "Ascendancy",
                    "2015-07" => "The Awakening",
                    "2014-08" => "Forsaken Masters",
                    "2014-03" => "Sacrifice of the Vaal"
                ]
            ]
        ];

        $indicesJuegos = array_keys($juegos);
        $indiceJuego = isset($_GET['indice']) ? (int)$_GET['indice'] : 0;
        $indiceJuego = max(0, min($indiceJuego, count($indicesJuegos) - 1));

        $nombreJuego = $indicesJuegos[$indiceJuego];
        $archivoJuego = $juegos[$nombreJuego]["archivo"];
        $expansiones = $juegos[$nombreJuego]["expansiones"];

        function obtenerPromedios($archivo, $expansiones) {
            $data = [];
            if (($handle = fopen($archivo, 'r')) !== false) {
                fgetcsv($handle);
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

            $promedios = [];
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
                $promedios[$nombre_expansion] = [
                    'antes' => round(array_sum($antes) / count($antes), 2),
                    'despues' => round(array_sum($despues) / count($despues), 2)
                ];
            }
            return $promedios;
        }

        $promedios = obtenerPromedios($archivoJuego, $expansiones);

        $labels = array_keys($promedios);
        $antes = array_column($promedios, 'antes');
        $despues = array_column($promedios, 'despues');
        ?>

        <h1>Impacto de las Expansiones: <?php echo $nombreJuego; ?></h1>

        <div class="navegacion">
            <a href="?indice=<?php echo ($indiceJuego - 1 + count($indicesJuegos)) % count($indicesJuegos); ?>" class="btn-small">←</a>
            <div class="chart-container">
                <canvas id="expansionChart"></canvas>
            </div>
            <a href="?indice=<?php echo ($indiceJuego + 1) % count($indicesJuegos); ?>" class="btn-small">→</a>
        </div>

        <div class="analysis-box">
            <h2>Análisis del Gráfico</h2>
            <p>El análisis de los gráficos sugiere que las expansiones de los MMORPGs tienen un impacto significativo en el comportamiento de los jugadores, aunque este efecto varía según el juego. Juegos como Final Fantasy XIV y The Elder Scrolls Online muestran un crecimiento pronunciado del promedio de jugadores tras ciertas expansiones clave, lo que indica el éxito en captar la atención de la comunidad. Por ejemplo, expansiones como Endwalker en Final Fantasy XIV y Greymoor en The Elder Scrolls Online lograron picos considerables en el promedio de jugadores mensuales después de su lanzamiento.<br>

                Sin embargo, en otros casos, como Guild Wars 2 y The Lord of the Rings Online, se observa una respuesta moderada o incluso una disminución en el promedio de jugadores después de ciertas expansiones. Este fenómeno puede atribuirse a diversos factores, como la falta de contenido atractivo o la competencia con otros juegos en el mercado.<br>

                Por otro lado, Path of Exile presenta un comportamiento mixto, con expansiones exitosas que generan picos notables, pero también periodos de estabilización o reducción en el promedio de jugadores en expansiones menos impactantes. En general, los datos sugieren que el éxito de una expansión no solo depende de su contenido, sino también del contexto del mercado y de las estrategias de marketing empleadas.</p>
        </div>

        <a href="index.php" class="btn-volver">Volver al inicio</a>

        <script>
            const ctx = document.getElementById('expansionChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [
                        {
                            label: 'Promedio Antes',
                            data: <?php echo json_encode($antes); ?>,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Promedio Después',
                            data: <?php echo json_encode($despues); ?>,
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        </script>
    </div>
</body>
</html>
