<?php

class Movimentacao
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listar(?string $tipo = null, ?int $limite = null): array
    {
        $sql = "SELECT m.*, p.nome AS produto, u.nome AS usuario
                FROM movimentacao m
                JOIN produto p ON p.id_produto = m.id_produto
                LEFT JOIN `user` u ON u.id_user = m.id_user";
        $params = [];
        if ($tipo !== null) {
            $sql .= " WHERE m.tipo = ?";
            $params[] = $tipo;
        }
        $sql .= " ORDER BY m.id_movimentacao DESC";
        if ($limite !== null) {
            $sql .= " LIMIT " . (int) $limite;
        }
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public function totais(): array
    {
        $sql = "SELECT
            (SELECT COUNT(*) FROM produto) AS produtos,
            (SELECT COALESCE(SUM(quantidade_estoque), 0) FROM produto) AS itens,
            (SELECT COUNT(*) FROM movimentacao) AS movimentacoes,
            (SELECT COUNT(*) FROM movimentacao WHERE tipo = 'SAIDA') AS saidas";
        return $this->pdo->query($sql)->fetch();
    }

    
    public function registrar(int $id_produto, string $tipo, int $quantidade, int $id_user): string
    {
        $this->pdo->beginTransaction();
        try {
            
            $st = $this->pdo->prepare("SELECT quantidade_estoque FROM produto WHERE id_produto = ? FOR UPDATE");
            $st->execute([$id_produto]);
            $produto = $st->fetch();

            if (!$produto) {
                $this->pdo->rollBack();
                return "Não foi possível encontrar o produto!";
            }

            $disponivel = (int) $produto["quantidade_estoque"];
            if ($tipo === "SAIDA" && $quantidade > $disponivel) {
                $this->pdo->rollBack();
                return "Saída não permitida: estoque insuficiente. Disponível: $disponivel. Solicitado: $quantidade.";
            }

            $st = $this->pdo->prepare("INSERT INTO movimentacao (tipo, quantidade, id_produto, datetime_movimentacao, id_user) VALUES (?, ?, ?, NOW(), ?)");
            $st->execute([$tipo, $quantidade, $id_produto, $id_user]);

            // ENTRADA soma no estoque, SAIDA subtrai
            $delta = $tipo === "ENTRADA" ? $quantidade : -$quantidade;
            $st = $this->pdo->prepare("UPDATE produto SET quantidade_estoque = quantidade_estoque + ? WHERE id_produto = ?");
            $st->execute([$delta, $id_produto]);

            $this->pdo->commit();
            return "";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    
    public function excluir(int $id): string
    {
        $this->pdo->beginTransaction();
        try {
            $st = $this->pdo->prepare("SELECT m.tipo, m.quantidade, m.id_produto, p.quantidade_estoque
                                       FROM movimentacao m JOIN produto p ON p.id_produto = m.id_produto
                                       WHERE m.id_movimentacao = ? FOR UPDATE");
            $st->execute([$id]);
            $m = $st->fetch();

            if (!$m) {
                $this->pdo->rollBack();
                return "Não foi possível encontrar a movimentação!";
            }
            if ($m["tipo"] === "ENTRADA" && $m["quantidade_estoque"] < $m["quantidade"]) {
                $this->pdo->rollBack();
                return "Não é possível excluir esta entrada: o estoque ficaria negativo.";
            }

            $delta = $m["tipo"] === "ENTRADA" ? -$m["quantidade"] : $m["quantidade"];
            $st = $this->pdo->prepare("UPDATE produto SET quantidade_estoque = quantidade_estoque + ? WHERE id_produto = ?");
            $st->execute([$delta, $m["id_produto"]]);

            $st = $this->pdo->prepare("DELETE FROM movimentacao WHERE id_movimentacao = ?");
            $st->execute([$id]);

            $this->pdo->commit();
            return "";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
