-- pruebas
-- entrada de datos de catalogos independientes 1)

-- 1.1 Disciplinas

INSERT INTO disciplinas (disciplina, descripcion) VALUES
('CrossFit', 'Entrenamiento de alta intensidad'),
('Spinning', 'Ciclismo de interior guiado'),
('Yoga', 'Clases de flexibilidad y relajación');

-- 1.2 Entrenadores (Se identifica de forma única por DNI)
INSERT INTO entrenadores (DNI, nombre, apellido, email, telefono) VALUES
(30111222, 'Carlos', 'Gómez', 'carlos.gomez@email.com', '1122334455'),
(31222333, 'Laura', 'Martínez', 'laura.martinez@email.com', '1166778899');

-- entrada de datos de catalogos principales y relaciones intermedias 2)

-- 2.1 Tarifas (Busca id_entrenador por su DNI)
INSERT INTO tarifas (id_entrenador, nombre, monto, descripcion) VALUES
(
    (SELECT id_entrenador FROM entrenadores WHERE DNI = 30111222),
    'Tarifa Personalizada CrossFit',
    15000.00,
    'Plan mensual de 3 clases semanales'
),
(
    (SELECT id_entrenador FROM entrenadores WHERE DNI = 31222333),
    'Tarifa Integral Spinning',
    12000.00,
    'Plan mensual pase libre'
);

-- 2.2 Clientes (Busca id_estado por la columna Estado)
INSERT INTO clientes (DNI, nombre, apellido, email, telefono, id_estado) VALUES
(
    40555666, 'Juan', 'Pérez', 'juan.perez@email.com', '1144556677',
    (SELECT ID_est_clientes FROM estado_cliente WHERE Estado = 'Activo')
),
(
    41777888, 'María', 'López', 'maria.lopez@email.com', '1188990011',
    (SELECT ID_est_clientes FROM estado_cliente WHERE Estado = 'Activo')
);

-- 2.3 Entrenadores x Disciplinas (Busca por DNI del entrenador y nombre de la disciplina)
INSERT INTO entrenadoresxdisciplinas (id_entrenador, id_disciplina) VALUES
(
    (SELECT id_entrenador FROM entrenadores WHERE DNI = 30111222),
    (SELECT id_disciplina FROM disciplinas WHERE disciplina = 'CrossFit')
),
(
    (SELECT id_entrenador FROM entrenadores WHERE DNI = 31222333),
    (SELECT id_disciplina FROM disciplinas WHERE disciplina = 'Spinning')
);

-- Registros de Actividad 3)
-- 3.1 Historia de Asistencia (Busca id_cliente por DNI)
INSERT INTO historia_de_asistencia (id_cliente, fecha_hora, asistio) VALUES
(
    (SELECT id_cliente FROM clientes WHERE DNI = 40555666),
    NOW(),
    1
);

-- 3.2 Historial de Pagos (Busca id_cliente por DNI e id_tarifa por el nombre de la tarifa)
INSERT INTO historial_de_pagos (id_cliente, id_tarifa, monto, fecha_pago) VALUES
(
    (SELECT id_cliente FROM clientes WHERE DNI = 40555666),
    (SELECT id_tarifa FROM tarifas WHERE nombre = 'Tarifa Personalizada CrossFit'),
    15000.00,
    NOW()
);
