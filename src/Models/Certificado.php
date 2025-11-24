<?php

namespace PlanetaTreinamentos\Models;

use PDO;

/**
 * Model Certificado
 * Gerencia dados e geração de certificados
 * 
 * NOTA: Os certificados são armazenados na tabela 'alunos' no campo 'certificado_path',
 * não existe uma tabela separada de certificados.
 */
class Certificado
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Buscar todos os certificados gerados
     */
    public function getAll(array $filters = []): array
    {
        $sql = "SELECT a.id, a.alunoid, a.nome, a.email,
                a.certificado_path, a.data_inicio, a.data_fim, a.created_at,
                c.nome as curso_nome, 
                p.nome as professor_nome
                FROM alunos a
                LEFT JOIN cursos c ON a.curso_id = c.id
                LEFT JOIN professores p ON a.professor_id = p.id
                WHERE a.deleted_at IS NULL
                AND a.certificado_path IS NOT NULL 
                AND a.certificado_path != ''";
        
        $params = [];

        // Filtro por curso
        if (isset($filters['curso_id'])) {
            $sql .= " AND a.curso_id = ?";
            $params[] = $filters['curso_id'];
        }

        // Filtro por status
        if (isset($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }

        $sql .= " ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar certificados gerados
     */
    public function countAll(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM alunos 
                WHERE deleted_at IS NULL
                AND certificado_path IS NOT NULL 
                AND certificado_path != ''";
        
        $params = [];

        // Filtro por curso
        if (isset($filters['curso_id'])) {
            $sql .= " AND curso_id = ?";
            $params[] = $filters['curso_id'];
        }

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
     * Buscar certificado por alunoid
     */
    public function getByAlunoId(string $alunoid): ?array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                c.nome as curso_nome,
                c.imagem_base as curso_imagem,
                p.nome as professor_nome,
                p.professorid as professor_professorid,
                p.assinatura as professor_assinatura
            FROM alunos a
            LEFT JOIN cursos c ON a.curso_id = c.id
            LEFT JOIN professores p ON a.professor_id = p.id
            WHERE a.alunoid = ? 
            AND a.deleted_at IS NULL
        ");
        
        $stmt->execute([$alunoid]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Buscar certificado por ID do aluno
     */
    public function getByAlunoDbId(int $alunoId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                c.nome as curso_nome,
                c.imagem_base as curso_imagem,
                p.nome as professor_nome,
                p.professorid as professor_professorid,
                p.assinatura as professor_assinatura
            FROM alunos a
            LEFT JOIN cursos c ON a.curso_id = c.id
            LEFT JOIN professores p ON a.professor_id = p.id
            WHERE a.id = ? 
            AND a.deleted_at IS NULL
        ");
        
        $stmt->execute([$alunoId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Verificar se certificado já foi gerado
     */
    public function exists(int $alunoId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM alunos 
            WHERE id = ? 
            AND certificado_path IS NOT NULL 
            AND certificado_path != ''
        ");
        
        $stmt->execute([$alunoId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Atualizar path do certificado no banco
     */
    public function updatePath(int $alunoId, string $path): bool
    {
        $stmt = $this->db->prepare("
            UPDATE alunos SET 
                certificado_path = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([$path, $alunoId]);
    }

    /**
     * Buscar certificados recentes
     */
    public function getRecentes(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT a.id, a.alunoid, a.nome,
                c.nome as curso_nome,
                a.certificado_path,
                a.created_at
            FROM alunos a
            LEFT JOIN cursos c ON a.curso_id = c.id
            WHERE a.deleted_at IS NULL
            AND a.certificado_path IS NOT NULL 
            AND a.certificado_path != ''
            ORDER BY a.created_at DESC
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar certificados por curso
     */
    public function getByCurso(int $cursoId): array
    {
        $stmt = $this->db->prepare("
            SELECT a.id, a.alunoid, a.nome, a.email,
                a.certificado_path, a.data_inicio, a.data_fim,
                c.nome as curso_nome,
                p.nome as professor_nome
            FROM alunos a
            LEFT JOIN cursos c ON a.curso_id = c.id
            LEFT JOIN professores p ON a.professor_id = p.id
            WHERE a.curso_id = ?
            AND a.deleted_at IS NULL
            AND a.certificado_path IS NOT NULL 
            AND a.certificado_path != ''
            ORDER BY a.nome ASC
        ");
        
        $stmt->execute([$cursoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Validar certificado por alunoid
     */
    public function validar(string $alunoid): ?array
    {
        return $this->getByAlunoId($alunoid);
    }

    /**
     * Buscar estatísticas de certificados
     */
    public function getStats(): array
    {
        // Total de certificados gerados
        $stmt = $this->db->query("
            SELECT COUNT(*) as total 
            FROM alunos 
            WHERE deleted_at IS NULL 
            AND certificado_path IS NOT NULL 
            AND certificado_path != ''
        ");
        $totalGerados = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total de alunos sem certificado
        $stmt = $this->db->query("
            SELECT COUNT(*) as total 
            FROM alunos 
            WHERE deleted_at IS NULL 
            AND status = 'ativo'
            AND (certificado_path IS NULL OR certificado_path = '')
        ");
        $semCertificado = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Certificados por curso
        $stmt = $this->db->query("
            SELECT c.nome as curso, COUNT(*) as total
            FROM alunos a
            LEFT JOIN cursos c ON a.curso_id = c.id
            WHERE a.deleted_at IS NULL
            AND a.certificado_path IS NOT NULL 
            AND a.certificado_path != ''
            GROUP BY c.id, c.nome
            ORDER BY total DESC
        ");
        $porCurso = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total_gerados' => $totalGerados,
            'sem_certificado' => $semCertificado,
            'por_curso' => $porCurso
        ];
    }

    /**
     * Deletar certificado (remove path do banco e arquivo físico)
     */
    public function delete(int $alunoId): bool
    {
        // Buscar path atual
        $stmt = $this->db->prepare("SELECT certificado_path FROM alunos WHERE id = ?");
        $stmt->execute([$alunoId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['certificado_path'])) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . $result['certificado_path'];
            
            // Remove arquivo físico se existir
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        // Limpa path do banco
        $stmt = $this->db->prepare("
            UPDATE alunos SET 
                certificado_path = NULL,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([$alunoId]);
    }

    /**
     * Regenerar certificado (apaga o antigo antes)
     */
    public function regenerar(int $alunoId): bool
    {
        return $this->delete($alunoId);
    }
}
