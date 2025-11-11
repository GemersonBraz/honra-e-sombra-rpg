-- Honra e Sombra RPG Database Schema
-- Parte 1: Autenticação básica

DROP DATABASE IF EXISTS honra_sombra_rpg;
CREATE DATABASE honra_sombra_rpg CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE honra_sombra_rpg;

-- Tabela de usuários
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('player', 'admin') NOT NULL DEFAULT 'player',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_login TIMESTAMP NULL,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir admin padrão (senha: admin123)
INSERT INTO users (nome, email, senha, tipo) VALUES 
('Admin Sistema', 'admin@honrasombra.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Índices para otimização
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_tipo ON users(tipo);
CREATE INDEX idx_users_ultimo_login ON users(ultimo_login);