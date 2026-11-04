<?php
session_start();
require_once "conexao.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php', true, 303);
    exit;
}

$csrf = $_POST['csrf'] ?? '';
if (!hash_equals($_SESSION['csrf_movimentacao'] ?? '', $csrf)) {
    $_SESSION['erro_movimentacao'] = 'Formulario expirado ou invalido.';
    header('Location: index.php', true, 303);
    exit;
}

$tipo = $_POST['tipo'] ?? '';
$produtoId = (int) ($_POST['produto_id'] ?? 0);
$quantidade = (int) ($_POST['quantidade'] ?? 0);
$observacao = trim($_POST['observacao'] ?? '');

if (!in_array($tipo, ['ENTRADA', 'SAIDA'], true)) {
    $_SESSION['erro_movimentacao'] = 'Tipo de movimentacao invalido.';
    header('Location: index.php', true, 303);
    exit;
}

if ($produtoId <= 0 || $quantidade <= 0) {
    $_SESSION['erro_movimentacao'] = 'Produto e quantidade devem ser maiores que zero.';
    header('Location: ' . ($tipo === 'SAIDA' ? 'saida.php' : 'entrada.php'), true, 303);
    exit;
}

try {
    $pdo->beginTransaction();

    $comando = $pdo->prepare("SELECT quantidade FROM produtos WHERE id = :id FOR UPDATE");
    $comando->execute([':id' => $produtoId]);
    $produto = $comando->fetch();

    if (!$produto) {
        throw new Exception('Produto nao encontrado.');
    }

    $saldoAtual = (int) $produto['quantidade'];

    if ($tipo === 'SAIDA' && $saldoAtual < $quantidade) {
        throw new Exception('Quantidade insuficiente em estoque.');
    }

    $novoSaldo = $tipo === 'ENTRADA'
        ? $saldoAtual + $quantidade
        : $saldoAtual - $quantidade;

    $comando = $pdo->prepare("UPDATE produtos SET quantidade = :quantidade WHERE id = :id");
    $comando->execute([':quantidade' => $novoSaldo, ':id' => $produtoId]);

    $comando = $pdo->prepare("
        INSERT INTO movimentacoes (produto_id, tipo, quantidade, observacao)
        VALUES (:produto_id, :tipo, :quantidade, :observacao)
    ");
    $comando->execute([
        ':produto_id' => $produtoId,
        ':tipo' => $tipo,
        ':quantidade' => $quantidade,
        ':observacao' => $observacao !== '' ? $observacao : null
    ]);

    $pdo->commit();

    $_SESSION['sucesso_movimentacao'] = 'Movimentacao registrada com sucesso!';
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['erro_movimentacao'] = $e->getMessage();
}

header('Location: ' . ($tipo === 'SAIDA' ? 'saida.php' : 'entrada.php'), true, 303);
exit;