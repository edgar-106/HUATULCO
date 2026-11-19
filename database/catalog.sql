USE huatulco;
SET NAMES utf8mb4;

INSERT INTO activities (catalog_section, category, name, description, price, is_published)
SELECT entry.catalog_section, entry.category, entry.name, entry.description, 0, 1
FROM (
    SELECT 'acuatica' AS catalog_section, 'deportes' AS category, 'Snorkel' AS name, 'Explora los arrecifes de Playa La Entrega, San Agustín o Chachacual. Consulta el punto de salida y el equipo disponible.' AS description
    UNION ALL
    SELECT 'acuatica' AS catalog_section, 'deportes' AS category, 'Buceo en arrecifes' AS name, 'Consulta inmersiones en Playa La Entrega, San Agustín o Chachacual y los requisitos según tu experiencia.' AS description
    UNION ALL
    SELECT 'acuatica' AS catalog_section, 'naturaleza' AS category, 'Paseo en lancha por las bahías' AS name, 'Recorre las Nueve Bahías en lancha. Consulta la ruta, las paradas y el punto de salida.' AS description
    UNION ALL
    SELECT 'acuatica' AS catalog_section, 'naturaleza' AS category, 'Paseo en catamarán' AS name, 'Consulta un recorrido en catamarán por las Nueve Bahías y las paradas disponibles.' AS description
    UNION ALL
    SELECT 'acuatica' AS catalog_section, 'deportes' AS category, 'Kayak' AS name, 'Consulta rutas en kayak y opciones de equipo según el tamaño de tu grupo.' AS description
    UNION ALL
    SELECT 'acuatica' AS catalog_section, 'deportes' AS category, 'Surf' AS name, 'Consulta playas, clases y renta de tabla según tu nivel.' AS description
    UNION ALL
    SELECT 'acuatica' AS catalog_section, 'deportes' AS category, 'Paddleboard' AS name, 'Consulta las zonas de salida y la disponibilidad de tablas de remo.' AS description
    UNION ALL
    SELECT 'acuatica' AS catalog_section, 'deportes' AS category, 'Jetski' AS name, 'Consulta la disponibilidad de motos acuáticas y los requisitos para conducirlas.' AS description
    UNION ALL
    SELECT 'aventura' AS catalog_section, 'naturaleza' AS category, 'Caminata guiada' AS name, 'Recorre senderos del Parque Nacional Huatulco. Consulta rutas y duración según tu grupo.' AS description
    UNION ALL
    SELECT 'aventura' AS catalog_section, 'naturaleza' AS category, 'Observación de aves' AS name, 'Consulta recorridos de observación de aves en el Parque Nacional Huatulco.' AS description
    UNION ALL
    SELECT 'aventura' AS catalog_section, 'deportes' AS category, 'Bicicleta en el Parque Nacional Huatulco' AS name, 'Consulta rutas, duración y renta de bicicleta para recorrer el parque.' AS description
    UNION ALL
    SELECT 'aventura' AS catalog_section, 'naturaleza' AS category, 'Cascadas de Llano Grande' AS name, 'Consulta una visita guiada y las opciones de natación, rappel o tirolesa disponibles.' AS description
    UNION ALL
    SELECT 'aventura' AS catalog_section, 'naturaleza' AS category, 'Cascadas de Copalitilla' AS name, 'Consulta una visita guiada y las opciones de natación, rappel o tirolesa disponibles.' AS description
    UNION ALL
    SELECT 'aventura', 'naturaleza', 'Natación en cascadas', 'Consulta en qué zonas de Llano Grande o Copalitilla se puede nadar durante tu visita.'
    UNION ALL
    SELECT 'aventura', 'deportes', 'Rappel en cascadas', 'Consulta recorridos de rappel en la zona de cascadas, equipo y requisitos para el grupo.'
    UNION ALL
    SELECT 'aventura', 'deportes', 'Tirolesa en la zona de cascadas', 'Consulta recorridos en tirolesa y los requisitos para participar.'
    UNION ALL
    SELECT 'aventura' AS catalog_section, 'deportes' AS category, 'Rafting en el Río Copalita' AS name, 'Consulta los tramos, horarios y requisitos según las condiciones del río.' AS description
    UNION ALL
    SELECT 'aventura' AS catalog_section, 'deportes' AS category, 'Paseo en cuatrimoto' AS name, 'Consulta rutas guiadas en cuatrimoto y los requisitos de la actividad.' AS description
    UNION ALL
    SELECT 'aventura' AS catalog_section, 'naturaleza' AS category, 'Paseo a caballo' AS name, 'Consulta recorridos a caballo según la experiencia del grupo.' AS description
    UNION ALL
    SELECT 'aventura' AS catalog_section, 'naturaleza' AS category, 'Avistamiento de fauna' AS name, 'Consulta recorridos guiados y qué fauna se puede observar según la temporada.' AS description
    UNION ALL
    SELECT 'aventura' AS catalog_section, 'naturaleza' AS category, 'Liberación de tortugas' AS name, 'Consulta jornadas de julio a diciembre. La fecha depende de la programación del campamento.' AS description
    UNION ALL
    SELECT 'aventura' AS catalog_section, 'naturaleza' AS category, 'Observación de bioluminiscencia' AS name, 'Consulta salidas nocturnas y las condiciones para observar el fenómeno.' AS description
    UNION ALL
    SELECT 'aerea' AS catalog_section, 'deportes' AS category, 'Parapente' AS name, 'Consulta si hay operador y condiciones adecuadas para la fecha de tu visita.' AS description
    UNION ALL
    SELECT 'aerea', 'deportes', 'Ala delta', 'Consulta si hay vuelos en tándem, qué requisitos tiene la actividad y desde dónde se despega.'
    UNION ALL
    SELECT 'aerea', 'deportes', 'Vuelo en avioneta o ultraligero', 'Consulta si hay sobrevuelos de las Nueve Bahías, la ruta y el punto de despegue.'
    UNION ALL
    SELECT 'aerea', 'deportes', 'Vuelo en helicóptero', 'Consulta las rutas panorámicas, la duración y el número de pasajeros permitido.'
    UNION ALL
    SELECT 'aerea', 'naturaleza', 'Vuelo en globo', 'Consulta si hay salidas, el punto de encuentro y las condiciones meteorológicas necesarias.'
    UNION ALL
    SELECT 'aerea', 'deportes', 'Paracaidismo o paseo en paracaídas', 'Consulta si hay saltos en tándem o paseos en paracaídas, y qué requisitos tiene cada modalidad.'
    UNION ALL
    SELECT 'cultura' AS catalog_section, 'cultura' AS category, 'Paseo por La Crucecita' AS name, 'Camina por el centro y visita comercios de artesanías locales.' AS description
    UNION ALL
    SELECT 'cultura' AS catalog_section, 'cultura' AS category, 'Cata de mezcal en Casa Chahue' AS name, 'Consulta horarios y opciones de degustación de mezcales.' AS description
    UNION ALL
    SELECT 'cultura' AS catalog_section, 'cultura' AS category, 'Visita a fincas cafetaleras' AS name, 'Consulta recorridos en El Pacífico o La Gloria para conocer el cultivo y la preparación del café.' AS description
    UNION ALL
    SELECT 'cultura' AS catalog_section, 'cultura' AS category, 'Museo de Artesanías Oaxaqueñas' AS name, 'Consulta horarios para visitar el museo y conocer piezas de artesanía regional.' AS description
    UNION ALL
    SELECT 'cultura' AS catalog_section, 'cultura' AS category, 'Gastronomía local' AS name, 'Consulta opciones para probar mole, quesillo, chapulines y otros sabores de Oaxaca.' AS description
    UNION ALL
    SELECT 'cultura' AS catalog_section, 'social' AS category, 'Club de playa' AS name, 'Consulta opciones de acceso a clubes de playa en Tangolunda.' AS description
    UNION ALL
    SELECT 'cultura' AS catalog_section, 'social' AS category, 'Spa y relajación' AS name, 'Consulta tratamientos, horarios y disponibilidad de spas.' AS description
    UNION ALL
    SELECT 'cultura' AS catalog_section, 'social' AS category, 'Vida nocturna' AS name, 'Consulta opciones para salir por la noche, como La Papaya.' AS description
) AS entry
WHERE NOT EXISTS (SELECT 1 FROM activities AS existing WHERE existing.name = entry.name);

UPDATE activities SET catalog_section = 'acuatica'
WHERE catalog_section IS NULL AND name IN ('Snorkel', 'Paseo en lancha por las bahías');

UPDATE activities SET catalog_section = 'aventura'
WHERE catalog_section IS NULL AND name = 'Caminata guiada';

UPDATE activities SET catalog_section = 'aerea'
WHERE (catalog_section IS NULL OR catalog_section = 'aventura') AND name = 'Parapente';

UPDATE activities SET description = 'Explora los arrecifes de Playa La Entrega, San Agustín o Chachacual. Consulta el punto de salida y el equipo disponible.'
WHERE name = 'Snorkel' AND description = 'Consulta opciones para explorar la vida marina y el equipo disponible para tu grupo.';

UPDATE activities SET description = 'Recorre las Nueve Bahías en lancha. Consulta la ruta, las paradas y el punto de salida.'
WHERE name = 'Paseo en lancha por las bahías' AND description = 'Consulta opciones de recorrido, horario y punto de salida para tu grupo.';

UPDATE activities SET description = 'Recorre senderos del Parque Nacional Huatulco. Consulta rutas y duración según tu grupo.'
WHERE name = 'Caminata guiada' AND description = 'Consulta rutas y duración según la fecha y el nivel del grupo.';
