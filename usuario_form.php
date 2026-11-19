<?php
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/includes/auth.php';
exigirLogin(["ADMIN"]);

$erro  = "";
$nome  = "";
$email = "";
$tipo  = "ALMOXARIFE";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome  = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $tipo  = $_POST["tipo"] ?? "";

    $usuarioModel = new Usuario($pdo);
    $erro = $usuarioModel->validar($nome, $email, $senha, $tipo);

    if ($erro === "") {
        $usuarioModel->cadastrar($nome, $email, $senha, $tipo);
        header("Location: usuarios.php?salvo=1");
        exit;
    }
}

require_once __DIR__ . '/includes/menu.php';
?>
    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="usuarios.php" class="hover:text-senai-blue">Usuários</a> ›
                <span class="text-gray-700 font-semibold">Novo Usuário</span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800">Novo Usuário</h1>
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
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" class="form-input" required maxlength="100" value="<?= htmlspecialchars($nome) ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">E-mail *</label>
                        <input type="email" name="email" class="form-input" required maxlength="100" value="<?= htmlspecialchars($email) ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Senha * (mín. 6 caracteres)</label>
                        <input type="password" name="senha" class="form-input" required minlength="6">
                    </div>
                    <div class="mb-6">
                        <label class="form-label">Tipo *</label>
                        <select name="tipo" class="form-input" required>
                            <?php foreach (Usuario::TIPOS as $valor => $rotulo): ?>
                                <option value="<?= $valor ?>" <?= $tipo == $valor ? "selected" : "" ?>><?= $rotulo ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar</button>
                        <a href="usuarios.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
