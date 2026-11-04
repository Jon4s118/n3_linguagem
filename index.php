<?php
session_start();
require_once "conexao.php";

$nomeSistema = "Estoque Fácil";

$totalProdutos = 0;
$unidadesEstoque = 0;
$valorEstoque = 0;
$estoqueBaixo = 0;
$movimentacoesRecentes = [];
$produtosBaixoEstoque = [];

try {
    $totalProdutos = (int) $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
    $unidadesEstoque = (int) $pdo->query("SELECT COALESCE(SUM(quantidade), 0) FROM produtos")->fetchColumn();
    $valorEstoque = (float) $pdo->query("SELECT COALESCE(SUM(quantidade * preco), 0) FROM produtos")->fetchColumn();
    $estoqueBaixo = (int) $pdo->query("SELECT COUNT(*) FROM produtos WHERE quantidade <= estoque_minimo")->fetchColumn();

    $comando = $pdo->query("
        SELECT m.tipo, m.quantidade, m.data_hora, p.nome AS produto_nome
        FROM movimentacoes m
        JOIN produtos p ON p.id = m.produto_id
        ORDER BY m.data_hora DESC
        LIMIT 10
    ");
    $movimentacoesRecentes = $comando->fetchAll();

    $comando = $pdo->query("
        SELECT nome, quantidade, estoque_minimo
        FROM produtos
        WHERE quantidade <= estoque_minimo
        ORDER BY quantidade ASC
        LIMIT 5
    ");
    $produtosBaixoEstoque = $comando->fetchAll();
} catch (PDOException $e) {
    $erro = "Erro ao carregar visão geral.";
}

$tituloPagina = "Visão geral";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nomeSistema ?> | Painel</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="pagina">
        <?php include "menu.php"; ?>
        <main class="conteudo">
            <?php include "cabecalho.php"; ?>

            <section class="resumo">
                <article class="cartao">
                    Produtos ativos
                    <strong><?= $totalProdutos ?></strong>
                    itens cadastrados
                </article>

                <article class="cartao">
                    Unidades em estoque
                    <strong><?= $unidadesEstoque ?></strong>
                    saldo total disponível
                </article>

                <article class="cartao">
                    Valor em estoque
                    <strong>R$ <?= number_format($valorEstoque, 2, ',', '.') ?></strong>
                    baseado no preço de custo
                </article>

                <article class="cartao alerta">
                    Estoque baixo
                    <strong><?= $estoqueBaixo ?></strong>
                    produtos pedindo atenção
                </article>
            </section>

            <section class="atalhos">
                <a class="atalho" href="entrada.php">Registrar entrada</a>
                <a class="atalho" href="saida.php">Registrar saída</a>
                <a class="atalho" href="produtos.php">Novo produto</a>
            </section>

            <div class="conteudo-duplo">
                <section class="painel">
                    <div class="painel-titulo">
                        <h2>Movimentações recentes</h2>
                        <p>Últimas operações registradas</p>
                    </div>

                    <div class="tabela-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Tipo</th>
                                    <th>Quantidade</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimentacoesRecentes as $mov): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($mov['produto_nome']) ?></strong></td>
                                        <td>
                                            <span class="etiqueta <?= $mov['tipo'] === 'ENTRADA' ? 'entrada' : 'saida' ?>">
                                                <?= $mov['tipo'] === 'ENTRADA' ? 'Entrada' : 'Saída' ?>
                                            </span>
                                        </td>
                                        <td><?= (int)$mov['quantidade'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($mov['data_hora'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($movimentacoesRecentes)): ?>
                                    <tr>
                                        <td colspan="4">Nenhuma movimentação registrada.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="painel">
                    <div class="painel-titulo">
                        <h2>Reposição necessária</h2>
                        <p>Produtos abaixo do estoque mínimo</p>
                    </div>

                    <ul class="lista-destaque">
                        <?php foreach ($produtosBaixoEstoque as $produto): ?>
                            <li>
                                <span>
                                    <strong><?= htmlspecialchars($produto['nome']) ?></strong>
                                    <small>Mínimo: <?= (int)$produto['estoque_minimo'] ?></small>
                                </span>
                                <b><?= (int)$produto['quantidade'] ?> un.</b>
                            </li>
                        <?php endforeach; ?>

                        <?php if (empty($produtosBaixoEstoque)): ?>
                            <li>Nenhum produto com estoque baixo.</li>
                        <?php endif; ?>
                    </ul>
                </section>
            </div>

            <?php include "rodape.php"; ?>
        </main>
    </div>
</body>
</html>