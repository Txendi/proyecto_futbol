-- ============================================================
-- BASE DE DATOS: sistema_futbol
-- Proyecto: Sistema de Scoring, Analisis de Rendimiento
--           y Control de Lesiones - SD Eibar
-- ============================================================

DROP DATABASE IF EXISTS sistema_futbol;
CREATE DATABASE sistema_futbol;
USE sistema_futbol;


-- ============================================================
-- TABLAS DE USUARIOS Y ACCESO
-- ============================================================

CREATE TABLE usuarios (
    id_usuario     INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50)  NOT NULL UNIQUE,
    contraseña_hash VARCHAR(255) NOT NULL,  -- Se guarda con bcrypt o texto plano en pruebas
    rol            ENUM('admin', 'entrenador', 'analista') NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ============================================================
-- TABLAS PRINCIPALES DEL CLUB
-- ============================================================

CREATE TABLE temporadas (
    id_temporada INT AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(20) NOT NULL,  -- Ej: '2025/26'
    fecha_inicio DATE,
    fecha_fin    DATE,
    activa       BOOLEAN DEFAULT FALSE
);

CREATE TABLE jugadores (
    id_jugador       INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(50) NOT NULL,
    apellidos        VARCHAR(50) NOT NULL,
    alias            VARCHAR(50),
    posicion_habitual VARCHAR(50),
    estado           ENUM('activo', 'lesionado', 'baja') DEFAULT 'activo',
    fecha_creacion   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dorsal de cada jugador por temporada
CREATE TABLE dorsales_jugador (
    id_dorsal    INT AUTO_INCREMENT PRIMARY KEY,
    id_jugador   INT,
    id_temporada INT,
    dorsal       INT UNIQUE,
    FOREIGN KEY (id_jugador)   REFERENCES jugadores  (id_jugador)   ON DELETE CASCADE,
    FOREIGN KEY (id_temporada) REFERENCES temporadas (id_temporada) ON DELETE CASCADE
);

CREATE TABLE partidos (
    id_partido       INT AUTO_INCREMENT PRIMARY KEY,
    id_temporada     INT,
    fecha            DATE NOT NULL,
    competicion      VARCHAR(100),
    rival            VARCHAR(100),
    local_visitante  ENUM('local', 'visitante'),
    goles_favor      INT DEFAULT 0,
    goles_contra     INT DEFAULT 0,
    fecha_creacion   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_temporada) REFERENCES temporadas (id_temporada)
);


-- ============================================================
-- TABLAS DE LESIONES
-- ============================================================

CREATE TABLE lesiones (
    id_lesion              INT AUTO_INCREMENT PRIMARY KEY,
    id_jugador             INT,
    fecha_inicio           DATE NOT NULL,
    fecha_fin              DATE,
    tipo_lesion            VARCHAR(100),
    gravedad               ENUM('leve', 'moderada', 'grave'),
    fecha_prevista_retorno DATE,
    observaciones          TEXT,
    fecha_creacion         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_jugador) REFERENCES jugadores (id_jugador)
);


-- ============================================================
-- TABLAS DEL SISTEMA DE PUNTUACIONES (gestionadas por Python)
-- ============================================================

-- Catalogo de posiciones
CREATE TABLE posiciones (
    codigo_posicion VARCHAR(5)  PRIMARY KEY,
    nombre_posicion VARCHAR(50) NOT NULL,
    linea           ENUM('defensa', 'medio', 'ataque') NOT NULL
);

-- Configuraciones de pesos del modelo de scoring
CREATE TABLE configuraciones_pesos (
    id_configuracion     INT AUTO_INCREMENT PRIMARY KEY,
    nombre_configuracion VARCHAR(100) NOT NULL,
    activa               BOOLEAN DEFAULT FALSE,
    fecha_creacion       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pesos por bloque (ataque / construccion / defensa) segun posicion
CREATE TABLE pesos_bloque_posicion (
    id_peso                  INT AUTO_INCREMENT PRIMARY KEY,
    id_configuracion         INT,
    codigo_posicion          VARCHAR(5),
    porcentaje_ataque        DECIMAL(5, 2),
    porcentaje_construccion  DECIMAL(5, 2),
    porcentaje_defensa       DECIMAL(5, 2),
    FOREIGN KEY (id_configuracion) REFERENCES configuraciones_pesos (id_configuracion),
    FOREIGN KEY (codigo_posicion)  REFERENCES posiciones (codigo_posicion)
);

-- Pesos de cada metrica dentro de su bloque
CREATE TABLE pesos_metrica_posicion (
    id_peso_metrica  INT AUTO_INCREMENT PRIMARY KEY,
    id_configuracion INT,
    codigo_posicion  VARCHAR(5),
    bloque           ENUM('ataque', 'construccion', 'defensa'),
    clave_metrica    VARCHAR(50),
    porcentaje       DECIMAL(5, 2),
    penaliza         BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_configuracion) REFERENCES configuraciones_pesos (id_configuracion),
    FOREIGN KEY (codigo_posicion)  REFERENCES posiciones (codigo_posicion)
);

-- Puntuaciones calculadas por Python para cada jugador en cada partido
CREATE TABLE puntuaciones (
    id_puntuacion           INT AUTO_INCREMENT PRIMARY KEY,
    id_partido              INT,
    id_jugador              INT,
    posicion_evaluada       VARCHAR(50),
    puntuacion_ataque       DECIMAL(4, 2),
    puntuacion_construccion DECIMAL(4, 2),
    puntuacion_defensa      DECIMAL(4, 2),
    factor_minutos          DECIMAL(4, 2),
    puntuacion_final        DECIMAL(4, 2),
    explicacion_positiva    TEXT,
    explicacion_negativa    TEXT,
    version_modelo          VARCHAR(50),
    fecha_creacion          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_partido)  REFERENCES partidos  (id_partido),
    FOREIGN KEY (id_jugador)  REFERENCES jugadores (id_jugador)
);

-- Estadisticas raw de cada jugador por partido (importadas desde Excel por Python)
CREATE TABLE estadisticas_jugador_partido (

    -- Campos generales
    id_estadistica  INT AUTO_INCREMENT PRIMARY KEY,
    id_partido      INT,
    id_jugador      INT,
    posicion_jugada VARCHAR(50),
    minutos_jugados INT,

    -- Estadisticas ofensivas
    goles               INT DEFAULT 0,
    asistencias         INT DEFAULT 0,
    tiros_totales       INT DEFAULT 0,
    tiros_a_puerta      INT DEFAULT 0,
    xg                  INT DEFAULT 0,
    xa                  INT DEFAULT 0,
    asistencias_tiro    INT DEFAULT 0,
    asistencias_segunda INT DEFAULT 0,

    -- Pases
    pases_totales                   INT DEFAULT 0,
    pases_completados               INT DEFAULT 0,
    pases_largos_totales            INT DEFAULT 0,
    pases_largos_completados        INT DEFAULT 0,
    pases_al_area_totales           INT DEFAULT 0,
    pases_al_area_completados       INT DEFAULT 0,
    pases_profundidad_totales       INT DEFAULT 0,
    pases_profundidad_completados   INT DEFAULT 0,
    pases_hacia_delante_totales     INT DEFAULT 0,
    pases_hacia_delante_completados INT DEFAULT 0,
    pases_hacia_atras_totales       INT DEFAULT 0,
    pases_hacia_atras_completados   INT DEFAULT 0,
    pases_recibidos                 INT DEFAULT 0,

    -- Regates y conducciones
    regates_totales      INT DEFAULT 0,
    regates_exitosos     INT DEFAULT 0,
    carreras_profundidad INT DEFAULT 0,

    -- Duelos
    duelos_totales            INT DEFAULT 0,
    duelos_ganados            INT DEFAULT 0,
    duelos_defensivos_totales INT DEFAULT 0,
    duelos_defensivos_ganados INT DEFAULT 0,
    duelos_ofensivos_totales  INT DEFAULT 0,
    duelos_ofensivos_ganados  INT DEFAULT 0,
    duelos_aereos_totales     INT DEFAULT 0,
    duelos_aereos_ganados     INT DEFAULT 0,

    -- Defensa
    intercepciones                  INT DEFAULT 0,
    despejes                        INT DEFAULT 0,
    balones_recuperados_campo_rival INT DEFAULT 0,
    balones_perdidos_campo_propio   INT DEFAULT 0,

    -- Disciplina
    tarjeta_amarilla BOOLEAN DEFAULT FALSE,
    tarjeta_roja     BOOLEAN DEFAULT FALSE,

    -- Campos tecnicos
    archivo_origen VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_partido) REFERENCES partidos  (id_partido),
    FOREIGN KEY (id_jugador) REFERENCES jugadores (id_jugador)
);

-- Notas manuales del entrenador (opcional)
CREATE TABLE notas_entrenador (
    id_nota        INT AUTO_INCREMENT PRIMARY KEY,
    id_partido     INT,
    id_jugador     INT,
    nota           DECIMAL(4, 2),
    comentario     TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_partido) REFERENCES partidos  (id_partido),
    FOREIGN KEY (id_jugador) REFERENCES jugadores (id_jugador)
);


-- ============================================================
-- DATOS INICIALES DEL SISTEMA
-- ============================================================

-- Posiciones del catalogo
INSERT INTO posiciones (codigo_posicion, nombre_posicion, linea) VALUES
    ('POR',  'Portero',               'defensa'),
    ('DFC',  'Central',               'defensa'),
    ('LD',   'Lateral Derecho',       'defensa'),
    ('LI',   'Lateral Izquierdo',     'defensa'),
    ('MCD',  'Mediocentro Defensivo', 'medio'),
    ('MC',   'Mediocentro',           'medio'),
    ('MCO',  'Mediocentro Ofensivo',  'medio'),
    ('EXTD', 'Extremo Derecho',       'ataque'),
    ('EXTI', 'Extremo Izquierdo',     'ataque'),
    ('DC',   'Delantero Centro',      'ataque');

-- Usuarios del sistema (contraseñas en texto plano para pruebas locales)
INSERT INTO usuarios (nombre_usuario, contraseña_hash, rol) VALUES
    ('admin',     '1234', 'admin'),
    ('mister',    '1234', 'entrenador'),
    ('analista1', '1234', 'analista');

-- Configuracion de pesos del modelo de scoring
INSERT INTO configuraciones_pesos (nombre_configuracion, activa) VALUES
    ('Modelo Estándar 2026', TRUE);

-- Peso de bloques para la posicion DC (de prueba)
INSERT INTO pesos_bloque_posicion (id_configuracion, codigo_posicion, porcentaje_ataque, porcentaje_construccion, porcentaje_defensa) VALUES
    (1, 'DC', 0.60, 0.30, 0.10);


-- ============================================================
-- DATOS DE PRUEBA (mock data temporada 2025/26)
-- ============================================================

-- Temporada activa
INSERT INTO temporadas (nombre, fecha_inicio, fecha_fin, activa) VALUES
    ('2025/26', '2025-08-15', '2026-06-30', TRUE);

-- Plantilla de jugadores
INSERT INTO jugadores (nombre, apellidos, alias, posicion_habitual, estado) VALUES
    ('Joseba',  'Bermejo',    '',        'POR',  'activo'),
    ('Unai',    'Ayala',      'Lunin',   'POR',  'activo'),
    ('Anartz',  'Amilibia',   'Ami',     'LD',   'activo'),
    ('Lucas',   'Sarasketa',  'Cabezon', 'LI',   'activo'),
    ('Oier',    'Llorente',   'Txo',     'DFC',  'activo'),
    ('Aitor',   'Larrañaga',  'Larra',   'DFC',  'activo'),
    ('Llorenc', 'Ferres',     'Ferreti', 'LI',   'activo'),
    ('Xavi',    'Pastor',     'Pastor',  'DFC',  'activo'),
    ('Oscar',   'Garcia',     'Osito',   'MC',   'activo'),
    ('Julen',   'Agirre',     'Jul',     'MCD',  'activo'),
    ('Ibai',    'Asenjo',     'Txejo',   'MCO',  'activo'),
    ('Asier',   'Santolaya',  'Santo',   'MCD',  'activo'),
    ('Jon',     'Lopez',      'Jonlo',   'DC',   'activo'),
    ('Marc',    'Delgado',    '',        'MC',   'activo'),
    ('Endika',  'Mateos',     'Bezana',  'EXTI', 'activo'),
    ('Ekaitz',  'Redondo',    'Eka',     'DC',   'activo'),
    ('Iker',    'Zubiria',    'Zubi',    'EXTD', 'activo'),
    ('Marcos',  'Sotelo',     'Sote',    'EXTD', 'activo'),
    ('Ekain',   'Etxebarria', 'Eka',     'DC',   'activo'),
    ('Hugo',    'Garcia',     'Hugillo', 'EXTI', 'activo');

-- Dorsales de la temporada 2025/26
INSERT INTO dorsales_jugador (id_dorsal, id_jugador, id_temporada, dorsal) VALUES
    (1,  1,  1, 1),
    (2,  2,  1, 13),
    (3,  3,  1, 2),
    (4,  4,  1, 3),
    (5,  5,  1, 4),
    (6,  6,  1, 5),
    (7,  7,  1, 22),
    (8,  8,  1, 23),
    (9,  9,  1, 8),
    (10, 10, 1, 6),
    (11, 11, 1, 14),
    (12, 12, 1, 16),
    (13, 13, 1, 17),
    (14, 14, 1, 21),
    (15, 15, 1, 7),
    (16, 16, 1, 9),
    (17, 17, 1, 10),
    (18, 18, 1, 11),
    (19, 19, 1, 18),
    (20, 20, 1, 19);

-- Calendario de partidos temporada 2025/26
INSERT INTO partidos (id_temporada, fecha, competicion, rival, local_visitante, goles_favor, goles_contra) VALUES
    (1, '2025-09-07', 'Segunda Federacion', 'SD Logroñes',       'visitante', 2, 0),
    (1, '2025-09-14', 'Segunda Federacion', 'Alfaro',            'local',     4, 3),
    (1, '2025-09-21', 'Segunda Federacion', 'Tudelano',          'visitante', 1, 0),
    (1, '2025-09-28', 'Segunda Federacion', 'Deportivo Alaves B','local',     0, 1),
    (1, '2025-10-05', 'Segunda Federacion', 'Naxara',            'visitante', 1, 1),
    (1, '2025-10-11', 'Segunda Federacion', 'Ejea',              'local',     2, 1),
    (1, '2025-10-18', 'Segunda Federacion', 'Beasain KE',        'visitante', 1, 2),
    (1, '2025-10-25', 'Segunda Federacion', 'Real Union Club',   'local',     0, 1),
    (1, '2025-11-02', 'Segunda Federacion', 'CD Ebro',           'visitante', 1, 1),
    (1, '2025-11-08', 'Segunda Federacion', 'Mutilvera',         'local',     1, 2),
    (1, '2025-11-16', 'Segunda Federacion', 'UD Logroñes',       'visitante', 0, 1),
    (1, '2025-11-22', 'Segunda Federacion', 'Sestao River',      'visitante', 1, 0),
    (1, '2025-11-29', 'Segunda Federacion', 'Deportivo Aragon',  'local',     1, 0),
    (1, '2025-12-06', 'Segunda Federacion', 'CD Basconia',       'visitante', 1, 1),
    (1, '2025-12-13', 'Segunda Federacion', 'SD Amorebieta',     'local',     0, 2),
    (1, '2025-12-20', 'Segunda Federacion', 'SD Gernika',        'visitante', 1, 2),
    (1, '2026-01-04', 'Segunda Federacion', 'Utebo',             'local',     1, 1),
    (1, '2026-01-11', 'Segunda Federacion', 'CD Alfaro',         'visitante', 0, 0),
    (1, '2026-01-17', 'Segunda Federacion', 'SD Logroñes',       'local',     0, 0),
    (1, '2026-01-24', 'Segunda Federacion', 'Deportivo Alaves B','visitante', 2, 0),
    (1, '2026-01-31', 'Segunda Federacion', 'Tudelano',          'local',     1, 0),
    (1, '2026-02-08', 'Segunda Federacion', 'Deportivo Aragon',  'visitante', 0, 1),
    (1, '2026-02-15', 'Segunda Federacion', 'CD Basconia',       'local',     1, 3),
    (1, '2026-02-21', 'Segunda Federacion', 'Real Union Club',   'visitante', 2, 1),
    (1, '2026-02-28', 'Segunda Federacion', 'SD Gernika',        'local',     3, 2),
    (1, '2026-03-07', 'Segunda Federacion', 'Sestao River',      'local',     2, 1),
    (1, '2026-03-14', 'Segunda Federacion', 'Mutilvera',         'visitante', 4, 0),
    (1, '2026-03-21', 'Segunda Federacion', 'Beasain KE',        'local',     0, 1);

-- Puntuaciones de prueba (partido 1 - SD Logroñes)
INSERT INTO puntuaciones (id_partido, id_jugador, posicion_evaluada, puntuacion_ataque, puntuacion_construccion, puntuacion_defensa, factor_minutos, puntuacion_final) VALUES
    (1, 13, 'DC',   7.20, 5.10, 3.40, 1.00, 7.50),
    (1, 16, 'DC',   6.80, 4.90, 2.10, 0.95, 6.90),
    (1,  9, 'MC',   4.50, 7.80, 5.20, 1.00, 6.40),
    (1, 10, 'MCD',  3.20, 6.90, 7.10, 1.00, 6.10),
    (1,  3, 'LD',   2.10, 5.40, 7.80, 0.90, 5.80),
    (1,  5, 'DFC',  1.80, 4.20, 8.30, 1.00, 5.60),
    (1,  6, 'DFC',  1.50, 3.90, 7.60, 0.85, 5.20),
    (1, 15, 'EXTI', 6.10, 5.30, 2.80, 0.80, 5.00);