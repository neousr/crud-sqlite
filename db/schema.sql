-- DDL
CREATE TABLE user (
  id_user INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  apellido VARCHAR(50) NOT NULL,
  nombre VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL UNIQUE,
  telefono VARCHAR(50) NOT NULL,
  creado DATETIME NOT NULL,
  modificado DATETIME NOT NULL,
  eliminado INTEGER NOT NULL DEFAULT 0 CHECK(eliminado IN(0, 1))
);
CREATE INDEX idx_user_eliminado ON user(eliminado);
CREATE INDEX idx_user_email ON user(email);