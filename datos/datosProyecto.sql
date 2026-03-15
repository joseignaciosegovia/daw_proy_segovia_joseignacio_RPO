-- Tabla clientes

INSERT INTO clientes VALUES 
    ('marLop@gmail.com','marL1234','María López', 655871025, '$2y$12$GRoxOUyMK.YyXPot2/2F..7Q8zTrzrlY4y2k9CZMNmPFgoOiMHq6y', 0),
    ('ferSan@gmail.com','ferS1234','Fernando San', 664788795, '$2y$10$9T21merzMQpflmLCKtA0v.nlFN05ci5S5042b8BPbD6S4VLQgmOtG', 0),
    ('antGar@gmail.com','antG1234','Antonio García', 697874169, '$2y$10$vgm9646Cb6zlRNT6NKFGeeI4/0rihPFbtv3Sr1y8LW6rE.FyZ/HhC', 0),
    ('ferLui@gmail.com','ferL1234','Fernanda Luisa', 604975301, '$2y$10$9T21merzMQpflmLCKtA0v.nlFN05ci5S5042b8BPbD6S4VLQgmOtG', 0);

-- Tabla gestores

INSERT INTO gestores VALUES 
    ('adminAnton@gmail.com', 'admAn'),
    ('adminMer@gmail.com', 'admMer'),
    ('admPrueba@gmail.com', 'prueba');

-- Tabla administradores

INSERT INTO administradores VALUES 
    ('administradorn@gmail.com', 'admin1');

-- Tabla calendarios

INSERT INTO pistas VALUES 
    ('Campo fútbol 7', 'Ciudad Deportiva', '{"adultoNormal": 16.50, "adultoConLuz": 24.50, "menorNormal": 8.25, "menorConLuz": 12.25}'),
    ('Campo fútbol 11', 'Ciudad Deportiva', '{"adultoNormal": 18, "adultoConLuz": 30, "menorNormal": 9, "menorConLuz": 15}'),
    ('Pádel', 'Ciudad Deportiva', '{"adultoNormal": 4, "adultoConLuz": 7.30, "menorNormal": 2, "menorConLuz": 3.85}'),
    ('Atletismo', 'Ciudad Deportiva', '{"adultoNormal": 12, "adultoConLuz": 15, "menorNormal": 8, "menorConLuz": 12}'),
    ('Multiusos', 'Ciudad Deportiva', '{"adultoNormal": 3, "adultoConLuz": 5, "menorNormal": 2, "menorConLuz": 4}'),
    ('Pista interna', 'Polideportivo', '4'),
    ('Pista externa', 'Polideportivo', '4');

-- Tabla reservas

INSERT INTO reservas VALUES 
    ('2025-06-18', '18:00:00', '19:00:00', 'Campo fútbol 7', 'antGar@gmail.com', 'Reserva realizada por un cliente'),
    ('2025-06-19', '15:00:00', '16:00:00', 'Campo fútbol 11', 'ferSan@gmail.com', 'Reserva realizada por un cliente'),
    ('2025-06-18', '18:00:00', '19:00:00', 'Pádel', 'marLop@gmail.com', 'Reserva realizada por un cliente'),
    ('2025-06-20', '18:00:00', '19:00:00', 'Pista interna', 'marLop@gmail.com', 'Reserva realizada por un cliente');

-- Tabla sugerencias_incidencias

INSERT INTO sugerencias_incidencias VALUES 
('2025-10-06', 'La página va lenta', 'marLop@gmail.com'),
('2025-10-15', 'La página da error', 'antGar@gmail.com'),
('2025-11-08', 'No veo mis reservas', 'antGar@gmail.com');