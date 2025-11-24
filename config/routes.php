<?php
/**
 * Definição de Rotas - VERSÃO CORRIGIDA
 * 
 * ✅ Todas as rotas públicas
 * ✅ Todas as rotas admin protegidas
 * ✅ Middleware correto (Auth com A maiúsculo)
 * 
 * @package PlanetaTreinamentos
 * @since 1.0
 */

use PlanetaTreinamentos\Core\Router;

$router = new Router();

// =====================================================
// ROTAS PÚBLICAS (sem autenticação)
// =====================================================

// Homepage e páginas públicas
$router->get('/', 'PublicController@home');
$router->get('/home', 'PublicController@home');
$router->get('/sobre', 'PublicController@sobre');
$router->get('/contato', 'PublicController@contato');
$router->post('/contato', 'PublicController@enviarContato');

// Formulário de interesse
$router->post('/interessados/criar', 'PublicController@criarInteressado');

// Verificação de certificado
$router->get('/verificar/{alunoid}', 'PublicController@verificarCertificado');
$router->get('/aluno.php', 'PublicController@alunoLegacy'); // Compatibilidade com QR codes antigos

// Autenticação
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/esqueci-senha', 'AuthController@showForgotPassword');
$router->post('/esqueci-senha', 'AuthController@forgotPassword');
$router->get('/redefinir-senha/{token}', 'AuthController@showResetPassword');
$router->post('/redefinir-senha', 'AuthController@resetPassword');

// =====================================================
// ROTAS ADMINISTRATIVAS (requerem autenticação)
// =====================================================

// Dashboard
$router->get('/admin', 'DashboardController@index', ['Auth']);
$router->get('/admin/dashboard', 'DashboardController@index', ['Auth']);

// Configurações White-label
$router->get('/admin/configuracoes', 'ConfigController@index', ['Auth']);
$router->post('/admin/configuracoes', 'ConfigController@update', ['Auth']);
$router->post('/admin/configuracoes/logo', 'ConfigController@uploadLogo', ['Auth']);

// Cursos
$router->get('/admin/cursos', 'CursoController@index', ['Auth']);
$router->get('/admin/cursos/criar', 'CursoController@create', ['Auth']);
$router->post('/admin/cursos', 'CursoController@store', ['Auth']);
$router->get('/admin/cursos/{id}/editar', 'CursoController@edit', ['Auth']);
$router->post('/admin/cursos/{id}', 'CursoController@update', ['Auth']);
$router->delete('/admin/cursos/{id}', 'CursoController@delete', ['Auth']);

// Professores
$router->get('/admin/professores', 'ProfessorController@index', ['Auth']);
$router->get('/admin/professores/criar', 'ProfessorController@create', ['Auth']);
$router->post('/admin/professores', 'ProfessorController@store', ['Auth']);
$router->get('/admin/professores/{id}/editar', 'ProfessorController@edit', ['Auth']);
$router->post('/admin/professores/{id}', 'ProfessorController@update', ['Auth']);
$router->delete('/admin/professores/{id}', 'ProfessorController@delete', ['Auth']);

// Alunos
$router->get('/admin/alunos', 'AlunoController@index', ['Auth']);
$router->get('/admin/alunos/criar', 'AlunoController@create', ['Auth']);
$router->post('/admin/alunos', 'AlunoController@store', ['Auth']);
$router->get('/admin/alunos/{id}/editar', 'AlunoController@edit', ['Auth']);
$router->post('/admin/alunos/{id}', 'AlunoController@update', ['Auth']);
$router->delete('/admin/alunos/{id}', 'AlunoController@delete', ['Auth']);

// Cargas Horárias
$router->get('/admin/cargas-horarias', 'CargaHorariaController@index', ['Auth']);
$router->post('/admin/cargas-horarias', 'CargaHorariaController@store', ['Auth']);
$router->post('/admin/cargas-horarias/{id}', 'CargaHorariaController@update', ['Auth']);
$router->delete('/admin/cargas-horarias/{id}', 'CargaHorariaController@delete', ['Auth']);

// Certificados
$router->get('/admin/certificados', 'CertificadoController@index', ['Auth']);
$router->get('/admin/certificados/gerar/{id}', 'CertificadoController@gerar', ['Auth']);
$router->post('/admin/certificados/gerar-lote', 'CertificadoController@gerarLote', ['Auth']);
$router->get('/admin/certificados/download/{id}', 'CertificadoController@download', ['Auth']);

// =====================================================
// RETORNAR ROUTER
// =====================================================

return $router;