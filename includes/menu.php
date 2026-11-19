<?php
$pagina = basename($_SERVER['PHP_SELF']);
$ativo = function ($prefixo) use ($pagina) {
    return strpos($pagina, $prefixo) === 0 ? 'active' : '';
};
$usuarioMenu = $_SESSION['usuario'] ?? null;
require __DIR__ . '/head.php';
?>
<body class="bg-gray-100 min-h-screen flex">
    <aside class="w-56 bg-gray-900 min-h-screen flex flex-col flex-shrink-0">
        <div class="px-4 py-5 border-b border-gray-700"><p class="text-white font-extrabold text-base">📦 Estoque SAEP</p><p class="text-gray-500 text-xs">Painel Administrativo</p></div>
        <nav class="flex-1 p-3 space-y-1 pt-4">
            <a href="index.php"         class="nav-link <?= $ativo('index') ?>">📊 Dashboard</a>
            <a href="produtos.php"      class="nav-link <?= $ativo('produto') ?>">🧴 Produtos</a>
            <a href="estoque.php"       class="nav-link <?= $ativo('estoque') ?>">📋 Verificar Estoque</a>
            <a href="movimentacoes.php" class="nav-link <?= $ativo('movimentac') ?>">🔄 Movimentações</a>
            <a href="saidas.php"        class="nav-link <?= $ativo('saidas') ?>">📤 Saídas</a>
            <?php if (($usuarioMenu["tipo"] ?? "") === "ADMIN"): ?>
            <a href="usuarios.php"      class="nav-link <?= $ativo('usuario') ?>">👤 Usuários</a>
            <?php endif; ?>
        </nav>
        <?php if ($usuarioMenu): ?>
        <div class="p-3 border-t border-gray-700">
            <p class="text-white text-sm font-semibold"><?= htmlspecialchars($usuarioMenu["nome"]) ?></p>
            <p class="text-gray-500 text-xs mb-2"><?= $usuarioMenu["tipo"] === "ADMIN" ? "Administrador" : "Almoxarife" ?></p>
            <a href="logout.php" class="nav-link">🚪 Sair</a>
        </div>
        <?php endif; ?>
    </aside>
