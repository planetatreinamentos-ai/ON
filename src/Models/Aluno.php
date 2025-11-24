<?php

namespace PlanetaTreinamentos\Models;

use PDO;

/**
 * Model Aluno
 * Gerencia dados de alunos com múltiplas formas de listagem
 */
class Aluno
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Buscar todos os alunos ativos (padrão)
     */
    public function all(): array
    {
        return $this->getAll(['status' => 1]);
    }

    /**
     * Buscar todos os alunos incluindo inativos
     */
    public function allWithInactive(): array
    {
        return $this->getAll();
    }

    /**
     * NOVA: Listar alunos por ordem alfabética
     */
    public function allOrderByName(): array
    {
        return $this->getAll(['status' => 1], 'nome', 'ASC');
    }

    /**
     * NOVA: Listar alunos por data de criação (mais recentes primeiro)
     */
    public function allOrderByNewest(): array
    {
        return $this->getAll(['status' => 1], 'created_at', 'DESC');
    }

    /**
     * NOVA: Listar alunos por data de criação (mais antigos primeiro)
     */
    public function allOrderByOldest(): array
    {
        return $this->getAll(['status' => 1], 'created_at', 'ASC');
    }

    /**
     * NOVA: Listar alunos por curso
     */
    public function allByCurso(int $cursoId): array
    {
        return $this->getAll(['status' => 1, 'curso_id' => $cursoId]);
    }

    /**
     * NOVA: Listar alunos por professor
     */
    public function allByProfessor(int $professorId): array
    {
        return $this->getAll(['status' => 1, 'professor_id' => $professorId]);
    }

    /**
     * NOVA: Listar apenas melhores alunos
     */
    public function allMelhoresAlunos(): array
    {
        return $this->getAll(['status' => 1, 'melhor_aluno' => 1]);
    }

    /**
     * NOVA: Listar alunos com certificado gerado
     */
    public function allComCertificado(): array
    {
        return $this->getAll(['status' => 1, 'certificado_gerado' => 1]);
    }

    /**
     * NOVA: Listar alunos sem certificado
     */
    public function allSemCertificado(): array
    {
        return $this->getAll(['status' => 1, 'certificado_gerado' => 0]);
    }

    /**
     * NOVA: Listar alunos concluídos
     */
    public function allConcluidos(): array
    {
        return $this->getAll(['status' => 0]);
    }

    /**
     * NOVA: Listar alunos cancelados
     */
    public function allCancelados(): array
    {
        return $this->getAll(['status' => 2]);
    }

    /**
     * NOVA: Buscar alunos por busca de nome/email
     */
    public function search(string $term): array
    {
        $sql = "SELECT a.*, 
                c.nome as curso_nome,
                p.nome as professor_nome,
                ch.horas as carga_horaria_horas
                FROM alunos a
                LEFT JOIN cursos c ON a.curso_id = c.id
                LEFT JOIN professores p ON a.professor_id = p.id
                LEFT JOIN cargas_horarias ch ON a.carga_horaria_id = ch.id
                WHERE a.deleted_at IS NULL
                AND (a.nome LIKE ? OR a.email LIKE ? OR a.alunoid LIKE ?)
                ORDER BY a.nome ASC";

        $searchTerm = "%{$term}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar todos os alunos (método flexível)
     */
    public function getAll(array $filters = [], string $orderBy = 'nome', string $order = 'ASC'): array
    {
        $sql = "SELECT a.*, 
                c.nome as curso_nome,
                p.nome as professor_nome,
                ch.horas as carga_horaria_horas
                FROM alunos a
                LEFT JOIN cursos c ON a.curso_id = c.id
                LEFT JOIN professores p ON a.professor_id = p.id
                LEFT JOIN cargas_horarias ch ON a.carga_horaria_id = ch.id
                WHERE a.deleted_at IS NULL";
        $params = [];

        // Filtros
        foreach ($filters as $field => $value) {
            $sql .= " AND a.{$field} = ?";
            $params[] = $value;
        }

        // Ordenação
        $allowedOrder = ['ASC', 'DESC'];
        $order = in_array(strtoupper($order), $allowedOrder) ? strtoupper($order) : 'ASC';
        $sql .= " ORDER BY a.{$orderBy} {$order}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar todos os alunos
     */
    public function countAll(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM alunos WHERE deleted_at IS NULL";
        $params = [];

        foreach ($filters as $field => $value) {
            $sql .= " AND {$field} = ?";
            $params[] = $value;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return (int) $stmt->fetchColumn();
    }

    /**
     * Buscar aluno por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                c.nome as curso_nome,
                c.imagem_base_certificado as curso_imagem,
                c.frase_certificado,
                p.nome as professor_nome,
                p.professorid as professor_professorid,
                p.assinatura as professor_assinatura,
                ch.horas as carga_horaria_horas
            FROM alunos a
            LEFT JOIN cursos c ON a.curso_id = c.id
            LEFT JOIN professores p ON a.professor_id = p.id
            LEFT JOIN cargas_horarias ch ON a.carga_horaria_id = ch.id
            WHERE a.id = ? AND a.deleted_at IS NULL
        ");
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
     * Alias para getByAlunoId() - compatibilidade
     */
    public function findByAlunoId(string $alunoid): ?array
    {
        return $this->getByAlunoId($alunoid);
    }

    /**
     * Buscar aluno por email
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                c.nome as curso_nome,
                p.nome as professor_nome,
                ch.horas as carga_horaria_horas
            FROM alunos a
            LEFT JOIN cursos c ON a.curso_id = c.id
            LEFT JOIN professores p ON a.professor_id = p.id
            LEFT JOIN cargas_horarias ch ON a.carga_horaria_id = ch.id
            WHERE a.email = ? AND a.deleted_at IS NULL
        ");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Buscar aluno por alunoid
     */
    public function getByAlunoId(string $alunoid): ?array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                c.nome as curso_nome,
                c.imagem_base_certificado as curso_imagem,
                c.frase_certificado,
                p.nome as professor_nome,
                p.professorid as professor_professorid,
                p.assinatura as professor_assinatura,
                ch.horas as carga_horaria_horas
            FROM alunos a
            LEFT JOIN cursos c ON a.curso_id = c.id
            LEFT JOIN professores p ON a.professor_id = p.id
            LEFT JOIN cargas_horarias ch ON a.carga_horaria_id = ch.id
            WHERE a.alunoid = ? AND a.deleted_at IS NULL
        ");
        $stmt->execute([$alunoid]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Criar novo aluno
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO alunos (
                alunoid, nome, email, whatsapp,
                curso_id, professor_id, carga_horaria_id,
                data_inicio, data_fim, nota,
                melhor_aluno, foto_principal,
                status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $data['alunoid'] ?? $this->generateAlunoId(),
            $data['nome'],
            $data['email'],
            $data['whatsapp'] ?? null,
            $data['curso_id'],
            $data['professor_id'],
            $data['carga_horaria_id'],
            $data['data_inicio'],
            $data['data_fim'],
            $data['nota'] ?? 8.0,
            $data['melhor_aluno'] ?? 0,
            $data['foto_principal'] ?? null,
            $data['status'] ?? 1
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualizar aluno
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE alunos SET
                nome = ?,
                email = ?,
                whatsapp = ?,
                curso_id = ?,
                professor_id = ?,
                carga_horaria_id = ?,
                data_inicio = ?,
                data_fim = ?,
                nota = ?,
                melhor_aluno = ?,
                foto_principal = ?,
                status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['nome'],
            $data['email'],
            $data['whatsapp'] ?? null,
            $data['curso_id'],
            $data['professor_id'],
            $data['carga_horaria_id'],
            $data['data_inicio'],
            $data['data_fim'],
            $data['nota'] ?? 8.0,
            $data['melhor_aluno'] ?? 0,
            $data['foto_principal'] ?? null,
            $data['status'] ?? 1,
            $id
        ]);
    }

    /**
     * Deletar aluno (soft delete)
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE alunos SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Gerar alunoid único
     */
    private function generateAlunoId(): string
    {
        do {
            $id = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM alunos WHERE alunoid = ?");
            $stmt->execute([$id]);
            $exists = $stmt->fetchColumn() > 0;
        } while ($exists);

        return $id;
    }

    /**
     * Contar alunos por curso
     */
    public function countByCurso(int $cursoId): int
    {
        return $this->countAll(['curso_id' => $cursoId, 'status' => 1]);
    }

    /**
     * Contar alunos por professor
     */
    public function countByProfessor(int $professorId): int
    {
        return $this->countAll(['professor_id' => $professorId, 'status' => 1]);
    }

    /**
     * Buscar alunos ativos
     */
    public function getAtivos(int $limit = null): array
    {
        $sql = "SELECT a.*, 
                c.nome as curso_nome,
                p.nome as professor_nome,
                ch.horas as carga_horaria_horas
                FROM alunos a
                LEFT JOIN cursos c ON a.curso_id = c.id
                LEFT JOIN professores p ON a.professor_id = p.id
                LEFT JOIN cargas_horarias ch ON a.carga_horaria_id = ch.id
                WHERE a.deleted_at IS NULL AND a.status = 1
                ORDER BY a.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar alunos recentes
     */
    public function getRecentes(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                c.nome as curso_nome,
                p.nome as professor_nome
            FROM alunos a
            LEFT JOIN cursos c ON a.curso_id = c.id
            LEFT JOIN professores p ON a.professor_id = p.id
            WHERE a.deleted_at IS NULL AND a.status = 1
            ORDER BY a.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar melhores alunos
     */
    public function getMelhores(int $limit = null): array
    {
        $sql = "SELECT a.*, 
                c.nome as curso_nome,
                p.nome as professor_nome
                FROM alunos a
                LEFT JOIN cursos c ON a.curso_id = c.id
                LEFT JOIN professores p ON a.professor_id = p.id
                WHERE a.deleted_at IS NULL 
                AND a.status = 1
                AND a.melhor_aluno = 1
                ORDER BY a.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar fotos adicionais do aluno
     */
    public function getFotosAdicionais(int $alunoId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM alunos_fotos 
            WHERE aluno_id = ? 
            ORDER BY ordem ASC, created_at DESC
        ");
        $stmt->execute([$alunoId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar se aluno tem certificado gerado
     */
    public function hasCertificado(int $alunoId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM alunos 
            WHERE id = ? AND certificado_gerado = 1
        ");
        $stmt->execute([$alunoId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Atualizar path do certificado
     */
    public function updateCertificadoPath(int $alunoId, string $path): bool
    {
        $stmt = $this->db->prepare("
            UPDATE alunos SET 
                certificado_path = ?,
                certificado_gerado = 1,
                certificado_emitido = 1,
                certificado_emitido_em = NOW(),
                updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$path, $alunoId]);
    }

    /**
     * Buscar estatísticas gerais
     */
    public function getStats(): array
    {
        return [
            'total_ativos' => $this->countAll(['status' => 1]),
            'total_concluidos' => $this->countAll(['status' => 0]),
            'total_cancelados' => $this->countAll(['status' => 2]),
            'total_com_certificado' => $this->countAll(['certificado_gerado' => 1]),
            'total_sem_certificado' => $this->countAll(['certificado_gerado' => 0, 'status' => 1]),
            'total_melhores_alunos' => $this->countAll(['melhor_aluno' => 1])
        ];
    }

    /**
     * Buscar histórico do aluno
     */
    public function getHistorico(int $alunoId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM alunos_historico 
            WHERE aluno_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$alunoId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar vínculos do aluno (outros cursos)
     */
    public function getVinculos(int $alunoId): array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, c.nome as curso_nome, ch.horas
            FROM alunos_vinculos av
            INNER JOIN alunos a ON av.aluno_vinculado_id = a.id
            INNER JOIN cursos c ON a.curso_id = c.id
            LEFT JOIN cargas_horarias ch ON a.carga_horaria_id = ch.id
            WHERE av.aluno_principal_id = ? AND av.ativo = 1
            ORDER BY a.data_inicio DESC
        ");
        $stmt->execute([$alunoId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
