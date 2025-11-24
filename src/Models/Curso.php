<?php

namespace PlanetaTreinamentos\Models;

use PDO;

/**
 * Model Curso
 * Gerencia dados de cursos
 */
class Curso
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Buscar todos os cursos ativos (alias para getAll com status ativo)
     */
    public function all(): array
    {
        return $this->getAll(['status' => 'ativo']);
    }

    /**
     * Buscar todos os cursos incluindo inativos
     */
    public function allWithInactive(): array
    {
        return $this->getAll();
    }

    /**
     * Buscar todos os cursos
     */
    public function getAll(array $filters = []): array
    {
        $sql = "SELECT * FROM cursos WHERE 1=1";
        $params = [];

        // Filtro por status
        if (isset($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        // Ordenação
        if ($this->columnExists('cursos', 'ordem')) {
            $sql .= " ORDER BY ordem ASC, nome ASC";
        } else {
            $sql .= " ORDER BY nome ASC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar todos os cursos
     */
    public function countAll(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM cursos WHERE 1=1";
        $params = [];

        // Filtro por status
        if (isset($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return (int) $stmt->fetchColumn();
    }

    /**
     * Contar cursos ativos
     */
    public function countAtivos(): int
    {
        return $this->countAll(['status' => 'ativo']);
    }

    /**
     * Buscar curso por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM cursos WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Alias para getById() - compatibilidade
     */
    public function findById(int $id): ?array
    {
        return $this->getById($id);
    }

    /**
     * Criar novo curso
     */
    public function create(array $data): int
    {
        if ($this->columnExists('cursos', 'ordem')) {
            $sql = "INSERT INTO cursos (nome, descricao, imagem_base, status, ordem, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $params = [
                $data['nome'],
                $data['descricao'] ?? null,
                $data['imagem_base'] ?? null,
                $data['status'] ?? 'ativo',
                $data['ordem'] ?? 0
            ];
        } else {
            $sql = "INSERT INTO cursos (nome, descricao, imagem_base, status, created_at) VALUES (?, ?, ?, ?, NOW())";
            $params = [
                $data['nome'],
                $data['descricao'] ?? null,
                $data['imagem_base'] ?? null,
                $data['status'] ?? 'ativo'
            ];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualizar curso
     */
    public function update(int $id, array $data): bool
    {
        if ($this->columnExists('cursos', 'ordem')) {
            $sql = "UPDATE cursos SET nome = ?, descricao = ?, imagem_base = ?, status = ?, ordem = ?, updated_at = NOW() WHERE id = ?";
            $params = [
                $data['nome'],
                $data['descricao'] ?? null,
                $data['imagem_base'] ?? null,
                $data['status'] ?? 'ativo',
                $data['ordem'] ?? 0,
                $id
            ];
        } else {
            $sql = "UPDATE cursos SET nome = ?, descricao = ?, imagem_base = ?, status = ?, updated_at = NOW() WHERE id = ?";
            $params = [
                $data['nome'],
                $data['descricao'] ?? null,
                $data['imagem_base'] ?? null,
                $data['status'] ?? 'ativo',
                $id
            ];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Deletar curso (soft delete)
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE cursos SET deleted_at = NOW() WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    /**
     * Contar alunos por curso
     */
    public function countAlunos(int $cursoId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM alunos 
            WHERE curso_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$cursoId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Buscar cursos ativos
     */
    public function getAtivos(): array
    {
        return $this->getAll(['status' => 'ativo']);
    }

    /**
     * Verificar se coluna existe na tabela
     */
    private function columnExists(string $table, string $column): bool
    {
        try {
            $stmt = $this->db->prepare("SHOW COLUMNS FROM $table LIKE ?");
            $stmt->execute([$column]);
            return $stmt->fetch() !== false;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
