
<?php
session_start();
require_once "conexao.php";

// Só aceita o envio do formulário (POST).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php', true, 303);
    exit;
}

$email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
$senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';

// Mensagem única: não revela se o e-mail existe ou se a senha é que está errada.
function falharLogin(string $mensagem = 'E-mail ou senha inválidos.'): void
{
    $_SESSION['erro_login'] = $mensagem;
    header('Location: login.php', true, 303);
    exit;
}

if ($email === '' || $senha === '') {
    falharLogin();
}

try {
    // Consulta preparada: o e-mail nunca é concatenado ao SQL.
    $comando = $pdo->prepare('SELECT id_usuario, nome, senha_hash, perfil, ativo FROM usuarios WHERE email = :email LIMIT 1');
    $comando->execute(['email' => $email]);
    $usuario = $comando->fetch();
} catch (PDOException $erro) {
    error_log('Erro no login: ' . $erro->getMessage());
    falharLogin('Não foi possível acessar o banco de dados. Tente novamente.');
}

// password_verify compara a senha digitada com o hash guardado no banco.
if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
    falharLogin();
}

if ((int) $usuario['ativo'] !== 1) {
    falharLogin('Usuário inativo. Procure o administrador.');
}

// Novo ID de sessão após autenticar: evita fixação de sessão.
session_regenerate_id(true);
$_SESSION['usuario_id'] = (int) $usuario['id_usuario'];
$_SESSION['nome'] = $usuario['nome'];
$_SESSION['perfil'] = $usuario['perfil']; // 'administrador' ou 'almoxarife'

$pdo->prepare('UPDATE usuarios SET ultimo_login_em = NOW() WHERE id_usuario = :id')
    ->execute(['id' => $usuario['id_usuario']]);

header('Location: index.php', true, 303);
exit;
