<?php
/**
 * Controller do Dashboard
 * 
 * Dashboard administrativo inicial
 * 
 * @package PlanetaTreinamentos\Controllers
 * @since 1.0
 */

namespace PlanetaTreinamentos\Controllers;

use PlanetaTreinamentos\Core\View;
use PlanetaTreinamentos\Core\Database;

class DashboardController
{
    /**
     * Database
     */
    private Database $db;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Página inicial do dashboard
     */
    public function index(): void
    {
        // Estatísticas básicas
        $stats = [
            'total_alunos' => $this->getTotalAlunos(),
            'total_cursos' => $this->getTotalCursos(),
            'total_professores' => $this->getTotalProfessores(),
            'total_certificados' => $this->getTotalCertificados(),
        ];
        
        View::make('dashboard/index', [
            'stats' => $stats,
            'pageTitle' => 'Dashboard'
        ], 'admin');
    }
    
    /**
     * Conta total de alunos
     */
    private function getTotalAlunos(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM alunos WHERE deleted_at IS NULL";
            return (int) $this->db->fetchColumn($sql);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Conta total de cursos
     */
    private function getTotalCursos(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM cursos WHERE status = 1";
            return (int) $this->db->fetchColumn($sql);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Conta total de professores
     */
    private function getTotalProfessores(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM professores WHERE status = 1";
            return (int) $this->db->fetchColumn($sql);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Conta total de certificados
     */
    private function getTotalCertificados(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM alunos WHERE certificado_emitido = 1";
            return (int) $this->db->fetchColumn($sql);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
