DROP DATABASE sistema_scoring_futbol;
CREATE DATABASE sistema_scoring_futbol;
USE sistema_scoring_futbol;

-- Tabla para usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contraseña_hash VARCHAR(255) NOT NULL,   -- Si va ser hasheada la dejo con los caracteres maximos y ya
    rol ENUM('admin', 'entrenador', 'analista') NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para organizar por temporadas
CREATE TABLE temporadas (
    id_temporada INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    activa BOOLEAN DEFAULT FALSE
);

-- Tabla de jugadores
CREATE TABLE jugadores (
    id_jugador INT UNIQUE PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    alias VARCHAR(50),
    posicion_habitual VARCHAR(50),
    estado ENUM('activo', 'lesionado', 'baja') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para el dorsal de cada jugador
CREATE TABLE dorsales_jugador (
    id_dorsal INT UNIQUE PRIMARY KEY,
    id_jugador INT,
    id_temporada INT,
    dorsal INT UNIQUE,
    FOREIGN KEY (id_jugador) REFERENCES jugadores(id_jugador) ON DELETE CASCADE,
    FOREIGN KEY (id_temporada) REFERENCES temporadas(id_temporada) ON DELETE CASCADE
);

-- Tabla para la gestion de cada partido
CREATE TABLE partidos (
    id_partido INT AUTO_INCREMENT PRIMARY KEY, -- O unique si podria meter
    id_temporada INT,
    fecha DATE NOT NULL,
    competicion VARCHAR(100),
    rival VARCHAR(100),
    local_visitante ENUM('local', 'visitante'), -- Uno u otro, asi es mas facil que el varchar
    goles_favor INT DEFAULT 0,
    goles_contra INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_temporada) REFERENCES temporadas(id_temporada)
);

-- Tabla para las puntuaciones de los jugadores
CREATE TABLE puntuaciones (
    id_puntuacion INT AUTO_INCREMENT PRIMARY KEY,
    id_partido INT,
    id_jugador INT,
    posicion_evaluada VARCHAR(50),  -- No se si por numero o por texto
    puntuacion_ataque DECIMAL(4,2),
    puntuacion_construccion DECIMAL(4,2),
    puntuacion_defensa DECIMAL(4,2),
    puntuacion_final DECIMAL(4,2),
    explicacion_positiva TEXT,
    explicacion_negativa TEXT,
    version_modelo VARCHAR(50),   -- Esto que es ????
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_partido) REFERENCES partidos(id_partido),
    FOREIGN KEY (id_jugador) REFERENCES jugadores(id_jugador)
);

-- Tabla para las posiciones de los jugadores
CREATE TABLE posiciones (
    codigo_posicion VARCHAR(5) PRIMARY KEY, 
    nombre_posicion VARCHAR(50) NOT NULL,
    linea ENUM('defensa', 'medio', 'ataque') NOT NULL
);

-- Tabla de pesos
CREATE TABLE configuraciones_pesos (
    id_configuracion INT AUTO_INCREMENT PRIMARY KEY,  
    nombre_configuracion VARCHAR(100) NOT NULL,
    activa BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para las notas del entrenador
CREATE TABLE notas_entrenador (
    id_nota INT AUTO_INCREMENT UNIQUE PRIMARY KEY,
    id_partido INT,
    id_jugador INT,
    nota DECIMAL(4,2),
    comentario TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_partido) REFERENCES partidos(id_partido),
    FOREIGN KEY (id_jugador) REFERENCES jugadores(id_jugador)
);

-- Tabla de los lesionados
CREATE TABLE lesiones (
    id_lesion INT AUTO_INCREMENT PRIMARY KEY,
    id_jugador INT,
    fecha_inicio DATE NOT NULL,  -- Por si la fecha de cuando le paso, ha sido dias anteriores, asi no pongo el timestamp
    fecha_fin DATE,
    tipo_lesion VARCHAR(100),
    gravedad ENUM('leve', 'moderada', 'grave'),   -- Asi igual mejor que un VARCHAR
    fecha_prevista_retorno DATE,
    observaciones TEXT, 
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_jugador) REFERENCES jugadores(id_jugador)
);

-- Tabla de pesos de bloque por posicion
CREATE TABLE pesos_bloque_posicion (
    id_peso INT AUTO_INCREMENT PRIMARY KEY,
    id_configuracion INT,
    codigo_posicion VARCHAR(10),
    porcentaje_ataque DECIMAL(5,2),  
    porcentaje_construccion DECIMAL(5,2),
    porcentaje_defensa DECIMAL(5,2),
    FOREIGN KEY (id_configuracion) REFERENCES configuraciones_pesos(id_configuracion),
    FOREIGN KEY (codigo_posicion) REFERENCES posiciones(codigo_posicion)
);

CREATE TABLE pesos_metrica_posicion (
    id_peso_metrica INT AUTO_INCREMENT PRIMARY KEY,
    id_configuracion INT,
    codigo_posicion VARCHAR(10),
    bloque ENUM('ataque', 'construccion', 'defensa'),
    clave_metrica VARCHAR(50),
    porcentaje DECIMAL(5,2),
    penaliza BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_configuracion) REFERENCES configuraciones_pesos(id_configuracion),
    FOREIGN KEY (codigo_posicion) REFERENCES posiciones(codigo_posicion)
);

CREATE TABLE estadisticas_jugador_partido (
	-- Campos generales
    id_estadistica INT AUTO_INCREMENT PRIMARY KEY,
    id_partido INT,
    id_jugador INT,
    posicion_jugada VARCHAR(50),
    minutos_jugados INT,
    
    -- Estadisticas ofensivas
    goles INT DEFAULT 0,
    asistencias INT DEFAULT 0,
    tiros_totales INT DEFAULT 0,
    tiros_a_puerta INT DEFAULT 0,
    xg INT DEFAULT 0,
    xa INT DEFAULT 0,
    asistencias_tiro INT DEFAULT 0,
    asistencias_segundo INT DEFAULT 0,
    
	-- Pases 
    pases_totales INT DEFAULT 0,
    pases_completados INT DEFAULT 0,
    pases_largos_completados INT DEFAULT 0,
    pases_al_area_totales INT DEFAULT 0,
    pases_al_area_completados INT DEFAULT 0,
    pases_profundidad_totales INT DEFAULT 0,
    pases_profundidad_completados INT DEFAULT 0,
	pases_hacia_delante_totales INT DEFAULT 0,
    pases_hacia_delante_completados INT DEFAULT 0,
    pases_hacia_atras_totales INT DEFAULT 0,
    pases_hacia_atras_completados INT DEFAULT 0,
    pases_recibidos INT DEFAULT 0,
    
    -- Regates y conducciones
    regates_totales INT DEFAULT 0,
    regates_exitosos INT DEFAULT 0,
    carreras_profundidad INT DEFAULT 0,
    
    -- Duelos
    duelos_totales INT DEFAULT 0,
    duelos_ganados INT DEFAULT 0,
    duelos_defensivos_totales INT DEFAULT 0,
    duelos_defensivos_ganados INT DEFAULT 0,
    duelos_ofensivos_totales INT DEFAULT 0,
    duelos_ofensivos_ganados INT DEFAULT 0,
    duelos_aereos_totales INT DEFAULT 0,
    duelos_aereos_ganados INT DEFAULT 0,
    
    -- Defensa
    intercepciones INT DEFAULT 0,
    despejes INT DEFAULT 0,
    balones_recuperados_campo_rival INT DEFAULT 0,
    balones_recuperados_campo_propio INT DEFAULT 0,
    
    -- Disciplina
    tarjeta_amarilla BOOLEAN DEFAULT FALSE,
    tarjeta_roja BOOLEAN DEFAULT FALSE,
    
    -- Campos tecnicos
    archivo_origen VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_partido) REFERENCES partidos(id_partido),
    FOREIGN KEY (id_jugador) REFERENCES jugadores(id_jugador)
);


-- INSERTS de prueba

INSERT INTO posiciones (codigo_posicion, nombre_posicion, linea) VALUES 
('DFC', 'Defensa Central', 'defensa'),
('MC', 'Mediocentro', 'medio'),
('EXT', 'Extremo', 'ataque'),
('DC', 'Delantero Centro', 'ataque');


INSERT INTO configuraciones_pesos (nombre_configuracion, activa) VALUES 
('Modelo Estándar 2026', TRUE);

INSERT INTO pesos_bloque_posicion (id_configuracion, codigo_posicion, porcentaje_ataque, porcentaje_construccion, porcentaje_defensa) 
VALUES (1, 'DC', 0.60, 0.30, 0.10);

INSERT INTO temporadas (nombre, fecha_inicio, fecha_fin, activa) 
VALUES ('2025/26', '2025-08-15', '2026-06-30', TRUE);

INSERT INTO jugadores (id_jugador, nombre, apellidos, alias, posicion_habitual, estado) VALUES 
(1, 'Lamine', 'Yamal', 'Lamine', 'EXT', 'activo'),
(2, 'Robert', 'Lewandowski', 'Lewy', 'DC', 'activo');

INSERT INTO dorsales_jugador (id_dorsal, id_jugador, id_temporada, dorsal) VALUES 
(1, 1, 1, 19),
(2, 2, 1, 9);

INSERT INTO partidos (id_temporada, fecha, competicion, rival, local_visitante, goles_favor, goles_contra) 
VALUES (1, '2026-02-14', 'LaLiga', 'Getafe', 'local', 2, 0);