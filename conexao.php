<?php

// Dados de conexão com o banco
$host  = 'localhost';
$banco = 'empresa';
$user  = 'root';
$senha = '';                     
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $user,
        $senha,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    exit('Falha na conexão: ' . $e->getMessage());
}


spl_autoload_register(function ($classe) {
    $arquivo = __DIR__ . '/models/' . $classe . '.php';
    if (file_exists($arquivo)) {
        require_once $arquivo;
    }
});
