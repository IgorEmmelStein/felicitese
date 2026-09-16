-- Inicialização do Banco de Dados para o Felicite-se (MariaDB / MySQL)

CREATE DATABASE IF NOT EXISTS `clube_felicitese` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `clube_felicitese`;

-- 1. Tabela de Categorias
CREATE TABLE IF NOT EXISTS `categorias` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabela de Usuários (Administradores)
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `senha` VARCHAR(255) NOT NULL,
    `funcao` VARCHAR(50) DEFAULT 'Administrador',
    `data_criacao` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabela de Posts / Publicações
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(255) NOT NULL,
    `conteudo` LONGTEXT NOT NULL,
    `imagem` VARCHAR(255) NULL,
    `pdf_anexo` VARCHAR(255) NULL,
    `categoria_id` INT NULL,
    `autor_id` INT NULL,
    `data_criacao` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_posts_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_posts_autor` FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserção de Categorias Padrão
INSERT IGNORE INTO `categorias` (`id`, `nome`) VALUES
(1, 'Psicologia e Saúde Mental'),
(2, 'Vivências Lúdicas'),
(3, 'Artigos Científicos'),
(4, 'Oficinas e Eventos');

-- Inserção do Usuário Administrador Padrão (login: admin@ifsul.edu.br / senha: admin123)
INSERT IGNORE INTO `usuarios` (`id`, `nome`, `email`, `senha`, `funcao`) VALUES
(1, 'Administrador Felicite-se', 'admin@ifsul.edu.br', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.qH129.GSmrC5Uhy4y1r0hR8RzM7K.iK', 'Administrador');

