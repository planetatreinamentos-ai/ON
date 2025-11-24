<?php
/**
 * Google Drive Service
 * 
 * Serviço para upload de certificados no Google Drive
 * 
 * @package PlanetaTreinamentos\Services
 * @since 1.0
 */

namespace PlanetaTreinamentos\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use PlanetaTreinamentos\Helpers\Logger;
use Exception;

class GoogleDriveService
{
    /**
     * Google Client
     */
    private ?Client $client = null;
    
    /**
     * Drive Service
     */
    private ?Drive $driveService = null;
    
    /**
     * Configurações
     */
    private array $config;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/app.php';
        
        if ($this->config['gdrive']['enabled']) {
            $this->initialize();
        }
    }
    
    /**
     * Inicializa Google Client
     */
    private function initialize(): void
    {
        try {
            if (!class_exists('Google\Client')) {
                Logger::warning('Google API Client não está instalado');
                return;
            }
            
            $this->client = new Client();
            $this->client->setApplicationName($this->config['company']['name']);
            $this->client->setScopes([Drive::DRIVE_FILE]);
            $this->client->setAuthConfig([
                'client_id' => $this->config['gdrive']['client_id'],
                'client_secret' => $this->config['gdrive']['client_secret'],
                'refresh_token' => $this->config['gdrive']['refresh_token']
            ]);
            $this->client->setAccessType('offline');
            
            $this->driveService = new Drive($this->client);
            
        } catch (Exception $e) {
            Logger::error('Erro ao inicializar Google Drive: ' . $e->getMessage());
            $this->client = null;
            $this->driveService = null;
        }
    }
    
    /**
     * Verifica se o serviço está disponível
     */
    public function isAvailable(): bool
    {
        return $this->driveService !== null;
    }
    
    /**
     * Faz upload de certificado
     * 
     * @param string $filePath Caminho do arquivo local
     * @param array $aluno Dados do aluno
     * @return string|null ID do arquivo no Drive ou URL pública
     */
    public function uploadCertificate(string $filePath, array $aluno): ?string
    {
        if (!$this->isAvailable()) {
            Logger::warning('Google Drive não está disponível');
            return null;
        }
        
        try {
            // Cria pasta do curso se não existir
            $courseFolderId = $this->getOrCreateCourseFolder($aluno['curso_nome']);
            
            // Metadata do arquivo
            $fileMetadata = new DriveFile([
                'name' => basename($filePath),
                'parents' => [$courseFolderId],
                'description' => "Certificado de {$aluno['nome']} - ID: {$aluno['alunoid']}"
            ]);
            
            // Upload
            $content = file_get_contents($filePath);
            $file = $this->driveService->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => 'image/jpeg',
                'uploadType' => 'multipart',
                'fields' => 'id,webViewLink,webContentLink'
            ]);
            
            // Torna o arquivo público (somente leitura)
            $this->makeFilePublic($file->id);
            
            Logger::info('Certificado enviado para Google Drive', [
                'aluno_id' => $aluno['id'],
                'file_id' => $file->id,
                'file_name' => basename($filePath)
            ]);
            
            // Retorna URL de visualização
            return $file->webViewLink ?? $file->webContentLink;
            
        } catch (Exception $e) {
            Logger::error('Erro ao fazer upload para Google Drive: ' . $e->getMessage(), [
                'aluno_id' => $aluno['id'] ?? null
            ]);
            return null;
        }
    }
    
    /**
     * Obtém ou cria pasta do curso
     */
    private function getOrCreateCourseFolder(string $courseName): string
    {
        $rootFolderId = $this->config['gdrive']['folder_id'];
        
        try {
            // Busca pasta existente
            $response = $this->driveService->files->listFiles([
                'q' => "name='$courseName' and mimeType='application/vnd.google-apps.folder' and '$rootFolderId' in parents and trashed=false",
                'fields' => 'files(id, name)'
            ]);
            
            if (count($response->files) > 0) {
                return $response->files[0]->id;
            }
            
            // Cria nova pasta
            $folderMetadata = new DriveFile([
                'name' => $courseName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => [$rootFolderId]
            ]);
            
            $folder = $this->driveService->files->create($folderMetadata, [
                'fields' => 'id'
            ]);
            
            return $folder->id;
            
        } catch (Exception $e) {
            Logger::error('Erro ao criar pasta no Drive: ' . $e->getMessage());
            return $rootFolderId;
        }
    }
    
    /**
     * Torna arquivo público
     */
    private function makeFilePublic(string $fileId): void
    {
        try {
            $permission = new \Google\Service\Drive\Permission([
                'type' => 'anyone',
                'role' => 'reader'
            ]);
            
            $this->driveService->permissions->create($fileId, $permission);
            
        } catch (Exception $e) {
            Logger::error('Erro ao tornar arquivo público: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove arquivo do Drive
     */
    public function deleteFile(string $fileId): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        
        try {
            $this->driveService->files->delete($fileId);
            
            Logger::info('Arquivo deletado do Google Drive', ['file_id' => $fileId]);
            
            return true;
        } catch (Exception $e) {
            Logger::error('Erro ao deletar arquivo do Drive: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lista arquivos de uma pasta
     */
    public function listFiles(string $folderId, int $maxResults = 100): array
    {
        if (!$this->isAvailable()) {
            return [];
        }
        
        try {
            $response = $this->driveService->files->listFiles([
                'q' => "'$folderId' in parents and trashed=false",
                'pageSize' => $maxResults,
                'fields' => 'files(id, name, mimeType, createdTime, size, webViewLink)',
                'orderBy' => 'createdTime desc'
            ]);
            
            $files = [];
            foreach ($response->files as $file) {
                $files[] = [
                    'id' => $file->id,
                    'name' => $file->name,
                    'mimeType' => $file->mimeType,
                    'createdTime' => $file->createdTime,
                    'size' => $file->size ?? 0,
                    'webViewLink' => $file->webViewLink
                ];
            }
            
            return $files;
            
        } catch (Exception $e) {
            Logger::error('Erro ao listar arquivos do Drive: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca arquivo por nome
     */
    public function findFileByName(string $fileName, string $folderId = null): ?array
    {
        if (!$this->isAvailable()) {
            return null;
        }
        
        try {
            $query = "name='$fileName' and trashed=false";
            
            if ($folderId) {
                $query .= " and '$folderId' in parents";
            }
            
            $response = $this->driveService->files->listFiles([
                'q' => $query,
                'pageSize' => 1,
                'fields' => 'files(id, name, webViewLink, webContentLink)'
            ]);
            
            if (count($response->files) > 0) {
                $file = $response->files[0];
                return [
                    'id' => $file->id,
                    'name' => $file->name,
                    'webViewLink' => $file->webViewLink,
                    'webContentLink' => $file->webContentLink
                ];
            }
            
            return null;
            
        } catch (Exception $e) {
            Logger::error('Erro ao buscar arquivo no Drive: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtém informações de um arquivo
     */
    public function getFileInfo(string $fileId): ?array
    {
        if (!$this->isAvailable()) {
            return null;
        }
        
        try {
            $file = $this->driveService->files->get($fileId, [
                'fields' => 'id, name, mimeType, size, createdTime, modifiedTime, webViewLink, webContentLink'
            ]);
            
            return [
                'id' => $file->id,
                'name' => $file->name,
                'mimeType' => $file->mimeType,
                'size' => $file->size ?? 0,
                'createdTime' => $file->createdTime,
                'modifiedTime' => $file->modifiedTime,
                'webViewLink' => $file->webViewLink,
                'webContentLink' => $file->webContentLink
            ];
            
        } catch (Exception $e) {
            Logger::error('Erro ao obter info do arquivo: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Move arquivo para outra pasta
     */
    public function moveFile(string $fileId, string $newParentId): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        
        try {
            // Obtém parents atuais
            $file = $this->driveService->files->get($fileId, ['fields' => 'parents']);
            $previousParents = join(',', $file->parents);
            
            // Move arquivo
            $this->driveService->files->update($fileId, new DriveFile(), [
                'addParents' => $newParentId,
                'removeParents' => $previousParents,
                'fields' => 'id, parents'
            ]);
            
            Logger::info('Arquivo movido no Drive', [
                'file_id' => $fileId,
                'new_parent' => $newParentId
            ]);
            
            return true;
            
        } catch (Exception $e) {
            Logger::error('Erro ao mover arquivo: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cria pasta
     */
    public function createFolder(string $folderName, string $parentId = null): ?string
    {
        if (!$this->isAvailable()) {
            return null;
        }
        
        try {
            $metadata = [
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder'
            ];
            
            if ($parentId) {
                $metadata['parents'] = [$parentId];
            }
            
            $folderMetadata = new DriveFile($metadata);
            $folder = $this->driveService->files->create($folderMetadata, [
                'fields' => 'id'
            ]);
            
            Logger::info('Pasta criada no Drive', [
                'folder_name' => $folderName,
                'folder_id' => $folder->id
            ]);
            
            return $folder->id;
            
        } catch (Exception $e) {
            Logger::error('Erro ao criar pasta: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtém espaço utilizado
     */
    public function getStorageInfo(): ?array
    {
        if (!$this->isAvailable()) {
            return null;
        }
        
        try {
            $about = $this->driveService->about->get(['fields' => 'storageQuota']);
            $quota = $about->storageQuota;
            
            return [
                'limit' => $quota->limit ?? 0,
                'usage' => $quota->usage ?? 0,
                'usageInDrive' => $quota->usageInDrive ?? 0,
                'available' => ($quota->limit ?? 0) - ($quota->usage ?? 0),
                'percentage' => $quota->limit > 0 ? round(($quota->usage / $quota->limit) * 100, 2) : 0
            ];
            
        } catch (Exception $e) {
            Logger::error('Erro ao obter info de storage: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Faz download de arquivo
     */
    public function downloadFile(string $fileId, string $destinationPath): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        
        try {
            $response = $this->driveService->files->get($fileId, [
                'alt' => 'media'
            ]);
            
            $content = $response->getBody()->getContents();
            
            $result = file_put_contents($destinationPath, $content);
            
            if ($result) {
                Logger::info('Arquivo baixado do Drive', [
                    'file_id' => $fileId,
                    'destination' => $destinationPath
                ]);
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            Logger::error('Erro ao fazer download: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Renomeia arquivo
     */
    public function renameFile(string $fileId, string $newName): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        
        try {
            $fileMetadata = new DriveFile(['name' => $newName]);
            
            $this->driveService->files->update($fileId, $fileMetadata);
            
            Logger::info('Arquivo renomeado no Drive', [
                'file_id' => $fileId,
                'new_name' => $newName
            ]);
            
            return true;
            
        } catch (Exception $e) {
            Logger::error('Erro ao renomear arquivo: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica se pasta existe
     */
    public function folderExists(string $folderName, string $parentId): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        
        try {
            $response = $this->driveService->files->listFiles([
                'q' => "name='$folderName' and mimeType='application/vnd.google-apps.folder' and '$parentId' in parents and trashed=false",
                'fields' => 'files(id)'
            ]);
            
            return count($response->files) > 0;
            
        } catch (Exception $e) {
            return false;
        }
    }
}
