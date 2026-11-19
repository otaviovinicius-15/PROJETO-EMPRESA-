<?php
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/includes/auth.php';
exigirLogin();

$produtoModel = new Produto($pdo);

$sucesso = "";
$erro = "";

if (isset($_GET["excluir"]) && !ehAdmin()) {
    $erro = "Somente administradores podem excluir produtos.";
} elseif (isset($_GET["excluir"])) {
    try {
        if ($produtoModel->excluir((int) $_GET["excluir"])) {
            $sucesso = "Produto removido com sucesso!";
        } else {
            $erro = "Não foi possível encontrar o produto!";
        }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1451) {       
            $erro = "Este produto possui movimentações e não pode ser removido.";
        } else {
            throw $e;
        }
    }
}
if (isset($_GET["salvo"])) {
    $sucesso = "Produto cadastrado com sucesso!";
}

require_once __DIR__ . '/includes/menu.php';


$filtros = [
    "nome"        => trim($_GET["nome"] ?? ""),
    "data_inicio" => $_GET["data_inicio"] ?? "",
    "data_fim"    => $_GET["data_fim"] ?? "",
];
$porPagina = 5;
$total     = $produtoModel->contar($filtros);
$paginas   = max(1, (int) ceil($total / $porPagina));
$pagina    = min(max(1, (int) ($_GET["pagina"] ?? 1)), $paginas);
$produtos  = $produtoModel->listarPaginado($filtros, $pagina, $porPagina);


$link = function ($p) use ($filtros) {
    return "?" . http_build_query(array_merge($filtros, ["pagina" => $p]));
};
?>
    <main class="flex-1 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Produtos</h1>
                <p class="text-sm text-gray-500">Cadastre, consulte e remova os produtos do estoque</p>
            </div>
            <?php if (ehAdmin()): ?>
            <a href="produto_form.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
                + Novo Produto
            </a>
            <?php endif; ?>
        </div>

        <div class="p-6 flex-1">

            <?php if (!empty($sucesso)): ?>
                <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                    <span class="font-bold text-base">✓</span>
                    <span><?= $sucesso ?></span>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-700 text-lg leading-none">×</button>
                </div>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                    <span class="font-bold text-base">!</span>
                    <span><?= $erro ?></span>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-700 text-lg leading-none">×</button>
                </div>
            <?php endif; ?>

            <form method="get" class="bg-white rounded-xl shadow-sm p-4 mb-5 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[180px]">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-input" placeholder="Buscar por nome" value="<?= htmlspecialchars($filtros["nome"]) ?>">
                </div>
                <div>
                    <label class="form-label">Cadastrado de</label>
                    <input type="date" name="data_inicio" class="form-input" value="<?= htmlspecialchars($filtros["data_inicio"]) ?>">
                </div>
                <div>
                    <label class="form-label">Até</label>
                    <input type="date" name="data_fim" class="form-input" value="<?= htmlspecialchars($filtros["data_fim"]) ?>">
                </div>
                <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">🔍 Filtrar</button>
                <a href="produtos.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Limpar</a>
            </form>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-senai-blue text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Produto</th>
                            <th class="px-4 py-3 text-center">Valor unitário</th>
                            <th class="px-4 py-3 text-center">Quantidade</th>
                            <th class="px-4 py-3 text-center">Valor total</th>
                            <th class="px-4 py-3 text-center">Cadastro</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                    <?php if (!$produtos): ?>
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Nenhum produto encontrado!</td></tr>
                    <?php endif; ?>

                    <?php foreach ($produtos as $p): ?>
                        <?php $valorTotal = round($p["valor_unitario"] * $p["quantidade_estoque"], 2); ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?= htmlspecialchars($p["nome"]) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600">R$ <?= number_format($p["valor_unitario"], 2, ",", ".") ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= $p["quantidade_estoque"] ?></td>
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold">R$ <?= number_format($valorTotal, 2, ",", ".") ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= date("d/m/Y", strtotime($p["data_cadastro"])) ?></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="produto.php?id=<?= $p["id_produto"] ?>" class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-senai-blue-dark transition" title="Ver produto">🔍 Ver</a>
                                    <a href="movimentacao_form.php?id_produto=<?= $p["id_produto"] ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Movimentar estoque">🔄 Movimentar</a>
                                    <?php if (ehAdmin()): ?>
                                    <a onclick="return confirm('Tem certeza disso?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" href="?excluir=<?= $p["id_produto"] ?>">Excluir</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>

                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50 flex items-center justify-between">
                    <p class="text-xs text-gray-400">Exibindo <?= count($produtos) ?> de <?= $total ?> produtos — página <?= $pagina ?> de <?= $paginas ?></p>
                    <div class="flex gap-1">
                        <?php if ($pagina > 1): ?>
                            <a href="<?= htmlspecialchars($link($pagina - 1)) ?>" class="bg-white border border-gray-200 text-gray-600 text-xs px-2.5 py-1.5 rounded-md hover:bg-gray-100">‹ Anterior</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $paginas; $i++): ?>
                            <a href="<?= htmlspecialchars($link($i)) ?>" class="text-xs px-2.5 py-1.5 rounded-md <?= $i == $pagina ? "bg-senai-blue text-white" : "bg-white border border-gray-200 text-gray-600 hover:bg-gray-100" ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($pagina < $paginas): ?>
                            <a href="<?= htmlspecialchars($link($pagina + 1)) ?>" class="bg-white border border-gray-200 text-gray-600 text-xs px-2.5 py-1.5 rounded-md hover:bg-gray-100">Próxima ›</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
