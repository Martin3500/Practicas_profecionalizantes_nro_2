# Creacion de la tabla de estados de clientes 
# Se deben definir valores por defecto en la tabla antes de la creacion de la tabla clientes
-- 1. TABLAS BASE Y CATÁLOGOS INDEPENDIENTES

CREATE TABLE estado_cliente (
    ID_est_clientes INT AUTO_INCREMENT PRIMARY KEY,
    Estado VARCHAR(150) NOT NULL,
    Descripcion VARCHAR(500)
);

INSERT INTO estado_cliente (Estado, Descripcion) VALUES
('Activo', 'El cliente está habilitado y operando normalmente.'),
('Inactivo', 'El cliente ha sido dado de baja o no registra actividad.'),
('Pausado', 'El cliente ha pausado temporalmente su servicio o se decidio pausarlo.'),
('Suspensión', 'El cliente se encuentra suspendido de forma preventiva o por falta de pago.');

CREATE TABLE disciplinas (
    id_disciplina INT AUTO_INCREMENT PRIMARY KEY,
    disciplina VARCHAR(150) NOT NULL,
    descripcion TEXT
);

CREATE TABLE entrenadores (
    id_entrenador INT AUTO_INCREMENT PRIMARY KEY,
    DNI INT(8)NOT NULL UNIQUE,
	 nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20)
);

CREATE TABLE tarifas (
    id_tarifa INT AUTO_INCREMENT PRIMARY KEY,
    id_entrenador INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    CONSTRAINT fk_tarifas_entrenadores FOREIGN KEY (id_entrenador)
        REFERENCES entrenadores(id_entrenador)
);


-- 2. ENTIDADES PRINCIPALES Y RELACIONES INTERMEDIAS

CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    DNI INT(8) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    id_estado INT NOT NULL,
    CONSTRAINT fk_cliente_estado FOREIGN KEY (id_estado)
        REFERENCES estado_cliente(ID_est_clientes)
);

CREATE TABLE entrenadoresxdisciplinas (
    id_entrenador_disciplina INT AUTO_INCREMENT PRIMARY KEY,
    id_entrenador INT NOT NULL,
    id_disciplina INT NOT NULL,
    CONSTRAINT fk_exd_entrenador FOREIGN KEY (id_entrenador)
        REFERENCES entrenadores(id_entrenador),
    CONSTRAINT fk_exd_disciplina FOREIGN KEY (id_disciplina)
        REFERENCES disciplinas(id_disciplina)
);


-- 3. HISTORIALES Y REGISTROS DE ACTIVIDAD

CREATE TABLE historia_de_asistencia (
    id_asistencia INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    asistio TINYINT(1) DEFAULT 1,
    CONSTRAINT fk_asistencia_cliente FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
);

CREATE TABLE historial_de_pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_tarifa INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pago_cliente FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente),
    CONSTRAINT fk_pago_tarifa FOREIGN KEY (id_tarifa)
        REFERENCES tarifas(id_tarifa)
);
