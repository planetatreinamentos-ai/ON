<?php
namespace PlanetaTreinamentos\Controllers;

use PlanetaTreinamentos\Core\View;
use PlanetaTreinamentos\Core\Session;
use PlanetaTreinamentos\Core\CSRF;
use PlanetaTreinamentos\Core\Database;
use PlanetaTreinamentos\Models\Professor;
use PlanetaTreinamentos\Helpers\Validator;
use PlanetaTreinamentos\Helpers\Logger;

class ProfessorController
{
    private Professor $professorModel;
    
    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->professorModel = new Professor($db);
    }
    
    public function index(): void
    {
        $professores = $this->professorModel->allWithInactive();
        View::make('professores/index', ['pageTitle' => 'Professores', 'professores' => $professores], 'admin');
    }
    
    public function create(): void
    {
        View::make('professores/create', ['pageTitle' => 'Novo Professor'], 'admin');
    }
    
    public function store(): void
    {
        CSRF::check();
        
        $validator = new Validator($_POST);
        $validator
            ->required('nome', 'Nome é obrigatório')
            ->max('nome', 255, 'Nome muito longo')
            ->required('professorid', 'ID do Professor é obrigatório')
            ->required('email', 'Email é obrigatório')
            ->email('email', 'Email inválido');
        
        if ($validator->fails()) {
            Session::error($validator->firstError());
            back();
        }
        
        $professorid = slugify($_POST['professorid']);
        
        if ($this->professorModel->professorIdExists($professorid)) {
            Session::error('Este ID de professor já está em uso.');
            back();
        }
        
        try {
            $data = [
                'professorid' => $professorid,
                'nome' => Validator::sanitizeString($_POST['nome']),
                'email' => Validator::sanitizeEmail($_POST['email']),
                'whatsapp' => Validator::sanitizeString($_POST['whatsapp'] ?? ''),
                'descricao' => Validator::sanitizeString($_POST['descricao'] ?? ''),
                'formacao' => Validator::sanitizeString($_POST['formacao'] ?? ''),
                'ordem_exibicao' => Validator::sanitizeInt($_POST['ordem_exibicao'] ?? 0),
                'status' => isset($_POST['status']) ? 1 : 0
            ];
            
            $id = $this->professorModel->create($data);
            Logger::audit('Professor criado', ['id' => $id, 'nome' => $data['nome']]);
            Session::success('Professor cadastrado com sucesso!');
            redirect('/admin/professores');
            
        } catch (\Exception $e) {
            Logger::error('Erro ao criar professor: ' . $e->getMessage());
            Session::error('Erro ao cadastrar professor.');
            back();
        }
    }
    
    public function edit(string $id): void
    {
        $professor = $this->professorModel->findById((int)$id);
        
        if (!$professor) {
            Session::error('Professor não encontrado.');
            redirect('/admin/professores');
        }
        
        View::make('professores/edit', ['pageTitle' => 'Editar Professor', 'professor' => $professor], 'admin');
    }
    
    public function update(string $id): void
    {
        CSRF::check();
        
        $professor = $this->professorModel->findById((int)$id);
        
        if (!$professor) {
            Session::error('Professor não encontrado.');
            redirect('/admin/professores');
        }
        
        $validator = new Validator($_POST);
        $validator
            ->required('nome', 'Nome é obrigatório')
            ->required('email', 'Email é obrigatório')
            ->email('email', 'Email inválido');
        
        if ($validator->fails()) {
            Session::error($validator->firstError());
            back();
        }
        
        try {
            $data = [
                'nome' => Validator::sanitizeString($_POST['nome']),
                'email' => Validator::sanitizeEmail($_POST['email']),
                'whatsapp' => Validator::sanitizeString($_POST['whatsapp'] ?? ''),
                'descricao' => Validator::sanitizeString($_POST['descricao'] ?? ''),
                'formacao' => Validator::sanitizeString($_POST['formacao'] ?? ''),
                'ordem_exibicao' => Validator::sanitizeInt($_POST['ordem_exibicao'] ?? 0),
                'status' => isset($_POST['status']) ? 1 : 0
            ];
            
            $this->professorModel->update((int)$id, $data);
            Logger::audit('Professor atualizado', ['id' => $id, 'nome' => $data['nome']]);
            Session::success('Professor atualizado com sucesso!');
            redirect('/admin/professores');
            
        } catch (\Exception $e) {
            Logger::error('Erro ao atualizar professor: ' . $e->getMessage());
            Session::error('Erro ao atualizar professor.');
            back();
        }
    }
    
    public function delete(string $id): void
    {
        CSRF::check();
        
        $professor = $this->professorModel->findById((int)$id);
        
        if (!$professor) {
            jsonResponse(['success' => false, 'message' => 'Professor não encontrado.'], 404);
        }
        
        if ($this->professorModel->hasAlunos((int)$id)) {
            jsonResponse(['success' => false, 'message' => 'Não é possível excluir este professor pois existem alunos cadastrados com ele.'], 400);
        }
        
        try {
            $this->professorModel->delete((int)$id);
            Logger::audit('Professor deletado', ['id' => $id, 'nome' => $professor['nome']]);
            jsonResponse(['success' => true, 'message' => 'Professor deletado com sucesso!']);
            
        } catch (\Exception $e) {
            Logger::error('Erro ao deletar professor: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Erro ao deletar professor.'], 500);
        }
    }
}