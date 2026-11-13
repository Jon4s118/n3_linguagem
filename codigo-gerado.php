<?php
session_start();
require_once "conexao.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php', true, 303);
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    $_SESSION['erro_login'] = 'Preencha e-mail e senha.';
    header('Location: login.php', true, 303);
    exit;
}

try {
    $comando = $pdo->prepare("
        SELECT id, nome, senha_hash, perfil, ativo
        FROM usuarios
        WHERE email = :email
        LIMIT 1
    ");
    $comando->execute([':email' => $email]);
    $usuario = $comando->fetch();

    if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
        $_SESSION['erro_login'] = 'E-mail ou senha incorretos.';
        header('Location: login.php', true, 303);
        exit;
    }

    if ((int)$usuario['ativo'] !== 1) {
        $_SESSION['erro_login'] = 'Usuário inativo.';
        header('Location: login.php', true, 303);
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['usuario_id'] = (int)$usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_perfil'] = $usuario['perfil'];

    header('Location: index.php', true, 303);
    exit;

} catch (PDOException $e) {
    $_SESSION['erro_login'] = 'Erro ao autenticar. Tente novamente.';
    header('Location: login.php', true, 303);
    exit;
}