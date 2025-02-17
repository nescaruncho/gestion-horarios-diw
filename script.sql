CREATE DATABASE gestion_ciclos;

USE gestion_ciclos;

CREATE TABLE USUARIO (
    id_user int primary key auto_increment not null,
    name varchar(50) not null,
    lastname varchar(50) not null,
    email varchar(100) not null UNIQUE,
    dni char(9) not null UNIQUE,
    login varchar(50) not null UNIQUE,
    password varchar(255) not null,
    rol ENUM('administrador', 'alumno') NOT NULL
);

CREATE TABLE CICLO (
    id_ciclo int primary key auto_increment not null,
    codigo varchar(50) not null UNIQUE,
    name varchar(100) not null
);

CREATE TABLE PROFESOR (
    id_profesor int primary key auto_increment not null,
    name varchar(50) not null,
    lastname varchar(50) not null,
    email varchar(100) not null UNIQUE
);

CREATE TABLE USUARIO_CICLO (
    id_usuario_ciclo int primary key auto_increment not null,
    id_user int not null,
    id_ciclo int not null,
    fecha_matricula DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (id_user) REFERENCES USUARIO(id_user) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_ciclo) REFERENCES CICLO(id_ciclo) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY unique_usuario_ciclo (id_user, id_ciclo)
);

CREATE TABLE MODULO (
    id_modulo int primary key auto_increment not null,
    name varchar(100) not null,
    curso ENUM('1º', '2º') not null,
    horas_totales int,
    id_ciclo int,
    id_profesor int,
    FOREIGN KEY (id_ciclo) REFERENCES CICLO(id_ciclo) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (id_profesor) REFERENCES PROFESOR(id_profesor) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE USER_MODULO (
    id_user_modulo int primary key auto_increment not null,
    id_user int not null,
    id_modulo int not null,
    fecha_matricula DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (id_user) REFERENCES USUARIO(id_user) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_modulo) REFERENCES MODULO(id_modulo) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_user_modulo (id_user, id_modulo)
);

CREATE TABLE SESION (
    id_sesiones int primary key auto_increment not null,
    hora_inicio time not null,
    hora_fin time not null,
    dia_semana ENUM('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes') NOT NULL,
    aula VARCHAR(50),
    id_modulo int not null,
    FOREIGN KEY (id_modulo) REFERENCES MODULO(id_modulo) ON DELETE CASCADE ON UPDATE CASCADE
);