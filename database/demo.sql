USE huatulco;
SET NAMES utf8mb4;

INSERT INTO accommodations (name, accommodation_type, description, neighborhood, latitude, longitude, capacity, bedrooms, bathrooms, nightly_rate, cover_image, is_featured, is_published)
SELECT 'Villa Paraíso Tangolunda', 'casa', 'Espectacular villa contemporánea con vista panorámica al océano Pacífico, alberca privada tipo infinity, terraza para asados y acceso exclusivo a la playa de Tangolunda.', 'Paseo de los Cocoteros Lote 14, Bahía Tangolunda', 15.7674, -96.0963, 8, 4, 5, 5200.00, 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=1200&q=80', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM accommodations WHERE name = 'Villa Paraíso Tangolunda');

INSERT INTO accommodations (name, accommodation_type, description, neighborhood, latitude, longitude, capacity, bedrooms, bathrooms, nightly_rate, cover_image, is_featured, is_published)
SELECT 'Condominio Vista Mar Chahué', 'departamento', 'Cómodo y luminoso departamento a 3 minutos caminando del Club de Playa Chahué. Cuenta con balcón con vista a la marina, cocina integral equipada y alberca común.', 'Blvd. Benito Juárez 102, Marina Chahué', 15.7601, -96.1264, 4, 2, 2, 2450.00, 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM accommodations WHERE name = 'Condominio Vista Mar Chahué');

INSERT INTO accommodations (name, accommodation_type, description, neighborhood, latitude, longitude, capacity, bedrooms, bathrooms, nightly_rate, cover_image, is_featured, is_published)
SELECT 'Residencia Península Conejos', 'casa', 'Exclusiva residencia sobre el acantilado de Bahía Conejos. Arquitectura abierta con brisa marina constante, acabados en parota, alberca privada y servicio de concierge.', 'Fraccionamiento Punta Conejos Lote 8, Bahías de Huatulco', 15.7720, -96.0715, 10, 5, 6, 7800.00, 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1200&q=80', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM accommodations WHERE name = 'Residencia Península Conejos');

INSERT INTO accommodations (name, accommodation_type, description, neighborhood, latitude, longitude, capacity, bedrooms, bathrooms, nightly_rate, cover_image, is_featured, is_published)
SELECT 'Suite Boutique Marina Santa Cruz', 'habitacion', 'Suite romántica ideal para parejas, a unos pasos del embarcadero de Santa Cruz y mercado de artesanías. Incluye cama King size, terraza privada y desayuno continental.', 'Calle Mitla s/n, Bahía Santa Cruz', 15.7538, -96.1332, 2, 1, 1, 1600.00, 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM accommodations WHERE name = 'Suite Boutique Marina Santa Cruz');

INSERT INTO accommodations (name, accommodation_type, description, neighborhood, latitude, longitude, capacity, bedrooms, bathrooms, nightly_rate, cover_image, is_featured, is_published)
SELECT 'Eco-Lodge Cabaña La Bocana', 'casa', 'Refugio sustentable rodeado de vegetación tropical donde el río Copalita se une con el mar. Ideal para amantes del surf, avistamiento de aves y noches estrelladas.', 'Camino a la Bocana del Río Copalita, Huatulco', 15.7761, -96.0652, 4, 2, 2, 1950.00, 'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1200&q=80', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM accommodations WHERE name = 'Eco-Lodge Cabaña La Bocana');

INSERT INTO accommodations (name, accommodation_type, description, neighborhood, latitude, longitude, capacity, bedrooms, bathrooms, nightly_rate, cover_image, is_featured, is_published)
SELECT 'Loft Moderno Bahía Maguey', 'apartamento', 'Estudio de diseño abierto en una de las zonas más tranquilas de Huatulco. Grandes ventanales con vista a la bahía de aguas cristalinas ideales para esnórquel.', 'Mirador Bahía Maguey s/n, Huatulco', 15.7485, -96.1512, 3, 1, 1, 1850.00, 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM accommodations WHERE name = 'Loft Moderno Bahía Maguey');

INSERT INTO accommodations (name, accommodation_type, description, neighborhood, latitude, longitude, capacity, bedrooms, bathrooms, nightly_rate, cover_image, is_featured, is_published)
SELECT 'Villa Arrocito con Acceso a Caleta', 'casa', 'Hermosa casa vacacional a 100 metros de la apacible caleta de Playa Arrocito. Jardín privado con palmeras, alberca y palapa para descansar.', 'Fracc. Residencial Arrocito, Huatulco', 15.7612, -96.1105, 6, 3, 3, 3900.00, 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM accommodations WHERE name = 'Villa Arrocito con Acceso a Caleta');

INSERT INTO accommodations (name, accommodation_type, description, neighborhood, latitude, longitude, capacity, bedrooms, bathrooms, nightly_rate, cover_image, is_featured, is_published)
SELECT 'Penthouse Panorámico Bahía Chahué', 'departamento', 'Espectacular penthouse de dos niveles con jacuzzi privado en la terraza superior, vista 360° a los atardeceres de Huatulco y acabados de mármol.', 'Av. Carrizal Lote 5, Bahía Chahué', 15.7635, -96.1240, 6, 3, 3, 4100.00, 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM accommodations WHERE name = 'Penthouse Panorámico Bahía Chahué');

INSERT INTO activities (name, category, description, price, is_published)
SELECT 'Paseo en lancha por las bahías', 'naturaleza', 'Consulta opciones de recorrido, horario y punto de salida para tu grupo.', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM activities WHERE name = 'Paseo en lancha por las bahías');

INSERT INTO activities (name, category, description, price, is_published)
SELECT 'Parapente', 'deportes', 'Consulta si hay operador y condiciones adecuadas para la fecha de tu visita.', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM activities WHERE name = 'Parapente');

INSERT INTO activities (name, category, description, price, is_published)
SELECT 'Snorkel', 'deportes', 'Consulta opciones para explorar la vida marina y el equipo disponible para tu grupo.', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM activities WHERE name = 'Snorkel');

INSERT INTO activities (name, category, description, price, is_published)
SELECT 'Caminata guiada', 'naturaleza', 'Consulta rutas y duración según la fecha y el nivel del grupo.', 0, 1
WHERE NOT EXISTS (SELECT 1 FROM activities WHERE name = 'Caminata guiada');
