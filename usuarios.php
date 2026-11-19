<?php
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/includes/auth.php';
exigirLogin(["ADMIN"]);

$sucesso = isset($_GET["salvo"]) ? "Usuário cadastrado com sucesso!" : "";

$usuarios = (new Usuario($pdo))->listar();

require_once __DIR__ . '/includes/menu.php';
?>
    <main class="flex-1 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Usuários</h1>
                <p class="text-sm text-gray-500">Usuários com acesso ao sistema</p>
            </div>
            <a href="usuario_form.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
                + Novo Usuário
            </a>
        </div>

        <div class="p-6 flex-1">

            <?php if (!empty($sucesso)): ?>
                <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                    <span class="font-bold text-base">✓</span>
                    <span><?= $sucesso ?></span>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-700 text-lg leading-none">×</button>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-senai-blue text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Nome</th>
                            <th class="px-4 py-3 text-center">E-mail</th>
                            <th class="px-4 py-3 text-center">Tipo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    <?php foreach ($usuarios as $u): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?= htmlspecialchars($u["nome"]) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= htmlspecialchars($u["email"]) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= Usuario::TIPOS[$u["tipo"]] ?? $u["tipo"] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                    <p class="text-xs text-gray-400">Exibindo <?= count($usuarios) ?> usuários</p>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
