<?php
session_start();
require_once "conexao.php";

$sucesso = $_SESSION['sucesso_movimentacao'] ?? '';
$erro = $_SESSION['erro_movimentacao'] ?? '';
unset($_SESSION['sucesso_movimentacao'], $_SESSION['erro_movimentacao']);

$_SESSION['csrf_movimentacao'] ??= bin2hex(random_bytes(32));

$produtos = [];
try {
    $comando = $pdo->query("SELECT id, nome, quantidade FROM produtos ORDER BY nome");
    $produtos = $comando->fetchAll();
} catch (PDOException $e) {
    $erro = "Erro ao carregar produtos.";
}

$tituloPagina = "Nova saida";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque Facil | Nova saida</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="pagina">
        <?php include "menu.php"; ?>
        <main class="conteudo">
            <?php include "cabecalho.php"; ?>

            <section class="painel">
                <div class="painel-titulo">
                    <h2>Registrar saida de estoque</h2>
                    <p>Informe o produto e a quantidade retirada.</p>
                </div>

                <?php if ($sucesso !== ''): ?>
                    <p class="mensagem-sucesso" role="status"><?= htmlspecialchars($sucesso) ?></p>
                <?php endif; ?>
                <?php if ($erro !== ''): ?>
                    <p role="alert"><?= htmlspecialchars($erro) ?></p>
                <?php endif; ?>

                <form method="POST" action="movimentacao.php" class="formulario">
                    <input type="hidden" name="tipo" value="SAIDA">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_movimentacao'] ?>">

                    <div class="campo-formulario">
                        <label for="produto_id">Produto</label>
                        <select name="produto_id" id="produto_id" required>
                            <option value="">Selecione</option>
                            <?php foreach ($produtos as $produto): ?>
                                <option value="<?= (int)$produto['id'] ?>">
                                    <?= htmlspecialchars($produto['nome']) ?> (atual: <?= (int)$produto['quantidade'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="campo-formulario">
                        <label for="quantidade">Quantidade</label>
                        <input type="number" name="quantidade" id="quantidade" min="1" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="observacao">Observacao</label>
                        <input type="text" name="observacao" id="observacao" maxlength="255">
                    </div>

                    <button type="submit" class="botao-principal">Registrar saida</button>
                </form>
            </section>

            <?php include "rodape.php"; ?>
        </main>
    </div>
</body>
</html>