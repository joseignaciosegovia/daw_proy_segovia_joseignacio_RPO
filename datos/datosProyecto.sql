-- Tabla clientes

INSERT INTO clientes VALUES 
    ('marLop@gmail.com', 'marL1234', 'María López', '87182344I', 655871025, LOAD_FILE('/imagenes/perfil1.png'), '$2y$12$GRoxOUyMK.YyXPot2/2F..7Q8zTrzrlY4y2k9CZMNmPFgoOiMHq6y', 0),
    ('ferSan@gmail.com', 'ferS1234', 'Fernando Sanz', '11298419O', 664788795, LOAD_FILE('/imagenes/perfil2.png'), '$2y$10$9T21merzMQpflmLCKtA0v.nlFN05ci5S5042b8BPbD6S4VLQgmOtG', 0),
    ('antGar@gmail.com', 'antG1234', 'Antonio García', '744391209U', 697874169, LOAD_FILE('/imagenes/perfil3.png'), '$2y$10$vgm9646Cb6zlRNT6NKFGeeI4/0rihPFbtv3Sr1y8LW6rE.FyZ/HhC', 0),
    ('ferLui@gmail.com', 'ferL1234', 'Fernanda Luisa', '11298437H', 604975301, LOAD_FILE('/imagenes/perfil4.png'), '$2y$10$9T21merzMQpflmLCKtA0v.nlFN05ci5S5042b8BPbD6S4VLQgmOtG', 0);

-- Tabla gestores

INSERT INTO gestores VALUES 
    ('adminMer@gmail.com', 'admMer', 'Mercedes Puertas', '77319284T', 661281938, LOAD_FILE('/imagenes/perfil1.png'), 0);
    ('adminAnton@gmail.com', 'admAn', 'Antonio Castillo', '71822198U', 617291009, LOAD_FILE('/imagenes/perfil2.png'), 0);

-- Tabla pistas

INSERT INTO pistas (nombre, localizacion, precioReserva) VALUES 
    ('Campo fútbol 7', 'Ciudad Deportiva', 8.25),
    ('Campo fútbol 11', 'Ciudad Deportiva', 9),
    ('Pádel', 'Ciudad Deportiva', 7.30),
    ('Atletismo', 'Ciudad Deportiva', 8),
    ('Multiusos', 'Ciudad Deportiva', 5),
    ('Pista interna', 'Polideportivo', 4),
    ('Pista externa', 'Polideportivo', 4);