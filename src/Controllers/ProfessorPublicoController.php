<?php

namespace PlanetaTreinamentos\Controllers;

use PlanetaTreinamentos\Models\Professor;
use PlanetaTreinamentos\Models\Aluno;

/**
 * Controller para página pública do professor
 */
class ProfessorPublicoController extends Controller
{
    private Professor $professorModel;
    private Aluno $alunoModel;

    public function __construct()
    {
        parent::__construct();
        $this->professorModel = new Professor($this->db);
        $this->alunoModel = new Aluno($this->db);
    }

    /**
     * Exibir página pública do professor
     * URL: /professor?id={professorid}
     */
    public function show(): void
    {
        // Validar ID
        $professorId = $_GET['id'] ?? '';
        
        if (empty($professorId)) {
            $this->redirect('/erro?msg=Professor não encontrado');
            return;
        }

        // Buscar professor por professorid
        $professor = $this->professorModel->getByProfessorId($professorId);

        if (!$professor) {
            $this->redirect('/erro?msg=Professor não encontrado');
            return;
        }

        // Verificar se professor está ativo
        if ($professor['status'] !== 'ativo') {
            $this->redirect('/erro?msg=Professor não disponível');
            return;
        }

        // Buscar alunos do professor (para estatísticas)
        $alunos = $this->alunoModel->getByProfessor($professor['id']);
        $totalAlunos = count($alunos);

        // Calcular média das notas
        $somaNotas = 0;
        $melhorAluno = null;
        $maiorNota = 0;

        foreach ($alunos as $aluno) {
            $somaNotas += $aluno['nota'];
            if ($aluno['nota'] > $maiorNota) {
                $maiorNota = $aluno['nota'];
                $melhorAluno = $aluno;
            }
        }

        $mediaNotas = $totalAlunos > 0 ? round($somaNotas / $totalAlunos, 1) : 0;

        // Buscar configurações da empresa
        $config = $this->getConfig();

        // Dados para a view
        $data = [
            'pageTitle' => $professor['nome'] . ' - Professor | ' . $config['nome_empresa'],
            'metaDescription' => 'Conheça ' . $professor['nome'] . ', professor na ' . $config['nome_empresa'],
            'professor' => $professor,
            'totalAlunos' => $totalAlunos,
            'mediaNotas' => $mediaNotas,
            'melhorAluno' => $melhorAluno,
            'config' => $config
        ];

        // Log de acesso
        $this->log('Acesso à página do professor: ' . $professor['nome'], 'info', [
            'professorid' => $professorId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        // Renderizar view
        $this->render('public/professor', $data, 'public');
    }

    /**
     * API para buscar professores (AJAX)
     */
    public function getProfessores(): void
    {
        header('Content-Type: application/json');
        
        try {
            $professores = $this->professorModel->getAll(['status' => 'ativo']);
            
            $result = [];
            foreach ($professores as $prof) {
                $result[] = [
                    'id' => $prof['professorid'],
                    'nome' => $prof['nome'],
                    'titulo' => $prof['titulo'] ?? '',
                    'foto' => $prof['foto'] ?? '/assets/img/default-professor.jpg',
                    'descricao_curta' => substr($prof['descricao'] ?? '', 0, 150) . '...'
                ];
            }

            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao buscar professores'
            ]);
        }
    }
}