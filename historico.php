<?php
session_start();
require_once "conexao.php";

$movimentacoes = [];
try {
    $comando = $pdo->query("
        SELECT m.id, m.tipo, m.quantidade, m.observacao, m.data_hora, p.nome AS produto_nome
        FROM movimentacoes m
        JOIN produtos p ON p.id = m.produto_id
        ORDER BY m.data_hora DESC
    ");
    $movimentacoes = $comando->fetchAll();
} catch (PDOException $e) {
    $erro = "Erro ao carregar historico.";
}

$tituloPagina = "Historico";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque Facil | Historico</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="pagina">
        <?php include "menu.php"; ?>
        <main class="conteudo">
            <?php include "cabecalho.php"; ?>

            <section class="painel">
                <div class="painel-titulo">
                    <h2>Historico de movimentacoes</h2>
                    <p>Todas as entradas e saidas registradas.</p>
                </div>

                <?php if (!empty($erro)): ?>
                    <p role="alert"><?= htmlspecialchars($erro) ?></p>
                <?php endif; ?>

                <div class="tabela-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Produto</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Observacao</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movimentacoes as $mov): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($mov['data_hora'])) ?></td>
                                    <td><strong><?= htmlspecialchars($mov['produto_nome']) ?></strong></td>
                                    <td><?= $mov['tipo'] === 'ENTRADA' ? 'Entrada' : 'Saida' ?></td>
                                    <td><?= (int)$mov['quantidade'] ?></td>
                                    <td><?= htmlspecialchars($mov['observacao'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($movimentacoes)): ?>
                                <tr>
                                    <td colspan="5">Nenhuma movimentacao registrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <?php include "rodape.php"; ?>
        </main>
    </div>
</body>
</html>