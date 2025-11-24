<?php

namespace PlanetaTreinamentos\Models;

use PDO;

/**
 * Model CargaHoraria
 * Gerencia cargas horárias dos cursos
 * 
 * Baseado na estrutura REAL do banco de dados u220553158_sistema
 * Estrutura: id, horas, status, created_at, updated_at
 */
class CargaHoraria
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Buscar todas as cargas horárias ativas (alias)
     */
    public function all(): array
    {
        return $this->getAll(['status' => 1]);
    }

    /**
     * Buscar todas as cargas horárias incluindo inativas
     */
    public function allWithInactive(): array
    {
        return $this->getAll();
    }

    /**
     * Buscar todas as cargas horárias
     */
    public function getAll(array $filters = []): array
    {
        $sql = "SELECT * FROM cargas_horarias WHERE 1=1";
        $params = [];

        // Filtro por status (1=ativo, 0=inativo)
        if (isset($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        $sql .= " ORDER BY horas ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar carga horária por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM cargas_horarias WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Criar nova carga horária
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO cargas_horarias (
                horas, status, created_at
            ) VALUES (?, ?, NOW())
        ");

        $stmt->execute([
            $data['horas'],
            $data['status'] ?? 1
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualizar carga horária
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE cargas_horarias SET
                horas = ?,
                status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['horas'],
            $data['status'] ?? 1,
            $id
        ]);
    }

    /**
     * Deletar carga horária
     */
    public function delete(int $id): bool
    {
        // Verificar se está em uso
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM alunos WHERE carga_horaria_id = ?
        ");
        $stmt->execute([$id]);
        $inUse = $stmt->fetchColumn() > 0;

        if ($inUse) {
            return false; // Não pode deletar se estiver em uso
        }

        $stmt = $this->db->prepare("DELETE FROM cargas_horarias WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Buscar cargas horárias ativas
     */
    public function getAtivas(): array
    {
        return $this->getAll(['status' => 1]);
    }

    /**
     * Contar quantos alunos usam esta carga horária
     */
    public function countAlunos(int $cargaHorariaId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM alunos 
            WHERE carga_horaria_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$cargaHorariaId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Buscar a carga horária mais usada
     */
    public function getMaisUsada(): ?array
    {
        $stmt = $this->db->query("
            SELECT ch.*, COUNT(a.id) as total_alunos
            FROM cargas_horarias ch
            LEFT JOIN alunos a ON ch.id = a.carga_horaria_id AND a.deleted_at IS NULL
            WHERE ch.status = 1
            GROUP BY ch.id
            ORDER BY total_alunos DESC
            LIMIT 1
        ");
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Listar com contagem de alunos
     */
    public function getAllWithCount(): array
    {
        $stmt = $this->db->query("
            SELECT ch.*, COUNT(a.id) as total_alunos
            FROM cargas_horarias ch
            LEFT JOIN alunos a ON ch.id = a.carga_horaria_id AND a.deleted_at IS NULL
            GROUP BY ch.id
            ORDER BY ch.horas ASC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar se carga horária existe
     */
    public function exists(int $horas): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM cargas_horarias WHERE horas = ?
        ");
        $stmt->execute([$horas]);
        return $stmt->fetchColumn() > 0;
    }
}
