<?php
namespace PlanetaTreinamentos\Controllers;

use PlanetaTreinamentos\Core\View;
use PlanetaTreinamentos\Core\Session;
use PlanetaTreinamentos\Core\CSRF;
use PlanetaTreinamentos\Core\Database;
use PlanetaTreinamentos\Models\Curso;
use PlanetaTreinamentos\Helpers\Validator;
use PlanetaTreinamentos\Helpers\Logger;

class CursoController
{
    private Curso $cursoModel;
    
    public function __construct()
    {
        // ✅ CORREÇÃO: getConnection() retorna PDO
        $db = Database::getInstance()->getConnection();
        $this->cursoModel = new Curso($db);
    }
    
    public function index(): void
    {
        $cursos = $this->cursoModel->allWithInactive();
        View::make('cursos/index', [
            'pageTitle' => 'Cursos',
            'cursos' => $cursos
        ], 'admin');
    }
    
    public function create(): void
    {
        View::make('cursos/create', ['pageTitle' => 'Novo Curso'], 'admin');
    }
    
    public function store(): void
    {
        CSRF::check();
        
        $validator = new Validator($_POST);
        $validator
            ->required('nome', 'Nome é obrigatório')
            ->max('nome', 255, 'Nome muito longo')
            ->required('descricao', 'Descrição é obrigatória')
            ->required('frase_certificado', 'Frase do certificado é obrigatória');
        
        if ($validator->fails()) {
            Session::error($validator->firstError());
            back();
        }
        
        try {
            $data = [
                'nome' => Validator::sanitizeString($_POST['nome']),
                'descricao' => Validator::sanitizeString($_POST['descricao']),
                'frase_certificado' => Validator::sanitizeString($_POST['frase_certificado']),
                'ordem_exibicao' => Validator::sanitizeInt($_POST['ordem_exibicao'] ?? 0),
                'status' => isset($_POST['status']) ? 1 : 0
            ];
            
            $id = $this->cursoModel->create($data);
            Logger::audit('Curso criado', ['id' => $id, 'nome' => $data['nome']]);
            Session::success('Curso criado com sucesso!');
            redirect('/admin/cursos');
            
        } catch (\Exception $e) {
            Logger::error('Erro ao criar curso: ' . $e->getMessage());
            Session::error('Erro ao criar curso.');
            back();
        }
    }
    
    public function edit(string $id): void
    {
        $curso = $this->cursoModel->findById((int)$id);
        
        if (!$curso) {
            Session::error('Curso não encontrado.');
            redirect('/admin/cursos');
        }
        
        View::make('cursos/edit', [
            'pageTitle' => 'Editar Curso',
            'curso' => $curso
        ], 'admin');
    }
    
    public function update(string $id): void
    {
        CSRF::check();
        
        $curso = $this->cursoModel->findById((int)$id);
        
        if (!$curso) {
            Session::error('Curso não encontrado.');
            redirect('/admin/cursos');
        }
        
        $validator = new Validator($_POST);
        $validator
            ->required('nome', 'Nome é obrigatório')
            ->max('nome', 255, 'Nome muito longo')
            ->required('descricao', 'Descrição é obrigatória')
            ->required('frase_certificado', 'Frase do certificado é obrigatória');
        
        if ($validator->fails()) {
            Session::error($validator->firstError());
            back();
        }
        
        try {
            $data = [
                'nome' => Validator::sanitizeString($_POST['nome']),
                'descricao' => Validator::sanitizeString($_POST['descricao']),
                'frase_certificado' => Validator::sanitizeString($_POST['frase_certificado']),
                'ordem_exibicao' => Validator::sanitizeInt($_POST['ordem_exibicao'] ?? 0),
                'status' => isset($_POST['status']) ? 1 : 0
            ];
            
            $this->cursoModel->update((int)$id, $data);
            Logger::audit('Curso atualizado', ['id' => $id, 'nome' => $data['nome']]);
            Session::success('Curso atualizado com sucesso!');
            redirect('/admin/cursos');
            
        } catch (\Exception $e) {
            Logger::error('Erro ao atualizar curso: ' . $e->getMessage());
            Session::error('Erro ao atualizar curso.');
            back();
        }
    }
    
    public function delete(string $id): void
    {
        CSRF::check();
        
        $curso = $this->cursoModel->findById((int)$id);
        
        if (!$curso) {
            jsonResponse(['success' => false, 'message' => 'Curso não encontrado.'], 404);
        }
        
        if ($this->cursoModel->hasAlunos((int)$id)) {
            jsonResponse(['success' => false, 'message' => 'Não é possível excluir este curso pois existem alunos cadastrados nele.'], 400);
        }
        
        try {
            $this->cursoModel->delete((int)$id);
            Logger::audit('Curso deletado', ['id' => $id, 'nome' => $curso['nome']]);
            jsonResponse(['success' => true, 'message' => 'Curso deletado com sucesso!']);
            
        } catch (\Exception $e) {
            Logger::error('Erro ao deletar curso: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro ao deletar curso.'], 500);
        }
    }
}