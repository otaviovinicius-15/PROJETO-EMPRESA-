<?php
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/includes/auth.php';
$usuario = exigirLogin(["ADMIN", "ALMOXARIFE"]);

$erro = "";

$id_produto = (int) ($_GET["id_produto"] ?? 0);
$tipo       = "ENTRADA";
$quantidade = "";
$id_user    = $usuario["id_user"];       

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_produto = (int) ($_POST["id_produto"] ?? 0);
    $tipo       = $_POST["tipo"] ?? "";
    $quantidade = trim($_POST["quantidade"] ?? "");

    if (!$id_produto || $quantidade === "" || $tipo === "") {
        $erro = "Preencha todos os campos!";
    } elseif ($tipo !== "ENTRADA" && $tipo !== "SAIDA") {
        $erro = "Tipo não aceito!";
    } elseif (!ctype_digit($quantidade) || (int) $quantidade <= 0) {
        $erro = "Quantidade inválida!";
    } else {
        
        $erro = (new Movimentacao($pdo))->registrar($id_produto, $tipo, (int) $quantidade, $id_user);
    }

    if ($erro === "") {
        header("Location: movimentacoes.php?salvo=" . strtolower($tipo));
        exit;
    }
}

$produtos = (new Produto($pdo))->listarTodos();

require_once __DIR__ . '/includes/menu.php';
?>
    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="movimentacoes.php" class="hover:text-senai-blue">Movimentações</a> ›
                <span class="text-gray-700 font-semibold">Nova Movimentação</span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800">Alterar Estoque</h1>
        </div>
        <div class="p-6 flex-1 max-w-xl">

            <?php if (!empty($erro)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?= $erro ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="post">
                    <div class="mb-4">
                        <label class="form-label">Produto *</label>
                        <select name="id_produto" class="form-input" required>
                            <option value="">Selecione o produto</option>
                            <?php foreach ($produtos as $p): ?>
                                <option value="<?= $p["id_produto"] ?>" <?= $p["id_produto"] == $id_produto ? "selected" : "" ?>><?= htmlspecialchars($p["nome"]) ?> (estoque: <?= $p["quantidade_estoque"] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Tipo *</label>
                        <select name="tipo" class="form-input" required>
                            <option value="ENTRADA" <?= $tipo == "ENTRADA" ? "selected" : "" ?>>Entrada</option>
                            <option value="SAIDA" <?= $tipo == "SAIDA" ? "selected" : "" ?>>Saída</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Quantidade *</label>
                        <input type="number" name="quantidade" class="form-input" required min="1" step="1" placeholder="Ex: 10" value="<?= htmlspecialchars($quantidade) ?>">
                    </div>
                    <div class="mb-6">
                        <label class="form-label">Usuário responsável</label>
                        <input type="text" class="form-input bg-gray-50" value="<?= htmlspecialchars($usuario["nome"]) ?>" disabled>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar</button>
                        <a href="movimentacoes.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
