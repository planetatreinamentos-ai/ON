<?php

namespace PlanetaTreinamentos\Controllers;

use PlanetaTreinamentos\Core\Controller;
use PlanetaTreinamentos\Models\Certificado;
use PlanetaTreinamentos\Models\Aluno;
use PlanetaTreinamentos\Services\CertificadoService;

/**
 * Controller de Certificados
 * 
 * IMPORTANTE: NÃO faz queries diretas, USA os Models!
 */
class CertificadoController extends Controller
{
    private Certificado $certificadoModel;
    private Aluno $alunoModel;
    private CertificadoService $certificadoService;

    public function __construct()
    {
        parent::__construct();
        
        $this->certificadoModel = new Certificado($this->db);
        $this->alunoModel = new Aluno($this->db);
        $this->certificadoService = new CertificadoService();
    }

    /**
     * Lista todos os certificados
     */
    public function index(): void
    {
        try {
            // USA o Model, NÃO faz query direta!
            $certificados = $this->certificadoModel->getAll();
            
            $this->view('admin/certificados/index', [
                'title' => 'Certificados',
                'certificados' => $certificados
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar certificados', [
                'error' => $e->getMessage()
            ]);
            
            $this->view('admin/certificados/index', [
                'title' => 'Certificados',
                'certificados' => [],
                'error' => 'Erro ao carregar certificados'
            ]);
        }
    }

    /**
     * Exibe detalhes do certificado
     */
    public function show(int $id): void
    {
        try {
            $certificado = $this->certificadoModel->getById($id);
            
            if (!$certificado) {
                $this->redirect('/admin/certificados', 'Certificado não encontrado', 'error');
                return;
            }
            
            $this->view('admin/certificados/show', [
                'title' => 'Detalhes do Certificado',
                'certificado' => $certificado
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Erro ao exibir certificado', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            $this->redirect('/admin/certificados', 'Erro ao carregar certificado', 'error');
        }
    }

    /**
     * Formulário de geração de certificado
     */
    public function create(): void
    {
        try {
            // Lista alunos ativos sem certificado
            $alunos = $this->alunoModel->allSemCertificado();
            
            $this->view('admin/certificados/create', [
                'title' => 'Gerar Certificado',
                'alunos' => $alunos
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Erro ao exibir formulário de certificado', [
                'error' => $e->getMessage()
            ]);
            
            $this->redirect('/admin/certificados', 'Erro ao carregar formulário', 'error');
        }
    }

    /**
     * Gera certificado para um aluno
     */
    public function store(): void
    {
        try {
            $alunoId = (int) ($_POST['aluno_id'] ?? 0);
            
            if (!$alunoId) {
                $this->redirect('/admin/certificados/create', 'Aluno não informado', 'error');
                return;
            }
            
            // Busca dados do aluno
            $aluno = $this->alunoModel->getById($alunoId);
            
            if (!$aluno) {
                $this->redirect('/admin/certificados/create', 'Aluno não encontrado', 'error');
                return;
            }
            
            // Verifica se já tem certificado
            if ($aluno['certificado_gerado']) {
                $this->redirect('/admin/certificados', 'Este aluno já possui certificado', 'warning');
                return;
            }
            
            // Gera o certificado
            $resultado = $this->certificadoService->gerar($aluno);
            
            if ($resultado['success']) {
                $this->logger->info('Certificado gerado', [
                    'aluno_id' => $alunoId,
                    'aluno_nome' => $aluno['nome']
                ]);
                
                $this->redirect('/admin/certificados', 'Certificado gerado com sucesso!', 'success');
            } else {
                throw new \Exception($resultado['message'] ?? 'Erro ao gerar certificado');
            }
            
        } catch (\Exception $e) {
            $this->logger->error('Erro ao gerar certificado', [
                'error' => $e->getMessage(),
                'aluno_id' => $alunoId ?? null
            ]);
            
            $this->redirect('/admin/certificados/create', 'Erro ao gerar certificado: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Gera certificado em lote (múltiplos alunos)
     */
    public function batch(): void
    {
        try {
            $alunoIds = $_POST['aluno_ids'] ?? [];
            
            if (empty($alunoIds)) {
                $this->redirect('/admin/certificados', 'Nenhum aluno selecionado', 'error');
                return;
            }
            
            $gerados = 0;
            $erros = 0;
            
            foreach ($alunoIds as $alunoId) {
                try {
                    $aluno = $this->alunoModel->getById((int) $alunoId);
                    
                    if ($aluno && !$aluno['certificado_gerado']) {
                        $resultado = $this->certificadoService->gerar($aluno);
                        
                        if ($resultado['success']) {
                            $gerados++;
                        } else {
                            $erros++;
                        }
                    }
                    
                } catch (\Exception $e) {
                    $erros++;
                    $this->logger->error('Erro em lote', [
                        'aluno_id' => $alunoId,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $message = "Certificados gerados: {$gerados}";
            if ($erros > 0) {
                $message .= " | Erros: {$erros}";
            }
            
            $this->redirect('/admin/certificados', $message, $erros > 0 ? 'warning' : 'success');
            
        } catch (\Exception $e) {
            $this->logger->error('Erro ao gerar certificados em lote', [
                'error' => $e->getMessage()
            ]);
            
            $this->redirect('/admin/certificados', 'Erro ao gerar certificados em lote', 'error');
        }
    }

    /**
     * Regenera certificado existente
     */
    public function regenerate(int $id): void
    {
        try {
            $aluno = $this->alunoModel->getById($id);
            
            if (!$aluno) {
                $this->redirect('/admin/certificados', 'Aluno não encontrado', 'error');
                return;
            }
            
            // Gera novamente
            $resultado = $this->certificadoService->gerar($aluno, true); // force = true
            
            if ($resultado['success']) {
                $this->logger->info('Certificado regenerado', [
                    'aluno_id' => $id,
                    'aluno_nome' => $aluno['nome']
                ]);
                
                $this->redirect('/admin/certificados', 'Certificado regenerado com sucesso!', 'success');
            } else {
                throw new \Exception($resultado['message'] ?? 'Erro ao regenerar certificado');
            }
            
        } catch (\Exception $e) {
            $this->logger->error('Erro ao regenerar certificado', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            $this->redirect('/admin/certificados', 'Erro ao regenerar certificado', 'error');
        }
    }

    /**
     * Download do certificado
     */
    public function download(int $id): void
    {
        try {
            $aluno = $this->alunoModel->getById($id);
            
            if (!$aluno || !$aluno['certificado_gerado']) {
                $this->redirect('/admin/certificados', 'Certificado não encontrado', 'error');
                return;
            }
            
            $path = $aluno['certificado_path'];
            
            if (!$path || !file_exists($path)) {
                $this->redirect('/admin/certificados', 'Arquivo de certificado não encontrado', 'error');
                return;
            }
            
            // Download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="certificado-' . $aluno['alunoid'] . '.pdf"');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
            
        } catch (\Exception $e) {
            $this->logger->error('Erro ao fazer download de certificado', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            $this->redirect('/admin/certificados', 'Erro ao fazer download', 'error');
        }
    }

    /**
     * Visualizar certificado (inline)
     */
    public function view(int $id): void
    {
        try {
            $aluno = $this->alunoModel->getById($id);
            
            if (!$aluno || !$aluno['certificado_gerado']) {
                echo "Certificado não encontrado";
                return;
            }
            
            $path = $aluno['certificado_path'];
            
            if (!$path || !file_exists($path)) {
                echo "Arquivo de certificado não encontrado";
                return;
            }
            
            // Visualização inline
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="certificado-' . $aluno['alunoid'] . '.pdf"');
            readfile($path);
            exit;
            
        } catch (\Exception $e) {
            $this->logger->error('Erro ao visualizar certificado', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            echo "Erro ao visualizar certificado";
        }
    }

    /**
     * Estatísticas de certificados
     */
    public function stats(): void
    {
        try {
            $stats = $this->certificadoModel->getStats();
            
            $this->json($stats);
            
        } catch (\Exception $e) {
            $this->logger->error('Erro ao buscar estatísticas de certificados', [
                'error' => $e->getMessage()
            ]);
            
            $this->json(['error' => 'Erro ao carregar estatísticas'], 500);
        }
    }

    /**
     * Deleta certificado
     */
    public function delete(int $id): void
    {
        try {
            $aluno = $this->alunoModel->getById($id);
            
            if (!$aluno) {
                $this->redirect('/admin/certificados', 'Aluno não encontrado', 'error');
                return;
            }
            
            // Remove arquivo físico se existir
            if ($aluno['certificado_path'] && file_exists($aluno['certificado_path'])) {
                unlink($aluno['certificado_path']);
            }
            
            // Atualiza registro
            $stmt = $this->db->prepare("
                UPDATE alunos SET 
                    certificado_gerado = 0,
                    certificado_path = NULL,
                    certificado_url = NULL,
                    certificado_emitido = 0,
                    certificado_emitido_em = NULL,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            $this->logger->info('Certificado deletado', [
                'aluno_id' => $id,
                'aluno_nome' => $aluno['nome']
            ]);
            
            $this->redirect('/admin/certificados', 'Certificado deletado com sucesso', 'success');
            
        } catch (\Exception $e) {
            $this->logger->error('Erro ao deletar certificado', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            $this->redirect('/admin/certificados', 'Erro ao deletar certificado', 'error');
        }
    }
}
