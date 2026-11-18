-- Estoque Fácil — N3 — Criação da tabela `usuarios` (autenticação e controle de acesso)
-- Use no phpMyAdmin, com o banco estoque_ads selecionado.
-- Não apaga produtos nem movimentações. Senhas ficam em HASH (password_hash/bcrypt), nunca em texto puro.
-- (Para um banco que já tem a tabela antiga `usuarios` com a coluna `senha`, use o bloco de ajuste no final de tabelas.sql.)

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario`      INT(11)      NOT NULL AUTO_INCREMENT,
  `nome`            VARCHAR(150) NOT NULL,
  `email`           VARCHAR(80)  NOT NULL,
  `senha_hash`      VARCHAR(255) NOT NULL,
  `perfil`          ENUM('administrador','almoxarife') NOT NULL DEFAULT 'almoxarife',
  `ativo`           TINYINT(1)   NOT NULL DEFAULT 1,
  `criado_em`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_login_em` DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uk_usuarios_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contas FICTÍCIAS de teste:
--   admin@teste.com  / Admin@123  (administrador)
--   almox@teste.com  / Almox@123  (almoxarife)
INSERT IGNORE INTO `usuarios` (`nome`, `email`, `senha_hash`, `perfil`, `ativo`) VALUES
('Administrador Teste', 'admin@teste.com', '$2y$10$FBMm/2cL2R4HQA8SbJjCdOdzwR3lY2rC1vHGcgR85DjTyofZuUgZG', 'administrador', 1),
('Almoxarife Teste',    'almox@teste.com', '$2y$10$6/0xPRiKpiDJE3.ZERtoWOhPS1uYDIOGfdkuTxh6FOiW7GI0Gt8x2', 'almoxarife',    1);
