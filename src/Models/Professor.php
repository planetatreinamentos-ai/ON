<?php

namespace PlanetaTreinamentos\Models;

use PDO;

/**
 * Model Professor
 * Gerencia dados de professores
 */
class Professor
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Buscar todos os professores ativos (alias)
     */
    public function all(): array
    {
        return $this->getAll(['status' => 'ativo']);
    }

    /**
     * Buscar todos os professores incluindo inativos
     */
    public function allWithInactive(): array
    {
        return $this->getAll();
    }

    /**
     * Buscar todos os professores
     */
    public function getAll(array $filters = []): array
    {
        $sql = "SELECT * FROM professores WHERE 1=1";
        $params = [];

        // Filtro por status
        if (isset($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        $sql .= " ORDER BY nome ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar todos os professores
     */
    public function countAll(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM professores WHERE 1=1";
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
     * Contar professores ativos
     */
    public function countAtivos(): int
    {
        return $this->countAll(['status' => 'ativo']);
    }

    /**
     * Buscar professor por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM professores WHERE id = ?");
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
     * Buscar professor por professorid
     */
    public function getByProfessorId(string $professorid): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM professores WHERE professorid = ?");
        $stmt->execute([$professorid]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Alias para getByProfessorId() - compatibilidade
     */
    public function findByProfessorId(string $professorid): ?array
    {
        return $this->getByProfessorId($professorid);
    }

    /**
     * Criar novo professor
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO professores (
                professorid, nome, email, telefone,
                especialidade, assinatura, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $data['professorid'] ?? $this->generateProfessorId(),
            $data['nome'],
            $data['email'] ?? null,
            $data['telefone'] ?? null,
            $data['especialidade'] ?? null,
            $data['assinatura'] ?? null,
            $data['status'] ?? 'ativo'
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualizar professor
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE professores SET
                nome = ?,
                email = ?,
                telefone = ?,
                especialidade = ?,
                assinatura = ?,
                status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['nome'],
            $data['email'] ?? null,
            $data['telefone'] ?? null,
            $data['especialidade'] ?? null,
            $data['assinatura'] ?? null,
            $data['status'] ?? 'ativo',
            $id
        ]);
    }

    /**
     * Deletar professor (soft delete se existir a coluna, senão hard delete)
     */
    public function delete(int $id): bool
    {
        // Verificar se tem alunos vinculados
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM alunos WHERE professor_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        $hasAlunos = $stmt->fetchColumn() > 0;

        if ($hasAlunos) {
            return false; // Não pode deletar se tiver alunos
        }

        // Verificar se existe coluna deleted_at
        if ($this->columnExists('professores', 'deleted_at')) {
            $stmt = $this->db->prepare("UPDATE professores SET deleted_at = NOW() WHERE id = ?");
        } else {
            $stmt = $this->db->prepare("DELETE FROM professores WHERE id = ?");
        }
        
        return $stmt->execute([$id]);
    }

    /**
     * Gerar professorid único
     */
    private function generateProfessorId(): string
    {
        do {
            $id = 'PROF' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM professores WHERE professorid = ?");
            $stmt->execute([$id]);
            $exists = $stmt->fetchColumn() > 0;
        } while ($exists);

        return $id;
    }

    /**
     * Contar alunos por professor
     */
    public function countAlunos(int $professorId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM alunos 
            WHERE professor_id = ? AND deleted_at IS NULL AND status = 'ativo'
        ");
        $stmt->execute([$professorId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Buscar professores ativos
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
