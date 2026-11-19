<?php


class Produto
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscar(int $id)
    {
        $st = $this->pdo->prepare("SELECT * FROM produto WHERE id_produto = ?");
        $st->execute([$id]);
        return $st->fetch();          
    }

    public function buscarPorNome(string $nome)
    {
        $st = $this->pdo->prepare("SELECT * FROM produto WHERE nome = ?");
        $st->execute([$nome]);
        return $st->fetch();
    }

    public function listarTodos(string $ordem = "nome"): array
    {
        $ordem = $ordem === "estoque" ? "quantidade_estoque, nome" : "nome";
        return $this->pdo->query("SELECT * FROM produto ORDER BY $ordem")->fetchAll();
    }

    
    private function filtros(array $f, array &$params): string
    {
        $where = [];
        if (!empty($f["nome"])) {
            $where[] = "nome LIKE ?";
            $params[] = "%" . $f["nome"] . "%";
        }
        if (!empty($f["data_inicio"])) {
            $where[] = "data_cadastro >= ?";
            $params[] = $f["data_inicio"];
        }
        if (!empty($f["data_fim"])) {
            $where[] = "data_cadastro <= ?";
            $params[] = $f["data_fim"];
        }
        return $where ? "WHERE " . implode(" AND ", $where) : "";
    }

    public function contar(array $f): int
    {
        $params = [];
        $st = $this->pdo->prepare("SELECT COUNT(*) FROM produto " . $this->filtros($f, $params));
        $st->execute($params);
        return (int) $st->fetchColumn();
    }

    public function listarPaginado(array $f, int $pagina, int $porPagina): array
    {
        $params = [];
        $offset = ($pagina - 1) * $porPagina;
        $st = $this->pdo->prepare("SELECT * FROM produto " . $this->filtros($f, $params) . " ORDER BY nome LIMIT $porPagina OFFSET $offset");
        $st->execute($params);
        return $st->fetchAll();
    }

    
    public function cadastrar(string $nome, int $quantidade, float $valor, string $data, int $id_user): void
    {
        $this->pdo->beginTransaction();
        try {
            $st = $this->pdo->prepare("INSERT INTO produto (nome, quantidade_estoque, valor_unitario, data_cadastro) VALUES (?, ?, ?, ?)");
            $st->execute([$nome, $quantidade, $valor, $data]);

            if ($quantidade > 0) {
                $st = $this->pdo->prepare("INSERT INTO movimentacao (tipo, quantidade, id_produto, datetime_movimentacao, id_user) VALUES ('ENTRADA', ?, ?, NOW(), ?)");
                $st->execute([$quantidade, $this->pdo->lastInsertId(), $id_user]);
            }
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    
    public function excluir(int $id): bool
    {
        $st = $this->pdo->prepare("DELETE FROM produto WHERE id_produto = ?");
        $st->execute([$id]);
        return $st->rowCount() > 0;
    }
}
