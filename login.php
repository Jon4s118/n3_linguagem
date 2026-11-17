<?php
session_start();

if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php', true, 303);
    exit;
}

$erro = $_SESSION['erro_login'] ?? '';
unset($_SESSION['erro_login']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque Fácil | Login</title>
    <link rel="stylesheet" href="stylelogin.css">
</head>
<body>
    <div class="pagina-login">
        <form method="POST" action="autenticar.php" class="formulario">
        
            <h1>Estoque Fácil</h1>
            <p>Faça login para continuar</p>

            <?php if ($erro !== ''): ?>
                <p role="alert" class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <div class="campo-formulario">
                <label for="text">usuário</label>
                <input type="text" name="usuário" id="email" required autofocus>
            </div>

            <div class="campo-formulario">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" required>
            </div>

            <button type="submit" class="botao-principal">Entrar</button>
        </form>
    </div>
</body>
</html>