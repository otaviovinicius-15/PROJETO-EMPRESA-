<?php
session_start();
require_once __DIR__ . '/conexao.php';

if (isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$sucesso = isset($_GET["cadastrado"]) ? "Cadastro realizado! Faça o login." : "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if ($email === "" || $senha === "") {
        $erro = "Preencha todos os campos!";
    } else {
        $user = (new Usuario($pdo))->autenticar($email, $senha);
        if (!$user) {
            $erro = "E-mail ou senha inválidos!";
        } else {
            session_regenerate_id(true);       
            $_SESSION["usuario"] = [
                "id_user" => (int) $user["id_user"],
                "nome"    => $user["nome"],
                "tipo"    => $user["tipo"],
            ];
            header("Location: index.php");
            exit;
        }
    }
}

require __DIR__ . '/includes/head.php';
?>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-6">
            <p class="text-2xl font-extrabold text-gray-800">📦 Estoque SAEP</p>
            <p class="text-sm text-gray-500">Acesse sua conta</p>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $erro ?></div>
        <?php endif; ?>
        <?php if (!empty($sucesso)): ?>
            <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded mb-4"><?= $sucesso ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="post">
                <div class="mb-4">
                    <label class="form-label">E-mail *</label>
                    <input type="email" name="email" class="form-input" required maxlength="100" value="<?= htmlspecialchars($email) ?>">
                </div>
                <div class="mb-6">
                    <label class="form-label">Senha *</label>
                    <input type="password" name="senha" class="form-input" required>
                </div>
                <button type="submit" class="w-full bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">Entrar</button>
            </form>
            <p class="text-center text-xs text-gray-500 mt-4">Não tem conta? <a href="cadastro.php" class="text-senai-blue hover:underline">Cadastre-se</a></p>
        </div>
    </div>
</body>
</html>
