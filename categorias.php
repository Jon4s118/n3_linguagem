<?php
session_start();
require_once "conexao.php";

// Categorias fixas conforme solicitado pelo professor.
$categorias = [
    ['id' => 1, 'nome' => 'Informática'],
    ['id' => 2, 'nome' => 'Escritório'],
    ['id' => 3, 'nome' => 'Acessórios']
];

$produtosPorCategoria = [];
try {
    foreach ($categorias as $categoria) {
         $comando = $pdo->prepare("
             SELECT id, nome, quantidade, estoque_minimo
             FROM produtos
             WHERE categoria_id = :categoria_id
             ORDER BY nome
         ");
         $comando->execute([':categoria_id' => $categoria['id']]);
         $produtosPorCategoria[$categoria['nome']] = $comando->fetchAll();
    }
} catch (PDOException $e) {
    $erro = "Erro ao carregar produtos por categoria.";
}

$tituloPagina = "Categorias";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque Facil | Categorias</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="pagina">
        <?php include "menu.php"; ?>
        <main class="conteudo">
            <?php include "cabecalho.php"; ?>

            <section class="painel">
                <div class="painel-titulo">
                    <h2>Produtos por categoria</h2>
                    <p>Itens organizados por setor.</p>
                </div>

                <?php if (!empty($erro)): ?>
                    <p role="alert"><?= htmlspecialchars($erro) ?></p>
                <?php endif; ?>

                <?php foreach ($produtosPorCategoria as $nomeCategoria => $produtos): ?>
                    <section class="painel" style="margin-top: 24px;">
                        <div class="painel-titulo">
                            <h3><?= htmlspecialchars($nomeCategoria) ?></h3>
                            <p><?= count($produtos) ?> item(ns)</p>
                        </div>

                        <div class="tabela-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Situacao</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($produtos)): ?>
                                        <tr>
                                            <td colspan="3">Nenhum produto nesta categoria.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($produtos as $produto): ?>
                                            <tr class="<?= $produto['quantidade'] <= $produto['estoque_minimo'] ? 'baixo-estoque' : '' ?>">
                                                <td><strong><?= htmlspecialchars($produto['nome']) ?></strong></td>
                                                <td><?= (int)$produto['quantidade'] ?> un.</td>
                                                <td>
                                                    <?= $produto['quantidade'] <= $produto['estoque_minimo'] ? 'Estoque baixo' : 'Disponivel' ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endforeach; ?>
            </section>

            <?php include "rodape.php"; ?>
        </main>
    </div>
</body>
</html>