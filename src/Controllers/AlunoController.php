<?php
/**
 * Controller de Alunos - COM FILTROS v5
 * 
 * ✅ Filtros implementados (busca, status, curso, professor, ordenação)
 * ✅ Passa PDO para Models
 * ✅ Compatível com estrutura descoberta
 * 
 * @package PlanetaTreinamentos\Controllers
 */

namespace PlanetaTreinamentos\Controllers;

use PlanetaTreinamentos\Core\View;
use PlanetaTreinamentos\Core\Session;
use PlanetaTreinamentos\Core\CSRF;
use PlanetaTreinamentos\Core\Database;
use PlanetaTreinamentos\Models\Aluno;
use PlanetaTreinamentos\Models\Curso;
use PlanetaTreinamentos\Models\Professor;
use PlanetaTreinamentos\Models\CargaHoraria;
use PlanetaTreinamentos\Helpers\Logger;

class AlunoController extends Controller
{
    private $alunoModel;
    private $cursoModel;
    private $professorModel;
    private $cargaHorariaModel;

    public function __construct()
    {
        parent::__construct();
        
        $db = Database::getInstance()->getConnection();
        
        $this->alunoModel = new Aluno($db);
        $this->cursoModel = new Curso($db);
        $this->professorModel = new Professor($db);
        $this->cargaHorariaModel = new CargaHoraria($db);
    }

    /**
     * Lista alunos COM FILTROS
     */
    public function index(): void
    {
        try {
            // ===== PEGA FILTROS DA URL =====
            $busca = $_GET['q'] ?? '';
            $filtro = $_GET['filtro'] ?? 'todos';
            $cursoId = $_GET['curso_id'] ?? null;
            $professorId = $_GET['professor_id'] ?? null;
            $ordem = $_GET['ordem'] ?? 'nome';

            // ===== APLICA FILTROS =====
            
            // Prioridade 1: BUSCA por termo
            if ($busca) {
                $alunos = $this->alunoModel->search($busca);
                
            // Prioridade 2: CURSO específico
            } elseif ($cursoId) {
                $alunos = $this->alunoModel->allByCurso((int) $cursoId);
                
            // Prioridade 3: PROFESSOR específico
            } elseif ($professorId) {
                $alunos = $this->alunoModel->allByProfessor((int) $professorId);
                
            // Prioridade 4: FILTROS de status
            } else {
                switch ($filtro) {
                    case 'ativos':
                        $alunos = $this->alunoModel->all(); // status = 1
                        break;
                        
                    case 'concluidos':
                        $alunos = $this->alunoModel->allConcluidos(); // status = 0
                        break;
                        
                    case 'cancelados':
                        $alunos = $this->alunoModel->allCancelados(); // status = 2
                        break;
                        
                    case 'sem-certificado':
                        $alunos = $this->alunoModel->allSemCertificado(); // certificado_gerado = 0
                        break;
                        
                    case 'com-certificado':
                        $alunos = $this->alunoModel->allComCertificado(); // certificado_gerado = 1
                        break;
                        
                    case 'melhores':
                        $alunos = $this->alunoModel->allMelhoresAlunos(); // melhor_aluno = 1
                        break;
                        
                    case 'todos':
                    default:
                        // Aplica ORDENAÇÃO
                        switch ($ordem) {
                            case 'nome':
                                $alunos = $this->alunoModel->allOrderByName();
                                break;
                            case 'recente':
                                $alunos = $this->alunoModel->allOrderByNewest();
                                break;
                            case 'antigo':
                                $alunos = $this->alunoModel->allOrderByOldest();
                                break;
                            default:
                                $alunos = $this->alunoModel->all();
                        }
                }
            }

            // ===== BUSCA DADOS PARA OS SELECTS =====
            $cursos = $this->cursoModel->all();
            $professores = $this->professorModel->all();
            
            // ===== BUSCA ESTATÍSTICAS =====
            $stats = $this->alunoModel->getStats();

            // ===== RENDERIZA VIEW =====
            View::render('alunos/index', [
                'title' => 'Gerenciar Alunos',
                'alunos' => $alunos,
                'cursos' => $cursos,
                'professores' => $professores,
                'stats' => $stats,
                'filtro_atual' => $filtro,
                'ordem_atual' => $ordem,
                'busca_atual' => $busca,
                'curso_atual' => $cursoId,
                'professor_atual' => $professorId
            ]);
            
        } catch (\Exception $e) {
            Logger::error('Erro ao listar alunos com filtros: ' . $e->getMessage());
            
            Session::setFlash('error', 'Erro ao carregar alunos. Tente novamente.');
            
            View::render('alunos/index', [
                'title' => 'Gerenciar Alunos',
                'alunos' => [],
                'cursos' => [],
                'professores' => [],
                'stats' => [
                    'total_ativos' => 0,
                    'total_concluidos' => 0,
                    'total_cancelados' => 0,
                    'total_sem_certificado' => 0
                ]
            ]);
        }
    }

    /**
     * Formulário de novo aluno
     */
    public function create(): void
    {
        try {
            $cursos = $this->cursoModel->all();
            $professores = $this->professorModel->all();
            $cargasHorarias = $this->cargaHorariaModel->all();

            View::render('alunos/create', [
                'title' => 'Novo Aluno',
                'cursos' => $cursos,
                'professores' => $professores,
                'cargas_horarias' => $cargasHorarias,
                'csrf_token' => CSRF::generate()
            ]);
        } catch (\Exception $e) {
            Logger::error('Erro ao carregar formulário de novo aluno: ' . $e->getMessage());
            Session::setFlash('error', 'Erro ao carregar formulário.');
            $this->redirect('/admin/alunos');
        }
    }

    /**
     * Salva novo aluno
     */
    public function store(): void
    {
        try {
            if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
                throw new \Exception('Token CSRF inválido');
            }

            $data = [
                'alunoid' => $_POST['alunoid'] ?? '',
                'nome' => $_POST['nome'] ?? '',
                'email' => $_POST['email'] ?? '',
                'whatsapp' => $_POST['whatsapp'] ?? '',
                'curso_id' => $_POST['curso_id'] ?? null,
                'professor_id' => $_POST['professor_id'] ?? null,
                'carga_horaria_id' => $_POST['carga_horaria_id'] ?? null,
                'data_inicio' => $_POST['data_inicio'] ?? null,
                'data_fim' => $_POST['data_fim'] ?? null,
                'nota' => $_POST['nota'] ?? null,
                'melhor_aluno' => isset($_POST['melhor_aluno']) ? 1 : 0,
                'status' => $_POST['status'] ?? 1
            ];

            // Upload de foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                $fileName = uniqid() . '_' . time() . '.jpeg';
                $uploadPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
                    $data['foto_principal'] = $fileName;
                }
            }

            $alunoId = $this->alunoModel->create($data);

            Logger::info("Aluno criado: ID {$alunoId}");
            Session::setFlash('success', 'Aluno cadastrado com sucesso!');
            $this->redirect('/admin/alunos');

        } catch (\Exception $e) {
            Logger::error('Erro ao criar aluno: ' . $e->getMessage());
            Session::setFlash('error', 'Erro ao cadastrar aluno: ' . $e->getMessage());
            $this->redirect('/admin/alunos/create');
        }
    }

    /**
     * Formulário de edição
     */
    public function edit(int $id): void
    {
        try {
            $aluno = $this->alunoModel->findById($id); // Usa findById() - alias
            
            if (!$aluno) {
                throw new \Exception('Aluno não encontrado');
            }

            $cursos = $this->cursoModel->all();
            $professores = $this->professorModel->all();
            $cargasHorarias = $this->cargaHorariaModel->all();

            View::render('alunos/edit', [
                'title' => 'Editar Aluno',
                'aluno' => $aluno,
                'cursos' => $cursos,
                'professores' => $professores,
                'cargas_horarias' => $cargasHorarias,
                'csrf_token' => CSRF::generate()
            ]);
        } catch (\Exception $e) {
            Logger::error('Erro ao carregar edição de aluno: ' . $e->getMessage());
            Session::setFlash('error', 'Erro ao carregar aluno.');
            $this->redirect('/admin/alunos');
        }
    }

    /**
     * Atualiza aluno
     */
    public function update(int $id): void
    {
        try {
            if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
                throw new \Exception('Token CSRF inválido');
            }

            $data = [
                'alunoid' => $_POST['alunoid'] ?? '',
                'nome' => $_POST['nome'] ?? '',
                'email' => $_POST['email'] ?? '',
                'whatsapp' => $_POST['whatsapp'] ?? '',
                'curso_id' => $_POST['curso_id'] ?? null,
                'professor_id' => $_POST['professor_id'] ?? null,
                'carga_horaria_id' => $_POST['carga_horaria_id'] ?? null,
                'data_inicio' => $_POST['data_inicio'] ?? null,
                'data_fim' => $_POST['data_fim'] ?? null,
                'nota' => $_POST['nota'] ?? null,
                'melhor_aluno' => isset($_POST['melhor_aluno']) ? 1 : 0,
                'status' => $_POST['status'] ?? 1
            ];

            // Upload de nova foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                $fileName = uniqid() . '_' . time() . '.jpeg';
                $uploadPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
                    $data['foto_principal'] = $fileName;
                }
            }

            $this->alunoModel->update($id, $data);

            Logger::info("Aluno atualizado: ID {$id}");
            Session::setFlash('success', 'Aluno atualizado com sucesso!');
            $this->redirect('/admin/alunos');

        } catch (\Exception $e) {
            Logger::error('Erro ao atualizar aluno: ' . $e->getMessage());
            Session::setFlash('error', 'Erro ao atualizar aluno: ' . $e->getMessage());
            $this->redirect("/admin/alunos/{$id}/edit");
        }
    }

    /**
     * Remove aluno
     */
    public function destroy(int $id): void
    {
        try {
            if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
                throw new \Exception('Token CSRF inválido');
            }

            $this->alunoModel->delete($id);

            Logger::info("Aluno removido: ID {$id}");
            Session::setFlash('success', 'Aluno removido com sucesso!');
            $this->redirect('/admin/alunos');

        } catch (\Exception $e) {
            Logger::error('Erro ao remover aluno: ' . $e->getMessage());
            Session::setFlash('error', 'Erro ao remover aluno: ' . $e->getMessage());
            $this->redirect('/admin/alunos');
        }
    }
}
