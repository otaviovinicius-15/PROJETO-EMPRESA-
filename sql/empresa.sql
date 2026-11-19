SET NAMES utf8mb4;
DROP DATABASE IF EXISTS empresa;
CREATE DATABASE empresa CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE empresa;

-- DROP TABLES
DROP TABLE IF EXISTS movimentacao CASCADE;
DROP TABLE IF EXISTS produto CASCADE;
DROP TABLE IF EXISTS `user` CASCADE;

-- tipo: ADMIN (acesso total) | ALMOXARIFE (consulta e movimenta o estoque)
CREATE TABLE `user` (
    id_user INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo VARCHAR(10) NOT NULL DEFAULT 'ALMOXARIFE' CHECK (tipo IN ('ADMIN', 'ALMOXARIFE'))
) ENGINE=InnoDB;

CREATE TABLE produto (
    id_produto INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    quantidade_estoque INTEGER NOT NULL CHECK (quantidade_estoque >= 0),
    valor_unitario NUMERIC(10,2) NOT NULL,
    data_cadastro DATE NOT NULL DEFAULT (CURRENT_DATE)
) ENGINE=InnoDB;

CREATE TABLE movimentacao (
    id_movimentacao INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    tipo VARCHAR(10) NOT NULL CHECK (tipo IN ('ENTRADA', 'SAIDA')),
    quantidade INTEGER NOT NULL CHECK (quantidade > 0),
    id_produto INTEGER NOT NULL,
    datetime_movimentacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_user INTEGER NOT NULL,
    FOREIGN KEY (id_produto) REFERENCES produto(id_produto),
    FOREIGN KEY (id_user) REFERENCES `user`(id_user)
) ENGINE=InnoDB;

-- Senhas (password_hash): admin@saep.com / admin123  |  almoxarife@saep.com / almox123
INSERT INTO `user` (nome, email, senha, tipo) VALUES
('Administrador', 'admin@saep.com', '$2y$10$QOHy0UwrMjozUF8Samt31eyNWUWFXoGpXfuaZtZkbtCU.RuyVhot.', 'ADMIN'),
('Almoxarife', 'almoxarife@saep.com', '$2y$10$9q5zhfV34HBqtpQaG0xzpu/c8VRmBPHm/1mNStpfCtSm1f4Ux7ZI2', 'ALMOXARIFE');

INSERT INTO produto (nome, quantidade_estoque, valor_unitario) VALUES
('Amaciante de roupas', 100, 0.50),
('Detergente', 25, 18.90),
('Sabão em pó', 40, 22.50);

-- produtos extras para demonstrar a paginação e o filtro por data
INSERT INTO produto (nome, quantidade_estoque, valor_unitario, data_cadastro) VALUES
('Água sanitária', 30, 6.90, '2026-01-10'),
('Desinfetante', 18, 9.75, '2026-02-15'),
('Esponja de louça', 60, 2.30, '2026-03-20'),
('Luva de borracha', 12, 7.40, '2026-04-05'),
('Papel toalha', 45, 11.20, '2026-05-12');


INSERT INTO movimentacao (tipo, quantidade, id_produto, datetime_movimentacao, id_user) VALUES
('ENTRADA', 50, 1, NOW(), 1),
('SAIDA', 10, 1, NOW(), 1),
('SAIDA', 5, 2, NOW(), 1);

select * from produto;
