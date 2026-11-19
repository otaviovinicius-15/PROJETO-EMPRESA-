<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuarioLogado(): ?array
{
    return $_SESSION["usuario"] ?? null;
}

function ehAdmin(): bool
{
    return (usuarioLogado()["tipo"] ?? "") === "ADMIN";
}

function exigirLogin(array $tipos = []): array
{
    $usuario = usuarioLogado();

    if (!$usuario) {
        header("Location: login.php");
        exit;
    }
    if ($tipos && !in_array($usuario["tipo"], $tipos, true)) {
        header("Location: index.php?negado=1");
        exit;
    }
    return $usuario;
}
