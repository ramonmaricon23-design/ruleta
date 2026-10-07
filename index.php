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

// 6. CATÁLOGO GENERAL DE POSTURAS
// ¡PEGA AQUÍ TUS 200 POSTURAS, SUPERMAN!

$poses = [
    // LOTE 1 (1 - 96)
    ['nombre' => 'El Misionero', 'descripcion' => 'Uno se acuesta boca arriba con las piernas separadas; el otro se coloca encima, apoyando antebrazos a los lados del pecho y manteniendo contacto visual constante vientre con vientre.'],
    ['nombre' => 'El Perrito', 'descripcion' => 'Uno se coloca a gatas apoyando rodillas y palmas en el colchón; la pareja se arrodilla por detrás sujetando firmemente las caderas para marcar la profundidad del empuje.'],
    ['nombre' => 'La Cucharita', 'descripcion' => 'Ambos se acuestan de costado mirando en la misma dirección; quien está detrás flexiona ligeramente las rodillas para encajar su pelvis contra los glúteos del compañero.'],
    ['nombre' => 'La Vaquera', 'descripcion' => 'Uno se tumba boca arriba; el compañero se sienta a horcajadas sobre su pelvis mirándolo de frente, apoyando las manos en el pecho o muslos para controlar el bote y el ángulo.'],
    ['nombre' => 'La Vaquera Invertida', 'descripcion' => 'La persona de arriba se sienta a horcajadas dándole la espalda al torso de su compañero, inclinándose hacia adelante sobre las piernas de este para un ángulo de entrada posterior.'],
    ['nombre' => 'El Loto', 'descripcion' => 'Uno se sienta con las piernas cruzadas; el compañero se sienta sobre su regazo rodeándole el torso con las piernas cruzadas a su espalda y entrelazando los brazos al cuello en abrazo cerrado.'],
    ['nombre' => 'El Yunque', 'descripcion' => 'La persona acostada boca arriba eleva las piernas rectas en vertical, descansando los tobillos o pantorrillas sobre los hombros de su pareja, quien se inclina hacia adelante.'],
    ['nombre' => 'La Carretilla', 'descripcion' => 'Uno apoya las palmas planas en el suelo con el tronco elevado; el compañero se mantiene de pie por detrás levantando las piernas del primero por los muslos para coordinar el movimiento.'],
    ['nombre' => 'El Puente', 'descripcion' => 'Uno apoya plantas de los pies y palmas en el suelo arqueando la columna hacia el techo; la pareja penetra arrodillada o de pie aprovechando la elevación de la cadera.'],
    ['nombre' => 'La Araña', 'descripcion' => 'Ambos se sientan en el suelo frente a frente, apoyando las palmas detrás para sostener el tronco y entrelazando las piernas por la cintura para acercar y balancear las pelvis.'],
    ['nombre' => 'El Arco', 'descripcion' => 'Uno se acuesta boca abajo arqueando el torso hacia atrás; la pareja se recuesta encima sujetando sus muñecas o costillas para acompasar el arqueo.'],
    ['nombre' => 'La X', 'descripcion' => 'Ambos se acuestan formando una cruz perpendicular con sus cuerpos; cruzan una pierna sobre la del compañero para bloquear las pelvis y generar un roce angulado.'],
    ['nombre' => 'El Exprimidor', 'descripcion' => 'Uno se tumba boca abajo con las piernas totalmente estiradas y apretadas entre sí; el otro se tiende encima entrando por detrás con un acople muy estrecho.'],
    ['nombre' => 'La Fusión', 'descripcion' => 'Misionero profundo en el que quien está acostado abajo cruza con fuerza los tobillos por detrás de los riñones del otro, fusionando los cuerpos sin hueco intermedio.'],
    ['nombre' => 'El Helicóptero', 'descripcion' => 'Uno boca arriba y el otro encima orientado hacia sus pies; se ejecutan giros rotatorios lentos de cadera sobre el eje pélvico.'],
    ['nombre' => 'El Perrito de pie', 'descripcion' => 'Uno inclina el torso hacia adelante apoyando los codos sobre una mesa, cama o mueble firme; el compañero penetra de pie por detrás sujetando la cintura.'],
    ['nombre' => 'El Columpio', 'descripcion' => 'De pie, uno carga al otro por debajo de los muslos elevándolo en el aire con la espalda apoyada en un muro o mueble para distribuir el esfuerzo.'],
    ['nombre' => 'El Cangrejo', 'descripcion' => 'La persona de abajo apoya pies y manos elevando el vientre horizontalmente como una mesa; el compañero se monta encima a horcajadas.'],
    ['nombre' => 'El Nudo', 'descripcion' => 'Ambos acostados de costado mirándose de frente; cada uno pasa una pierna por encima de la cadera del otro, cerrando el espacio hasta quedar trabados vientre contra vientre.'],
    ['nombre' => 'La Tijera', 'descripcion' => 'Tumbados de costado formando una V abierta; cruzan las piernas de manera alternada de modo que el movimiento de vaivén sea constante por fricción continua.'],
    ['nombre' => 'El Trapecio', 'descripcion' => 'Uno se sienta al borde de la cama con los pies en el suelo; el otro se acomoda sobre su regazo dejando caer el tronco hacia atrás casi suspendido.'],
    ['nombre' => 'El Bambú', 'descripcion' => 'Acostado boca arriba, quien recibe levanta una pierna totalmente estirada apoyándola en el hombro del compañero, manteniendo la otra extendida en el colchón.'],
    ['nombre' => 'La Bailarina', 'descripcion' => 'Ambos de pie frente a frente abrazados al cuello; uno levanta una pierna flexionada a 90 grados enganchando el talón sobre la cadera del otro.'],
    ['nombre' => 'La Flor de Loto Invertida', 'descripcion' => 'Sentados en el regazo en loto, pero quien está encima arquea la columna hacia atrás apoyando sus manos en los muslos de su pareja.'],
    ['nombre' => 'El Tornillo', 'descripcion' => 'Uno se acuesta boca arriba y el otro entra de lado en ángulo recto de 90 grados, creando una penetración perpendicular cruzada.'],
    ['nombre' => 'La Cobra', 'descripcion' => 'Uno boca abajo levanta el pecho sosteniéndose sobre los antebrazos como una cobra; el otro se acuesta encima apoyando su pecho sobre su espalda y penetrando desde atrás.'],
    ['nombre' => 'El Molino', 'descripcion' => 'Variante dinámica donde los cuerpos giran 360 grados rodando lateralmente sobre el colchón sin desenganchar el acople.'],
    ['nombre' => 'La Mariposa', 'descripcion' => 'Uno se acuesta al borde de una mesa con las nalgas justo en la orilla y los muslos abiertos; el compañero realiza la acción de pie sujetando sus rodillas.'],
    ['nombre' => 'El Caballo de Balancín', 'descripcion' => 'Sentados frente a frente con las piernas entrelazadas en las caderas, meciendo el tronco adelante y atrás al compás pélvico.'],
    ['nombre' => 'La Tabla', 'descripcion' => 'Misionero donde ambos cuerpos están completamente estirados en paralelo, vientre con vientre, sin flexionar las piernas.'],
    ['nombre' => 'La Rana', 'descripcion' => 'A gatas, la persona de abajo abre las rodillas al máximo hacia afuera bajando el pecho al colchón; el otro entra por detrás arrodillado.'],
    ['nombre' => 'La Estrella de Mar', 'descripcion' => 'Uno boca arriba con brazos y piernas abiertos en cruz sin oponer resistencia; el compañero se sitúa entre sus muslos con control absoluto.'],
    ['nombre' => 'El Trípode', 'descripcion' => 'Uno apoya las palmas en el suelo y los pies en la pared en semi-pino; la pareja de pie lo sujeta de las caderas guiando el contacto.'],
    ['nombre' => 'El Gato', 'descripcion' => 'Misionero donde quien está arriba apoya los antebrazos y arquea la columna arriba y abajo frotando el pubis contra el clítoris en cada repetición.'],
    ['nombre' => 'La Ostra', 'descripcion' => 'El que está acostado de espaldas dobla las piernas llevando las rodillas directamente contra su propio esternón, exponiendo al máximo la zona pélvica.'],
    ['nombre' => 'El Arado', 'descripcion' => 'Acostado boca arriba, quien recibe pasa las piernas por encima de su propia cabeza tocando el colchón con las puntas; el otro penetra desde arriba.'],
    ['nombre' => 'El Salto de Rana', 'descripcion' => 'La persona receptora se acuclilla dándole la espalda al compañero; el otro se arrodilla pegado detrás sujetando sus costillas.'],
    ['nombre' => 'La T', 'descripcion' => 'Uno acostado horizontal boca arriba y el otro arrodillado de forma perpendicular a su cadera formando una letra T perfecta.'],
    ['nombre' => 'El Acróbata', 'descripcion' => 'El que está acostado eleva las piernas y apoya las plantas de los pies en la cadera del compañero para sostenerlo suspendido en el aire.'],
    ['nombre' => 'La Plancha', 'descripcion' => 'Uno se tumba boca abajo con la cadera al borde del colchón y las piernas colgando al vacío; el otro entra de pie por detrás.'],
    ['nombre' => 'La Silla', 'descripcion' => 'Uno se sienta en una silla de espaldar recto; el compañero se sienta a horcajadas sobre sus muslos mirándolo de frente con los brazos al cuello.'],
    ['nombre' => 'La Silla Invertida', 'descripcion' => 'Sentado en una silla, el compañero se monta a horcajadas dándole la espalda e inclinándose hacia el frente sobre sus propias rodillas.'],
    ['nombre' => 'El Plegable', 'descripcion' => 'La persona de abajo flexiona las piernas juntas hacia arriba descansando las pantorrillas sobre el pecho del compañero que está encima.'],
    ['nombre' => 'La Montaña', 'descripcion' => 'Uno se sienta en el suelo con rodillas dobladas; el otro se sienta en su regazo mirando en la misma dirección, reclinándose sobre su pecho.'],
    ['nombre' => 'El Abrazo del Oso', 'descripcion' => 'Ambos de pie en abrazo frontal completo, con uno levantando una pierna para engancharla a la cadera del otro mientras se besan.'],
    ['nombre' => 'La Escalera', 'descripcion' => 'De pie en una escalera; uno se sitúa uno o dos peldaños por encima del otro para salvar diferencias de estatura y empujar con precisión.'],
    ['nombre' => 'El Canto de la Sirena', 'descripcion' => 'Acostado boca arriba, la persona receptora junta ambas piernas estiradas y las sube de lado apoyándolas sobre un solo hombro de su pareja.'],
    ['nombre' => 'La Cuchara Invertida', 'descripcion' => 'Acostados de lado, pero la persona de atrás se coloca orientada hacia los pies de quien tiene delante en un cruce oblicuo.'],
    ['nombre' => 'El Telescopio', 'descripcion' => 'Desde el perrito, quien recibe estira los brazos al frente y baja el pecho hasta tocar las sábanas, alargando la columna mientras la cadera queda en alto.'],
    ['nombre' => 'La Serpiente', 'descripcion' => 'Ambos acostados boca abajo en el suelo en paralelo, entrelazando las piernas y deslizando las caderas con ondulaciones continuas.'],
    ['nombre' => 'El Espejo', 'descripcion' => 'Sentados en postura de loto frente a un espejo de pared, observando el acoplamiento y los gestos mutuos reflejados.'],
    ['nombre' => 'La Cascada', 'descripcion' => 'Uno se acuesta al borde de la cama dejando caer la cabeza y los hombros hacia el suelo; el compañero se arrodilla sobre el colchón entrando por encima.'],
    ['nombre' => 'El Elevador', 'descripcion' => 'De pie, uno carga al otro sosteniéndolo por debajo de las nalgas mientras la espalda del alzado descansa contra la pared.'],
    ['nombre' => 'La Enredadera', 'descripcion' => 'De pie frente a frente; quien es sostenido cruza con firmeza ambos tobillos detrás de los riñones del otro sin tocar el suelo.'],
    ['nombre' => 'El Diamante', 'descripcion' => 'Acostados boca arriba frente a frente; flexionan las rodillas hacia afuera uniendo las plantas de sus pies para trazar un rombo pélvico.'],
    ['nombre' => 'La Cuna', 'descripcion' => 'Uno se sienta en un sillón ancho y acomoda a su pareja de costado en el regazo, rodeándola con ambos brazos como a un bebé.'],
    ['nombre' => 'El Puente Colgante', 'descripcion' => 'El que recibe apoya las palmas en el suelo y descansa los empeines sobre el asiento de un sofá; la pareja entra de pie por detrás.'],
    ['nombre' => 'La Pinza', 'descripcion' => 'De costado frente a frente; uno atrapa fuertemente una de las piernas del compañero comprimiéndola entre sus propios muslos.'],
    ['nombre' => 'El Velero', 'descripcion' => 'Misionero donde quien está encima eleva una pierna en ángulo de 90 grados totalmente vertical simulando el mástil de un barco.'],
    ['nombre' => 'La V', 'descripcion' => 'Acostado boca arriba, quien recibe abre las piernas totalmente estiradas en un ángulo muy amplio; el compañero se arrodilla entre ellas.'],
    ['nombre' => 'El Centauro', 'descripcion' => 'Variante de perrito donde el de abajo no apoya las manos: mantiene las rodillas en el suelo y el torso completamente erguido.'],
    ['nombre' => 'La Esfinge', 'descripcion' => 'Uno boca abajo sostiene el torso erguido apoyando antebrazos en el colchón; el compañero se acuesta sobre su espalda y penetra por detrás.'],
    ['nombre' => 'El Clavado', 'descripcion' => 'Quien recibe apoya hombros y nuca en la cama elevando la cadera en vertical; la pareja entra arrodillada o semidepie desde arriba.'],
    ['nombre' => 'La Ola', 'descripcion' => 'En vaquera, quien está arriba cambia el salto vertical por un movimiento basculante continuo de balanceo adelante-atrás.'],
    ['nombre' => 'El Trompo', 'descripcion' => 'Sentados en el loto en regazo, realizando giros pélvicos de 360 grados sobre el mismo punto de contacto sin levantarse.'],
    ['nombre' => 'El Nido', 'descripcion' => 'Acostados de lado acurrucados en postura fetal compacta; la pareja se pega a su espalda cubriéndolo entero en cucharita cerrada.'],
    ['nombre' => 'La Escuadra', 'descripcion' => 'Uno acostado en el sofá levanta las piernas rectas verticalmente apoyándolas en el respaldo; el otro entra de pie de frente.'],
    ['nombre' => 'El Zigzag', 'descripcion' => 'Ambos acostados de costado cruzando los torsos en ángulo diagonal asimétrico para que la penetración friccione lateralmente.'],
    ['nombre' => 'La Hamaca', 'descripcion' => 'Uno se cuelga del borde de la cama dejando caer la cadera libre al aire mientras el otro lo sostiene por los huesos ilíacos.'],
    ['nombre' => 'El Candado', 'descripcion' => 'Sentados frente a frente con brazos y piernas cruzados de manera inamovible alrededor del torso del otro sin holgura.'],
    ['nombre' => 'La Araña Tejedora', 'descripcion' => 'Sentados con las palmas apoyadas atrás, inclinando las caderas de izquierda a derecha sincrónicamente al compás.'],
    ['nombre' => 'El Triángulo', 'descripcion' => 'Uno arquea la espalda en puente y el otro descansa sus pies sobre sus muslos levantando la cadera, formando un triángulo.'],
    ['nombre' => 'La Flecha', 'descripcion' => 'Ambos estirados boca abajo y en línea recta perfecta uno encima del otro, reduciendo al mínimo la distancia corporal.'],
    ['nombre' => 'El Fuego', 'descripcion' => 'Cucharita acelerada donde la persona de atrás mantiene una cadencia de movimientos cortos, superficiales y muy enérgicos.'],
    ['nombre' => 'El Ciempiés', 'descripcion' => 'Ambos a cuatro patas sobre el colchón colocados en paralelo, rozándose las caderas lateralmente con balanceos coordinados.'],
    ['nombre' => 'La Langosta', 'descripcion' => 'La persona boca abajo dobla las rodillas hacia atrás intentando tocar sus glúteos con los talones; el otro penetra por detrás inclinándose sobre ella.'],
    ['nombre' => 'El Avestruz', 'descripcion' => 'A cuatro patas con los codos en la cama y la cabeza hundida entre cojines; la pareja entra por detrás sujetando firmemente sus costillas.'],
    ['nombre' => 'El Látigo', 'descripcion' => 'Posición de perrito donde quien está detrás acompaña cada empuje con un golpe de cadera amplio y profundo.'],
    ['nombre' => 'La Herradura', 'descripcion' => 'Uno se sienta al borde de la cama y arquea la columna hacia atrás hasta que los omóplatos tocan el colchón formando una curva en U.'],
    ['nombre' => 'El Péndulo', 'descripcion' => 'De pie frente a una pared; uno empuja rítmicamente el cuerpo del otro contra el muro aprovechando el rebote del impacto.'],
    ['nombre' => 'La Media Luna', 'descripcion' => 'Cucharita lateral donde ambos arquean sus columnas hacia atrás al mismo tiempo dibujando la curvatura de una media luna.'],
    ['nombre' => 'El Compás', 'descripcion' => 'En el misionero, la persona de arriba abre una sola pierna muy hacia el lateral para fijar un nuevo ángulo de roce.'],
    ['nombre' => 'El Nudo de Marinero', 'descripcion' => 'La persona de arriba pasa sus pies bajo las corvas de la persona de abajo, bloqueando sus tobillos como un ancla cerrada.'],
    ['nombre' => 'La Gota', 'descripcion' => 'En vaquera erguida, la persona de arriba se va deslizando lentamente hacia atrás hasta tumbarse por completo sobre el pecho del otro.'],
    ['nombre' => 'El Deslizador', 'descripcion' => 'Ambos completamente estirados en la cama vientre con vientre, deslizándose hacia arriba y hacia abajo mediante roce continuo.'],
    ['nombre' => 'La Estrella Fugaz', 'descripcion' => 'En vaquera, quien está arriba se inclina hacia atrás apoyando sus manos directamente sobre las espinillas de su pareja.'],
    ['nombre' => 'El Torpedo', 'descripcion' => 'Misionero directo donde el de arriba apoya las puntas de los pies y empuja su torso entero hacia adelante rozando el colchón.'],
    ['nombre' => 'La Cerradura', 'descripcion' => 'Misionero donde el de abajo cruza sus piernas sobre la cintura del de arriba y este cruza sus pies bajo las nalgas del de abajo.'],
    ['nombre' => 'El Cántaro', 'descripcion' => 'Uno boca arriba dobla sus piernas apretando los muslos contra su abdomen; el otro se arrodilla encima empujando verticalmente.'],
    ['nombre' => 'La Libélula', 'descripcion' => 'Ambos sentados formando un ángulo recto, tocándose exclusivamente de cadera hacia abajo sin juntar los pechos.'],
    ['nombre' => 'El Pincel', 'descripcion' => 'Juego previo donde uno pasa el cabello o las puntas de los dedos con lentitud por las zonas erógenas antes de la entrada.'],
    ['nombre' => 'La Almeja', 'descripcion' => 'Cucharita en la que la persona de adelante junta fuertemente sus muslos ofreciendo una resistencia estrecha al roce.'],
    ['nombre' => 'El Ancla', 'descripcion' => 'Misionero lento donde la persona de arriba apoya todo su peso corporal pecho con pecho sin usar los brazos de apoyo.'],
    ['nombre' => 'La Orquídea', 'descripcion' => 'Uno se sienta en una mesa alta con las piernas abiertas; la pareja se coloca de pie entre sus muslos con las manos en su cintura.'],
    ['nombre' => 'El Volcán', 'descripcion' => 'Quien está arriba se coloca en cuclillas sobre el otro, controlando la altura y velocidad con la fuerza de sus cuádriceps.'],
    ['nombre' => 'El Éxtasis', 'descripcion' => 'Abrazo frontal total con pechos y rostros pegados, manteniendo una respiración acompasada y miradas fijas.'],

    // LOTE 2 (97 - 192)
    ['nombre' => 'La Tabla de Planchar', 'descripcion' => 'Uno se tumba boca abajo con la cadera en el borde exacto de la cama; el otro, de pie en el suelo, lo toma por las caderas empujando horizontalmente.'],
    ['nombre' => 'El Reloj', 'descripcion' => 'En misionero, ambos van rotando sus cuerpos poco a poco sobre el colchón dibujando un círculo completo sin detener la marcha.'],
    ['nombre' => 'El Salto del Ángel', 'descripcion' => 'Quien está arriba en el misionero levanta el pecho y extiende ambos brazos en cruz hacia los lados, arqueando la espalda al compás.'],
    ['nombre' => 'La Cobra Real', 'descripcion' => 'Boca abajo, quien recibe arquea el torso sobre los codos y flexiona las rodillas tocando sus propios glúteos con los talones mientras el otro penetra por detrás.'],
    ['nombre' => 'El Nudo Corredizo', 'descripcion' => 'Sentados frente a frente; uno rodea el cuello del otro con los brazos y el compañero cierra con fuerza los tobillos alrededor de su espalda baja.'],
    ['nombre' => 'La Mecedora', 'descripcion' => 'Acostados en cucharita lateral, balanceando sincronizadamente el peso corporal hacia adelante y hacia atrás sin perder el acople.'],
    ['nombre' => 'El Trípode Invertido', 'descripcion' => 'La persona boca arriba levanta ambas piernas juntas descansando las pantorrillas sobre un solo hombro del compañero que está encima.'],
    ['nombre' => 'La Envolvente', 'descripcion' => 'Uno se sienta con piernas cruzadas en loto y el otro lo abraza desde atrás rodeándolo por completo con muslos y brazos entrelazados.'],
    ['nombre' => 'La Avalancha', 'descripcion' => 'Uno de pie apoya su espalda contra la pared y sostiene por los glúteos al compañero, quien salta dejándose caer con su peso sobre él.'],
    ['nombre' => 'El Torniquete', 'descripcion' => 'En vaquera, quien está arriba apoya las palmas en el pecho de su pareja y rota el torso hacia la izquierda y derecha de forma alterna.'],
    ['nombre' => 'La Silla Giratoria', 'descripcion' => 'Uno se sienta en una silla de oficina giratoria y el compañero se monta a horcajadas encima, haciendo rotar el asiento durante el movimiento.'],
    ['nombre' => 'El Flamenco', 'descripcion' => 'De pie frente a frente; uno dobla una pierna hacia atrás apoyando el empeine en el muslo del otro mientras se sujeta de sus hombros.'],
    ['nombre' => 'El Arpa', 'descripcion' => 'Postura de costado cara a cara con piernas entrelazadas, donde uno desliza los dedos con lentitud por la columna del compañero.'],
    ['nombre' => 'La Grapa', 'descripcion' => 'Uno tumbado boca arriba con piernas dobladas hacia el pecho; el otro se tumba encima en sentido inverso aprisionando sus muslos con los brazos.'],
    ['nombre' => 'El Colibrí', 'descripcion' => 'En cualquier postura, se suspenden los empujes largos para ejecutar vibraciones superficiales, hiper-rápidas y constantes en la entrada.'],
    ['nombre' => 'El Telescopio Invertido', 'descripcion' => 'A gatas, quien recibe pega el mentón y el pecho al colchón elevando la pelvis al máximo para que la pareja entre inclinada desde atrás.'],
    ['nombre' => 'La Catapulta', 'descripcion' => 'El de abajo tumbado boca arriba apoya las plantas de los pies en las caderas de su compañero, balanceándolo rítmicamente.'],
    ['nombre' => 'El Lazo', 'descripcion' => 'Acostado boca arriba, quien recibe sube las piernas rectas y cruza con firmeza los tobillos detrás de la nuca de su pareja.'],
    ['nombre' => 'La Tijera Abierta', 'descripcion' => 'Tumbados de lado frente a frente, abriendo las piernas hacia afuera en una V muy pronunciada para un contacto angular constante.'],
    ['nombre' => 'La Esponja', 'descripcion' => 'Misionero estático sin vaivén: compresión de pubis contra pubis contrayendo rítmicamente los músculos pélvicos al unísono.'],
    ['nombre' => 'El Yoyo', 'descripcion' => 'En vaquera, la persona de arriba sube la pelvis hasta casi salir y se deja caer con fuerza y precisión en cada repetición.'],
    ['nombre' => 'La Oruga', 'descripcion' => 'Ambos acostados boca abajo uno sobre el lomo del otro, reptando lentamente por la cama mediante balanceos de cadera coordinados.'],
    ['nombre' => 'El Loto Lateral', 'descripcion' => 'Iniciando sentados en loto abrazados, ambos se dejan caer juntos hacia un costado sobre el colchón sin soltar el nudo de extremidades.'],
    ['nombre' => 'La Encrucijada', 'descripcion' => 'Ambos tumbados de costado formando una X simétrica con los troncos, enganchando las piernas por detrás de las corvas de la pareja.'],
    ['nombre' => 'El Deslizamiento', 'descripcion' => 'Uno descansa boca arriba sobre una superficie lisa mientras la fuerza del compañero lo empuja suavemente deslizándolo sobre las sábanas.'],
    ['nombre' => 'El Pájaro Carpintero', 'descripcion' => 'Variante de perrito ejecutada con empujes extremadamente cortos, constantes y rápidos sin pausas intermedias.'],
    ['nombre' => 'La Estalactita', 'descripcion' => 'Uno se acuesta boca arriba dejando que la mitad superior del torso y la cabeza cuelguen hacia el suelo, mientras el otro actúa de pie.'],
    ['nombre' => 'El Camello', 'descripcion' => 'A cuatro patas, la persona de abajo redondea la columna hacia el techo como la joroba de un camello para modificar el ángulo interno.'],
    ['nombre' => 'La Bufanda', 'descripcion' => 'Acostado boca arriba, quien recibe sube las piernas rectas y envuelve los muslos alrededor del cuello del compañero como una bufanda.'],
    ['nombre' => 'El Salto de Pértiga', 'descripcion' => 'Uno descansa sobre una mesa o cómoda alta mientras el otro, de pie en el suelo, lo toma firmemente de las caderas con empujes horizontales.'],
    ['nombre' => 'La Doble V', 'descripcion' => 'Ambos boca arriba con las cabezas en extremos opuestos; abren las piernas en V y las entrelazan alternadas acoplando las pelvis.'],
    ['nombre' => 'El Nido de Águila', 'descripcion' => 'Uno se sienta en equilibrio en el respaldo de un sofá con las piernas abiertas y el compañero se sitúa de pie frente a él.'],
    ['nombre' => 'La Pluma', 'descripcion' => 'Uno reposa inmóvil boca arriba mientras el compañero recorre su piel acariciándolo con labios y torso con extrema lentitud.'],
    ['nombre' => 'El Candado Doble', 'descripcion' => 'Misionero estrecho donde los tobillos del de abajo se cierran tras la cintura del otro y los brazos del de arriba rodean su espalda.'],
    ['nombre' => 'La Mantarraya', 'descripcion' => 'Ambos acostados boca abajo uno sobre la espalda del otro, con brazos y piernas estirados en cruz cubriendo el colchón.'],
    ['nombre' => 'El Carrusel', 'descripcion' => 'Uno descansa boca arriba mientras la persona encima va cambiando su orientación corporal en giros de 45 grados tras varias repeticiones.'],
    ['nombre' => 'La Pirámide', 'descripcion' => 'Quien recibe se arrodilla sobre sus talones con la frente en el colchón; el compañero se recuesta sobre su espalda entrando por detrás.'],
    ['nombre' => 'El Salto Hacia Atrás', 'descripcion' => 'En vaquera invertida, la persona de arriba se arquea hacia atrás hasta posar las manos en los empeines de su pareja.'],
    ['nombre' => 'La Sirena Invertida', 'descripcion' => 'Quien está abajo se recuesta boca abajo con los tobillos firmemente apretados; la pareja se tumba encima penetrando por detrás.'],
    ['nombre' => 'El Abanico', 'descripcion' => 'Acostado boca arriba, quien recibe abre y cierra rítmicamente las piernas como las alas de un abanico acompañando la cadencia.'],
    ['nombre' => 'La Araña Boca Arriba', 'descripcion' => 'Variante de la araña donde uno apoya las manos y pies en mesa invertida y el otro reclina su espalda completamente plana en el suelo.'],
    ['nombre' => 'El Trompo Invertido', 'descripcion' => 'En posición de cucharita lateral, ambos ruedan juntos en bloque hacia el otro lado de la cama sin perder el acople.'],
    ['nombre' => 'La Escalada', 'descripcion' => 'Uno se apoya de pie con la espalda firme en la pared mientras el otro lo trepa de frente rodeándole el torso con las piernas.'],
    ['nombre' => 'El Tren', 'descripcion' => 'Ambos arrodillados sobre la cama mirando en la misma dirección (uno detrás del otro), balanceándose al unísono hacia adelante y atrás.'],
    ['nombre' => 'La Llave Inglesa', 'descripcion' => 'De lado frente a frente; uno atrapa fuertemente una pierna del otro entre sus propios muslos ejerciendo palanca de fricción.'],
    ['nombre' => 'El Escudo', 'descripcion' => 'Uno acostado boca arriba apoya sus palmas en los huesos de la cadera de su compañero para guiar la fuerza y ritmo exacto de cada empuje.'],
    ['nombre' => 'La Guitarra', 'descripcion' => 'Sentados en el lateral de la cama; uno levanta una sola pierna flexionada del otro sosteniéndola bajo el brazo como quien sujeta una guitarra.'],
    ['nombre' => 'La Grulla', 'descripcion' => 'Ambos de pie; uno sostiene al compañero elevándole una sola pierna por debajo de la corva manteniéndola suspendida en el aire.'],
    ['nombre' => 'El Sello', 'descripcion' => 'Tumbados en el suelo plano vientre con vientre, expulsando el aire a la vez y manteniendo los torsos pegados sin dejar hueco de luz.'],
    ['nombre' => 'La Ostra Abierta', 'descripcion' => 'Acostado boca arriba, quien recibe abre las rodillas hacia afuera y las lleva hacia sus axilas sujetándose las plantas de los pies con las manos.'],
    ['nombre' => 'El Búmeran', 'descripcion' => 'En misionero, el de abajo dobla sus piernas contra el pecho del de arriba, quien empuja suavemente las rodillas hacia atrás para cerrar el ángulo.'],
    ['nombre' => 'La Silla Reclinable', 'descripcion' => 'Uno se sienta en un sillón reclinable inclinado hacia atrás y el otro se sienta a horcajadas encima dejándose llevar por la gravedad.'],
    ['nombre' => 'El Submarino', 'descripcion' => 'En postura de cucharita o misionero, ambos se cubren de pies a cabeza con una sábana o edredón aislándose de cualquier estímulo exterior.'],
    ['nombre' => 'La Cinta', 'descripcion' => 'En cualquier postura sentada o acostada, los movimientos pélvicos dibujan la figura de un 8 horizontal continuo en lugar de ir en línea recta.'],
    ['nombre' => 'El Tobogán', 'descripcion' => 'Uno coloca una pila de almohadas bajo su cadera para crear una pendiente pronunciada, mientras el otro entra de rodillas de frente.'],
    ['nombre' => 'La Tijera Elevada', 'descripcion' => 'Postura de tijera de costado, pero con una almohada firme o cojín bajo la cadera de ambos para facilitar el balanceo pélvico.'],
    ['nombre' => 'El Remolino', 'descripcion' => 'Misionero donde quien está arriba apoya las palmas planas y hace rotar la cadera en círculos amplios y continuos sin salir.'],
    ['nombre' => 'La Pincelada', 'descripcion' => 'Quien está arriba aprovecha los muslos juntos de la persona acostada para frotar el clítoris o base con cada deslizamiento.'],
    ['nombre' => 'El Tridente', 'descripcion' => 'A gatas, quien recibe apoya el pecho, la frente y los antebrazos pegados al colchón con la pelvis elevada; el otro entra por detrás de pie o arrodillado.'],
    ['nombre' => 'La Aguja', 'descripcion' => 'Tumbados ambos de lado en paralelo, con las cuatro piernas totalmente rectas y pegadas como una sola línea, entrando por detrás.'],
    ['nombre' => 'El Ovillo', 'descripcion' => 'Ambos acostados de costado cara a cara, flexionando rodillas y brazos al máximo hasta quedar hechos una bola compacta e inseparable.'],
    ['nombre' => 'La Montaña Rusa', 'descripcion' => 'En postura de vaquera, la persona de arriba alterna cambios bruscos de tempo: 5 empujes lentos seguidos de 10 empujes frenéticos.'],
    ['nombre' => 'El Cuadro', 'descripcion' => 'Uno de pie apoyando su espalda plana contra la pared y el otro lo sostiene en el aire por los glúteos manteniéndolo inmóvil frente a él.'],
    ['nombre' => 'La Estatua', 'descripcion' => 'En el punto de mayor profundidad de cualquier postura, ambos congelan el movimiento contrayendo los músculos del suelo pélvico al unísono.'],
    ['nombre' => 'El Arado Lateral', 'descripcion' => 'La persona de abajo lleva las piernas por encima de su cabeza pero deja caer los pies ligeramente hacia un lateral, cambiando el ángulo de entrada.'],
    ['nombre' => 'La Campana', 'descripcion' => 'En vaquera invertida, la persona de arriba balancea el torso rítmicamente hacia adelante y atrás como el badajo de una campana.'],
    ['nombre' => 'El Fuelle', 'descripcion' => 'Acostados frente a frente con rodillas flexionadas, ambos empujan sus caderas al mismo tiempo haciéndolas chocar en el centro en cada tiempo.'],
    ['nombre' => 'La Viga', 'descripcion' => 'Uno acostado al borde de la cama deja caer una pierna estirada hacia el suelo mientras el otro entra de pie entre sus muslos.'],
    ['nombre' => 'El Colgante', 'descripcion' => 'Uno se sienta al borde de una mesa alta envolviendo la cintura de su compañero con las piernas, mientras el otro lo levanta ligeramente en vilo de pie.'],
    ['nombre' => 'La Esfinge Invertida', 'descripcion' => 'Uno acostado boca arriba apoya su peso sobre los codos arqueando el pecho hacia arriba, y el otro se sienta a horcajadas encima de espaldas a él.'],
    ['nombre' => 'El Pájaro', 'descripcion' => 'Ambos arrodillados frente a frente pecho contra pecho, rozándose los costados y brazos con movimientos suaves y envolventes.'],
    ['nombre' => 'El Rompecabezas', 'descripcion' => 'Ambos sentados de lado dentro del hueco de un sillón o sofá, encajando los torsos contra los cojines para lograr ángulos estrechos.'],
    ['nombre' => 'La Brújula', 'descripcion' => 'Uno acostado boca arriba con piernas abiertas mientras el compañero, arrodillado, va cambiando la inclinación de su torso hacia los 4 puntos cardinales.'],
    ['nombre' => 'El Anillo', 'descripcion' => 'Sentados en postura de loto frente a frente, rodeándose con una sábana o toalla atada firmemente alrededor de ambas cinturas para no separarse.'],
    ['nombre' => 'La Mariposa de Pie', 'descripcion' => 'Uno recostado de espaldas sobre una mesa alta; el compañero de pie le levanta ambas piernas apoyando los tobillos en sus costillas.'],
    ['nombre' => 'El Candelabro', 'descripcion' => 'Uno sostiene al otro en el aire de pie levantándolo por debajo de las nalgas mientras el compañero rodea su cuello con los brazos.'],
    ['nombre' => 'El Delfín', 'descripcion' => 'Ambos boca abajo en la cama uno sobre otro, coordinando ondulaciones continuas de cadera sin levantar el torso.'],
    ['nombre' => 'El Puente Elevador', 'descripcion' => 'Uno apoya manos y pies en el suelo en puente invertido mientras el otro entra arrodillado por encima de su pelvis.'],
    ['nombre' => 'La Hélice', 'descripcion' => 'En vaquera, quien está arriba apoya las palmas en los muslos de su pareja y hace rotar su pelvis como una hélice en giros de semicírculo.'],
    ['nombre' => 'El Sofá', 'descripcion' => 'Uno recostado boca arriba a lo largo del sofá y el otro encajado de frente entre el asiento y el respaldo aprovechando el espacio estrecho.'],
    ['nombre' => 'La Medusa', 'descripcion' => 'Uno descansa boca arriba y el otro se mueve encima ondulando suavemente brazos y torso con una lentitud casi líquida.'],
    ['nombre' => 'El Arlequín', 'descripcion' => 'Tumbados de lado, uno pasa los brazos por detrás de la cabeza del otro sujetándole la nuca mientras entrelazan las rodillas de forma juguetona.'],
    ['nombre' => 'La Escuadra Invertida', 'descripcion' => 'Uno se acuesta en el suelo apoyando las piernas rectas verticalmente contra la pared, y el compañero entra de rodillas o de pie frente a él.'],
    ['nombre' => 'El Nudo Celta', 'descripcion' => 'Sentados en el regazo frente a frente, cruzando primero los tobillos tras la espalda baja y luego las muñecas tras la nuca del otro.'],
    ['nombre' => 'La Avalancha Invertida', 'descripcion' => 'Uno apoya la espalda contra la pared y sostiene al otro que salta mirándolo de frente, entrelazando brazos y piernas alrededor de su torso.'],
    ['nombre' => 'El Trapecista', 'descripcion' => 'Uno se coloca en cuclillas en el borde delantero de una silla firme mientras el compañero entra arrodillado por debajo.'],
    ['nombre' => 'El Espejismo', 'descripcion' => 'De pie frente al espejo de la habitación; uno entra por detrás mientras ambos mantienen contacto visual directo a través del reflejo.'],
    ['nombre' => 'La Catapulta Lateral', 'descripcion' => 'Uno tumbado de lado con las rodillas pegadas al pecho; el otro se acomoda de rodillas por detrás entrando con inclinación oblicua.'],
    ['nombre' => 'El Remo', 'descripcion' => 'Sentados frente a frente con piernas abiertas y entrelazadas, tomándose de las manos y balanceando los torsos adelante y atrás al compás.'],
    ['nombre' => 'La Lámpara', 'descripcion' => 'Uno se sienta a horcajadas sobre el reposabrazos acolchado del sofá y el compañero entra de pie frente a él abrazándolo por la cintura.'],
    ['nombre' => 'El Relámpago', 'descripcion' => 'Posición a cuatro patas ejecutada sustituyendo la entrada recta por un patrón de empujes diagonales en zigzag izquierda-derecha.'],
    ['nombre' => 'La Cuchara Cruzada', 'descripcion' => 'En posición de cucharita, quien está detrás pasa su pierna superior por encima del torso del otro fijando la cadera.'],
    ['nombre' => 'El Arcoíris', 'descripcion' => 'Uno se recuesta boca abajo arqueando la columna hacia atrás y el otro se tumba sobre él cubriéndolo como un arco.'],
    ['nombre' => 'La Pluma al Viento', 'descripcion' => 'De pie frente a frente; uno carga al otro por la cintura mientras quien es levantado da suaves impulsos con las puntas de los pies en el suelo.'],
    ['nombre' => 'El Diamante Roto', 'descripcion' => 'Acostados de frente con plantas de los pies unidas en rombo, abriendo y cerrando las rodillas de forma alterna durante la penetración.'],
    ['nombre' => 'La Flor de Lis', 'descripcion' => 'Uno recostado boca arriba con las piernas muy abiertas; el otro entra arrodillado inclinando su torso lateralmente en cada compás.'],
    ['nombre' => 'El Eco', 'descripcion' => 'Cualquier postura donde quien recibe debe imitar con su pelvis la velocidad y fuerza exacta del movimiento que acaba de hacer el otro con un segundo de retardo.'],
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

        if ($es_ultima) {
            $reto_elegido = 'HASTA ACABAR LOS 2';
        } else {
            $reto_elegido = $retos_intermedios[array_rand($retos_intermedios)];
        }

        // Selección aleatoria evitando repetición de las últimas 50 posturas
        $historial = $_SESSION['poses_vistas'] ?? [];
        $disponibles = array_diff(array_keys($poses), $historial);
        
        // Seguridad: por si en algún momento hay menos de 50 posturas en el array
        if (empty($disponibles)) {
            $historial = [];
            $disponibles = array_keys($poses);
        }
        
        $idx_pose = $disponibles[array_rand($disponibles)];
        $historial[] = $idx_pose;
        
        // Mantener solo el recuerdo de las últimas 50
        if (count($historial) > 50) {
            array_shift($historial);
        }
        
        $_SESSION['poses_vistas'] = $historial;

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

        @media (max-width: 480px) {
            body { padding: 0.5rem; align-items: flex-start; }
            .card { padding: 1.2rem; margin-top: 1rem; margin-bottom: 1rem; }
            h1 { font-size: 1.4rem; margin-bottom: 1rem; }
            .resultado { padding: 1rem; margin: 1rem 0; }
            .lugar { font-size: 0.95rem; }
            .pose { font-size: 1.2rem; }
            .desc { font-size: 0.9rem; }
            .btn-principal { padding: 0.8rem 1rem; font-size: 1rem; }
            .btn-secundario { padding: 0.7rem 1rem; font-size: 0.85rem; }
            .reto-box { padding: 0.6rem 0.8rem; }
            .reto-texto { font-size: 0.85rem; }
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