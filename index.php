<?php
session_start();

// 1. SI SE ACCEDE POR GET (F5 o RECICLADO DE PÁGINA), REINICIAR TODO
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $_SESSION['partida_activa'] = false;
    $_SESSION['veces_total'] = 0;
    $_SESSION['ronda_actual'] = 0;
    $_SESSION['poses_vistas'] = [];
    $_SESSION['orales_vistas'] = [];
    unset($_SESSION['ultimo_estado']);
}

// 2. COOKIE: Contador acumulado de tiradas (30 días)
$tiradas = isset($_COOKIE['total_tiradas']) ? (int)$_COOKIE['total_tiradas'] : 0;

// 3. HABITACIONES DE LA CASA
$habitaciones = [
    'En el sofá del salón',
    'Sobre la encimera de la cocina',
    'Bajo la ducha',
    'En la cama principal',
    'En la alfombra del salón',
    'En la mesa del comedor',
    'En la terraza o balcón',
    'Sobre la lavadora en marcha',
    'Apoyados contra la pared del pasillo',
    'En las escaleras de la casa',
    'En un sillón individual o butaca',
    'Dentro de la bañera'
];

// 4. RETOS INTERMEDIOS "HASTA CUÁNDO" (Sin tiempo ni minutos)
$retos_intermedios = [
    'Hasta que uno de los dos varíe el ritmo tres veces consecutivas.',
    'Hasta que ambos se miren fijamente a los ojos sin sonreír durante 30 respiraciones profundas.',
    'Hasta que la persona activa bese con lentitud 5 zonas distintas del cuerpo del otro.',
    'Hasta que uno le susurre al oído al otro con precisión qué movimiento desea recibir a continuación.',
    'Hasta que sincronicen 50 movimientos continuos a un compás exacto.',
    'Hasta que quien está debajo consiga acelerar la respiración de su pareja solo mediante caricias pélvicas.',
    'Hasta que mantengan la fricción en absoluto silencio durante 20 repeticiones.',
    'Hasta que la persona receptora tome el control absoluto del balanceo de cadera.',
    'Hasta que coordinen la respiración pecho contra pecho durante 40 pulsos cardíacos.',
    'Hasta que uno le pida al otro una breve tregua con un mordisco en el hombro o cuello.'
];

// 5. ARRAY EXCLUSIVO DE SEXO ORAL (Activado únicamente en opción de rescate)
$poses_orales = [
    [
        'nombre' => 'El 69 Clásico',
        'descripcion' => 'Tumbados en sentidos opuestos, uno sobre otro, alineando la boca exactamente sobre el sexo del compañero para una estimulación lingual mutua simultánea.'
    ],
    [
        'nombre' => 'La Noria Lateral',
        'descripcion' => 'Postura de 69 pero ambos acostados de lado sobre el colchón; permite que el que ya terminó use lengua y manos con libertad sin cargar peso.'
    ],
    [
        'nombre' => 'La Corona 69 Circular',
        'descripcion' => 'Variante circular con cabezas apoyadas sobre almohadas y caderas flexionadas hacia el rostro del otro, maximizando el acceso a las zonas erógenas.'
    ],
    [
        'nombre' => 'El Trono Oral',
        'descripcion' => 'Quien aún no ha acabado se sienta en el sillón con las piernas abiertas; la pareja se arrodilla entre sus muslos concentrando boca y manos en el centro.'
    ],
    [
        'nombre' => 'El Pinball de Rodillas',
        'descripcion' => 'La persona no satisfecha permanece de pie o apoyada al borde de un mueble; el que acabó se arrodilla de frente combinando succión lingual y caricias manuales.'
    ],
    [
        'nombre' => 'El Puente de Hielo Oral',
        'descripcion' => 'El que no ha acabado arquea la pelvis en puente apoyando los pies firmes; el otro se desliza con el torso por debajo para estimular desde un ángulo inferior invertido.'
    ],
    [
        'nombre' => 'El Festín en la Orilla',
        'descripcion' => 'El que no terminó se acuesta boca arriba con los glúteos justo en el borde de la cama y las piernas sobre los hombros del compañero, quien de pie realiza el estímulo con toda la boca.'
    ],
    [
        'nombre' => 'La Ofrenda Invertida',
        'descripcion' => 'Quien debe terminar se coloca en perrito o boca abajo con la cadera en alto sobre cojines; el otro se acomoda por detrás para estimular oralmente clítoris o base con empuje directo.'
    ]
];

// 6. CATÁLOGO GENERAL DE POSTURAS (Recortado visualmente para la respuesta, el código asume que mantienes tus 192 posturas intactas)
// IMPORTANTE: Asegúrate de pegar aquí todo tu array original de $poses
$poses = [
    // LOTE 1 (1 - 96)
    ['nombre' => 'El Misionero', 'descripcion' => 'Uno se acuesta boca arriba con las piernas separadas; el otro se coloca encima, apoyando antebrazos a los lados del pecho y manteniendo contacto visual constante vientre con vientre.'],
    ['nombre' => 'El Perrito', 'descripcion' => 'Uno se coloca a gatas apoyando rodillas y palmas en el colchón; la pareja se arrodilla por detrás sujetando firmemente las caderas para marcar la profundidad del empuje.'],
    // ... AQUÍ VAN EL RESTO DE TUS POSTURAS ...
    ['nombre' => 'El Abrazo Infinito', 'descripcion' => 'Misionero muy lento y profundo, con frentes pegadas, brazos rodeando el cuello y miradas fijas hasta fundirse en una sola respiración.']
];

// 7. INICIALIZACIÓN DE VARIABLES
if (!isset($_SESSION['partida_activa'])) {
    $_SESSION['partida_activa'] = false;
    $_SESSION['veces_total'] = 0;
    $_SESSION['ronda_actual'] = 0;
    $_SESSION['poses_vistas'] = [];
    $_SESSION['orales_vistas'] = [];
}

$postura_elegida = null;
$habitacion_elegida = null;
$reto_elegido = null;
$ronda_actual = 0;
$veces_total = 0;
$es_ultima = false;

// 8. CONTROL DE ACCIONES (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'girar';

    if ($accion === 'girar') {
        $tiradas++;
        setcookie('total_tiradas', (string)$tiradas, time() + (30 * 24 * 60 * 60));

        if (!$_SESSION['partida_activa'] || $_SESSION['ronda_actual'] >= $_SESSION['veces_total']) {
            $_SESSION['partida_activa'] = true;
            $_SESSION['veces_total'] = rand(1, 5);
            $_SESSION['ronda_actual'] = 1;
        } else {
            $_SESSION['ronda_actual']++;
        }

        $ronda_actual = $_SESSION['ronda_actual'];
        $veces_total = $_SESSION['veces_total'];
        $es_ultima = ($ronda_actual === $veces_total);

        // En el 100% de los casos finales se exige acabar ambos
        if ($es_ultima) {
            $reto_elegido = 'HASTA ACABAR LOS 2';
        } else {
            $reto_elegido = $retos_intermedios[array_rand($retos_intermedios)];
        }

        // Selección sin repetición del catálogo general
        $disponibles = array_diff(array_keys($poses), $_SESSION['poses_vistas'] ?? []);
        if (empty($disponibles)) {
            $_SESSION['poses_vistas'] = [];
            $disponibles = array_keys($poses);
        }
        $idx_pose = $disponibles[array_rand($disponibles)];
        $_SESSION['poses_vistas'][] = $idx_pose;

        $postura_elegida = $poses[$idx_pose];
        $habitacion_elegida = $habitaciones[array_rand($habitaciones)];

        $_SESSION['ultimo_estado'] = [
            'postura' => $postura_elegida,
            'habitacion' => $habitacion_elegida,
            'reto' => $reto_elegido,
            'ronda' => $ronda_actual,
            'total' => $veces_total,
            'es_ultima' => $es_ultima
        ];
    } elseif ($accion === 'desempate') {
        $tiradas++;
        setcookie('total_tiradas', (string)$tiradas, time() + (30 * 24 * 60 * 60));

        // Selección sin repetición del array EXCLUSIVO DE SEXO ORAL
        $disponibles_oral = array_diff(array_keys($poses_orales), $_SESSION['orales_vistas'] ?? []);
        if (empty($disponibles_oral)) {
            $_SESSION['orales_vistas'] = [];
            $disponibles_oral = array_keys($poses_orales);
        }
        $idx_oral = $disponibles_oral[array_rand($disponibles_oral)];
        $_SESSION['orales_vistas'][] = $idx_oral;

        $postura_elegida = $poses_orales[$idx_oral];
        $habitacion_elegida = $habitaciones[array_rand($habitaciones)];
        $reto_elegido = 'MISIÓN FINAL (SEXO ORAL): El que ya ha terminado se entrega al 100% a hacer acabar al otro en esta postura.';

        $ronda_actual = $_SESSION['veces_total'];
        $veces_total = $_SESSION['veces_total'];
        $es_ultima = true;

        $_SESSION['ultimo_estado'] = [
            'postura' => $postura_elegida,
            'habitacion' => $habitacion_elegida,
            'reto' => $reto_elegido,
            'ronda' => $ronda_actual,
            'total' => $veces_total,
            'es_ultima' => true
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!-- ETIQUETA VITAL PARA MÓVILES -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ruleta Kamasutra</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #111827;
            color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
            box-sizing: border-box;
        }
        .card {
            background: #1f2937;
            padding: 2.2rem;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.6);
            max-width: 580px;
            width: 100%;
            text-align: center;
            box-sizing: border-box;
        }
        h1 { margin-top: 0; font-size: 1.7rem; color: #f43f5e; }
        .badge-progreso {
            display: inline-block;
            background: #374151;
            color: #38bdf8;
            padding: 0.35rem 0.9rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .resultado {
            background: #374151;
            padding: 1.4rem;
            border-radius: 10px;
            margin: 1.2rem 0;
            text-align: left;
        }
        .lugar { color: #fbbf24; font-size: 1.05rem; font-weight: bold; margin-bottom: 0.6rem; }
        .pose { font-size: 1.4rem; font-weight: bold; color: #ffffff; margin-bottom: 0.6rem; }
        .desc { font-size: 0.95rem; color: #d1d5db; line-height: 1.5; margin-bottom: 1rem; }
        .reto-box {
            background: #1e293b;
            border-left: 4px solid #f43f5e;
            padding: 0.8rem 1rem;
            border-radius: 4px;
        }
        .reto-titulo { font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; font-weight: bold; }
        .reto-texto { font-size: 0.95rem; font-weight: bold; color: #f87171; margin-top: 0.2rem; }
        .btn-principal {
            background: #f43f5e;
            color: white;
            border: none;
            padding: 0.9rem 1.5rem;
            font-size: 1.05rem;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
            margin-bottom: 0.6rem;
            box-sizing: border-box;
        }
        .btn-principal:hover { background: #e11d48; }
        .btn-secundario {
            background: #475569;
            color: white;
            border: none;
            padding: 0.75rem 1.2rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
            margin-bottom: 0.6rem;
            box-sizing: border-box;
        }
        .btn-secundario:hover { background: #334155; }
        .stats { margin-top: 1.2rem; font-size: 0.85rem; color: #9ca3af; line-height: 1.5; text-align: center; }

        /* --- AJUSTES PARA MÓVILES --- */
        @media (max-width: 480px) {
            body {
                padding: 0.5rem;
                align-items: flex-start; /* Evita cortes superiores en pantallas muy pequeñas */
            }
            .card {
                padding: 1.2rem;
                margin-top: 1rem;
                margin-bottom: 1rem;
            }
            h1 {
                font-size: 1.4rem;
                margin-bottom: 1rem;
            }
            .resultado {
                padding: 1rem;
                margin: 1rem 0;
            }
            .lugar {
                font-size: 0.95rem;
            }
            .pose {
                font-size: 1.2rem;
            }
            .desc {
                font-size: 0.9rem;
            }
            .btn-principal {
                padding: 0.8rem 1rem;
                font-size: 1rem;
            }
            .btn-secundario {
                padding: 0.7rem 1rem;
                font-size: 0.85rem;
            }
            .reto-box {
                padding: 0.6rem 0.8rem;
            }
            .reto-texto {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

<div class="card">
    <h1>Ruleta Kamasutra</h1>

    <?php if ($postura_elegida): ?>
        <div class="badge-progreso">
            Ronda <?= $ronda_actual ?> de <?= $veces_total ?> <?= $es_ultima ? '🔥 (ÚLTIMA RONDA)' : '' ?>
        </div>

        <div class="resultado">
            <div class="lugar">📍 <?= htmlspecialchars($habitacion_elegida) ?></div>
            <div class="pose"><?= htmlspecialchars($postura_elegida['nombre']) ?></div>
            <div class="desc"><?= htmlspecialchars($postura_elegida['descripcion']) ?></div>
            
            <div class="reto-box">
                <div class="reto-titulo">Hasta cuándo:</div>
                <div class="reto-texto"><?= htmlspecialchars($reto_elegido) ?></div>
            </div>
        </div>

        <form method="POST">
            <?php if ($es_ultima && $reto_elegido === 'HASTA ACABAR LOS 2'): ?>
                <input type="hidden" name="accion" value="desempate">
                <button type="submit" class="btn-secundario">¿Uno ha acabado y el otro no?<br>Pedir postura oral de rescate</button>
            <?php endif; ?>
        </form>

        <form method="POST">
            <input type="hidden" name="accion" value="girar">
            <button type="submit" class="btn-principal">
                <?= $es_ultima ? '¡Empezar nueva serie!' : '¡Siguiente postura!' ?>
            </button>
        </form>

    <?php else: ?>
        <p style="color: #9ca3af; margin: 2rem 0; font-size: 0.95rem;">Pulsa el botón para fijar la serie de posturas y el primer reto.</p>
        <form method="POST">
            <input type="hidden" name="accion" value="girar">
            <button type="submit" class="btn-principal">¡Empezar a jugar!</button>
        </form>
    <?php endif; ?>

    <div class="stats">
        Tiradas acumuladas: <strong><?= $tiradas ?></strong><br>
    </div>
</div>

</body>
</html>