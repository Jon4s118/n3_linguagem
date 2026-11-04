<?php $paginaAtual = basename($_SERVER["PHP_SELF"]); ?>

<aside class="menu-lateral">
    <a class="marca" href="index.php">
        <span class="marca-icone">EF</span>
        <span>Estoque Facil<small>Gestao integrada</small></span>
    </a>

    <nav>
        <a class="<?= $paginaAtual === "index.php" ? "ativo" : "" ?>" href="index.php">Visão geral</a>

        <span class="titulo-menu">Catálogo</span>
        <a class="<?= $paginaAtual === "produtos.php" ? "ativo" : "" ?>" href="produtos.php">Produtos</a>
        <a class="<?= $paginaAtual === "categorias.php" ? "ativo" : "" ?>" href="categorias.php">Categorias</a>

        <span class="titulo-menu">Operacoes</span>
        <a class="<?= $paginaAtual === "entrada.php" ? "ativo" : "" ?>" href="entrada.php">Nova entrada</a>
        <a class="<?= $paginaAtual === "saida.php" ? "ativo" : "" ?>" href="saida.php">Nova saida</a>
        <a class="<?= $paginaAtual === "historico.php" ? "ativo" : "" ?>" href="historico.php">Historico</a>
    </nav>

    <a class="sair" href="#">Encerrar sessao</a>
</aside>