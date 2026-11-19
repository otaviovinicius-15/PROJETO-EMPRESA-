<?php

session_start();
require_once __DIR__ . '/conexao.php';

$erro  = "";
$nome  = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome  = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    $usuarioModel = new Usuario($pdo);
    $erro = $usuarioModel->validar($nome, $email, $senha, "ALMOXARIFE");

    if ($erro === "") {
        $usuarioModel->cadastrar($nome, $email, $senha, "ALMOXARIFE");
        header("Location: login.php?cadastrado=1");
        exit;
    }
}

require __DIR__ . '/includes/head.php';
?>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-6">
            <p class="text-2xl font-extrabold text-gray-800">📦 Estoque SAEP</p>
            <p class="text-sm text-gray-500">Criar conta de almoxarife</p>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $erro ?></div>
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
                <div class="mb-6">
                    <label class="form-label">Senha * (mín. 6 caracteres)</label>
                    <input type="password" name="senha" class="form-input" required minlength="6">
                </div>
                <button type="submit" class="w-full bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">Cadastrar</button>
            </form>
            <p class="text-center text-xs text-gray-500 mt-4">Já tem conta? <a href="login.php" class="text-senai-blue hover:underline">Entrar</a></p>
        </div>
    </div>
</body>
</html>
