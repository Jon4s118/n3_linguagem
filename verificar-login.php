<?php
// Autenticação: garante que existe um usuário logado nesta sessão.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Páginas protegidas não devem ficar em cache: após o logout, o botão
// "Voltar" do navegador não pode reexibir uma tela que exigia login.
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php', true, 303);
    exit;
}

// Autorização: funções de apoio para checar o PERFIL no servidor.
function usuarioEhAdministrador(): bool
{
    return ($_SESSION['perfil'] ?? '') === 'administrador';
}

// Interrompe a requisição com 403 se o perfil logado não estiver na lista.
function exigirPerfil(array $perfisPermitidos): void
{
    if (!in_array($_SESSION['perfil'] ?? '', $perfisPermitidos, true)) {
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">'
            . '<title>Acesso negado</title><link rel="stylesheet" href="css/estilo.css"></head>'
            . '<body><main class="conteudo"><h1>Acesso negado</h1>'
            . '<p role="alert">Seu perfil não tem permissão para executar esta operação.</p>'
            . '<p><a class="botao-secundario" href="produtos.php">Voltar para Produtos</a></p>'
            . '</main></body></html>';
        exit;
    }
}
