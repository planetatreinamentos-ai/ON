<?php

namespace PlanetaTreinamentos\Models;

use PDO;

/**
 * Model Interessado
 * Gerencia leads de pessoas interessadas nos cursos
 */
class Interessado
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Criar novo interessado
     */
    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO interessados (
                nome, email, whatsapp, curso_interesse, 
                mensagem, origem, ip_address, created_at
            ) VALUES (
                :nome, :email, :whatsapp, :curso_interesse,
                :mensagem, :origem, :ip_address, NOW()
            )";

            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                'nome' => $data['nome'],
                'email' => $data['email'],
                'whatsapp' => $data['whatsapp'] ?? null,
                'curso_interesse' => $data['curso_interesse'] ?? null,
                'mensagem' => $data['mensagem'] ?? null,
                'origem' => $data['origem'] ?? 'homepage',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
            ]);
        } catch (\PDOException $e) {
            error_log("Erro ao criar interessado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar todos os interessados
     */
    public function getAll(array $filters = []): array
    {
        try {
            $sql = "SELECT i.*, c.nome as curso_nome
                    FROM interessados i
                    LEFT JOIN cursos c ON i.curso_interesse = c.id
                    WHERE 1=1";
            
            $params = [];

            if (!empty($filters['data_inicio'])) {
                $sql .= " AND DATE(i.created_at) >= :data_inicio";
                $params['data_inicio'] = $filters['data_inicio'];
            }

            if (!empty($filters['data_fim'])) {
                $sql .= " AND DATE(i.created_at) <= :data_fim";
                $params['data_fim'] = $filters['data_fim'];
            }

            if (!empty($filters['curso_id'])) {
                $sql .= " AND i.curso_interesse = :curso_id";
                $params['curso_id'] = $filters['curso_id'];
            }

            if (!empty($filters['status'])) {
                $sql .= " AND i.status = :status";
                $params['status'] = $filters['status'];
            }

            $sql .= " ORDER BY i.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erro ao buscar interessados: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar interessado por ID
     */
    public function getById(int $id): ?array
    {
        try {
            $sql = "SELECT i.*, c.nome as curso_nome
                    FROM interessados i
                    LEFT JOIN cursos c ON i.curso_interesse = c.id
                    WHERE i.id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            error_log("Erro ao buscar interessado: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Atualizar status do interessado
     */
    public function updateStatus(int $id, string $status, ?string $observacao = null): bool
    {
        try {
            $sql = "UPDATE interessados 
                    SET status = :status, 
                        observacao = :observacao,
                        updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                'id' => $id,
                'status' => $status,
                'observacao' => $observacao
            ]);
        } catch (\PDOException $e) {
            error_log("Erro ao atualizar status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar se email já existe (últimos 30 dias)
     */
    public function emailExists(string $email): bool
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM interessados 
                    WHERE email = :email 
                    AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result['total'] ?? 0) > 0;
        } catch (\PDOException $e) {
            error_log("Erro ao verificar email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Contar interessados por período
     */
    public function countByPeriod(string $inicio, string $fim): int
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM interessados 
                    WHERE DATE(created_at) BETWEEN :inicio AND :fim";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['inicio' => $inicio, 'fim' => $fim]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        } catch (\PDOException $e) {
            error_log("Erro ao contar interessados: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Deletar interessado
     */
    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM interessados WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            error_log("Erro ao deletar interessado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Estatísticas de interessados
     */
    public function getStatistics(): array
    {
        try {
            $stats = [
                'total' => 0,
                'mes_atual' => 0,
                'pendentes' => 0,
                'convertidos' => 0,
                'por_curso' => []
            ];

            // Total geral
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM interessados");
            $stats['total'] = (int)$stmt->fetchColumn();

            // Mês atual
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM interessados WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
            $stats['mes_atual'] = (int)$stmt->fetchColumn();

            // Pendentes
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM interessados WHERE status = 'pendente'");
            $stats['pendentes'] = (int)$stmt->fetchColumn();

            // Convertidos
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM interessados WHERE status = 'convertido'");
            $stats['convertidos'] = (int)$stmt->fetchColumn();

            // Por curso
            $stmt = $this->db->query("
                SELECT c.nome, COUNT(*) as total
                FROM interessados i
                LEFT JOIN cursos c ON i.curso_interesse = c.id
                WHERE i.curso_interesse IS NOT NULL
                GROUP BY c.nome
                ORDER BY total DESC
            ");
            $stats['por_curso'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $stats;
        } catch (\PDOException $e) {
            error_log("Erro ao buscar estatísticas: " . $e->getMessage());
            return [];
        }
    }
}