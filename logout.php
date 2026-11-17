<?php
session_start();

// 1) Limpa os dados da sessão em memória.
$_SESSION = [];

// 2) Apaga o cookie de sessão usando os mesmos parâmetros com que foi criado.
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// 3) Destrói a sessão no servidor.
session_destroy();

header('Location: login.php', true, 303);
exit;
