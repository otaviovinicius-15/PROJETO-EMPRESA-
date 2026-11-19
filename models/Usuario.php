<?php

class Usuario
{
    public const TIPOS = ["ADMIN" => "Administrador", "ALMOXARIFE" => "Almoxarife"];

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listar(): array
    {
        return $this->pdo->query("SELECT id_user, nome, email, tipo FROM `user` ORDER BY nome")->fetchAll();
    }

    public function buscarPorEmail(string $email)
    {
        $st = $this->pdo->prepare("SELECT * FROM `user` WHERE email = ?");
        $st->execute([$email]);
        return $st->fetch();
    }

    
    public function autenticar(string $email, string $senha)
    {
        $user = $this->buscarPorEmail($email);
        if ($user && password_verify($senha, $user["senha"])) {
            return $user;
        }
        return false;
    }

    
    public function validar(string $nome, string $email, string $senha, string $tipo): string
    {
        if ($nome === "" || $email === "" || $senha === "") {
            return "Preencha todos os campos!";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "E-mail inválido!";
        }
        if (strlen($senha) < 6) {
            return "A senha deve ter no mínimo 6 caracteres.";
        }
        if (!isset(self::TIPOS[$tipo])) {
            return "Tipo de usuário inválido!";
        }
        if ($this->buscarPorEmail($email)) {
            return "E-mail já cadastrado!";
        }
        return "";
    }

    public function cadastrar(string $nome, string $email, string $senha, string $tipo): void
    {
        $st = $this->pdo->prepare("INSERT INTO `user` (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
        $st->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT), $tipo]);
    }
}
