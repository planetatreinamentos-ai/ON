<?php
/**
 * Classe Validator
 * 
 * Validação e sanitização de dados
 * 
 * @package PlanetaTreinamentos\Helpers
 * @since 1.0
 */

namespace PlanetaTreinamentos\Helpers;

class Validator
{
    /**
     * Erros de validação
     */
    private array $errors = [];
    
    /**
     * Dados a validar
     */
    private array $data = [];
    
    /**
     * Construtor
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    
    /**
     * Valida campo obrigatório
     */
    public function required(string $field, string $message = null): self
    {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = $message ?? "O campo é obrigatório";
        }
        return $this;
    }
    
    /**
     * Valida email
     */
    public function email(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "Email inválido";
        }
        return $this;
    }
    
    /**
     * Valida tamanho mínimo
     */
    public function min(string $field, int $min, string $message = null): self
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field] = $message ?? "Deve ter no mínimo $min caracteres";
        }
        return $this;
    }
    
    /**
     * Valida tamanho máximo
     */
    public function max(string $field, int $max, string $message = null): self
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field] = $message ?? "Deve ter no máximo $max caracteres";
        }
        return $this;
    }
    
    /**
     * Valida se é um número
     */
    public function numeric(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = $message ?? "Deve ser um número";
        }
        return $this;
    }
    
    /**
     * Valida se é um inteiro
     */
    public function integer(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && filter_var($this->data[$field], FILTER_VALIDATE_INT) === false) {
            $this->errors[$field] = $message ?? "Deve ser um número inteiro";
        }
        return $this;
    }
    
    /**
     * Valida URL
     */
    public function url(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
            $this->errors[$field] = $message ?? "URL inválida";
        }
        return $this;
    }
    
    /**
     * Valida data no formato brasileiro
     */
    public function date(string $field, string $message = null): self
    {
        if (isset($this->data[$field])) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->data[$field]);
            if (!$date || $date->format('d/m/Y') !== $this->data[$field]) {
                $this->errors[$field] = $message ?? "Data inválida (formato: DD/MM/AAAA)";
            }
        }
        return $this;
    }
    
    /**
     * Valida se os valores são iguais
     */
    public function matches(string $field, string $matchField, string $message = null): self
    {
        if (
            isset($this->data[$field], $this->data[$matchField]) &&
            $this->data[$field] !== $this->data[$matchField]
        ) {
            $this->errors[$field] = $message ?? "Os campos não coincidem";
        }
        return $this;
    }
    
    /**
     * Valida se o valor está em uma lista
     */
    public function in(string $field, array $values, string $message = null): self
    {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $values, true)) {
            $this->errors[$field] = $message ?? "Valor inválido";
        }
        return $this;
    }
    
    /**
     * Validação customizada com callback
     */
    public function custom(string $field, callable $callback, string $message): self
    {
        if (isset($this->data[$field]) && !$callback($this->data[$field])) {
            $this->errors[$field] = $message;
        }
        return $this;
    }
    
    /**
     * Verifica se a validação passou
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }
    
    /**
     * Verifica se a validação falhou
     */
    public function fails(): bool
    {
        return !$this->passes();
    }
    
    /**
     * Retorna os erros
     */
    public function errors(): array
    {
        return $this->errors;
    }
    
    /**
     * Retorna o primeiro erro
     */
    public function firstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
    
    /**
     * Sanitiza string
     */
    public static function sanitizeString(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitiza email
     */
    public static function sanitizeEmail(?string $email): string
    {
        if ($email === null) {
            return '';
        }
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Sanitiza número
     */
    public static function sanitizeNumber($value): float
    {
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    
    /**
     * Sanitiza inteiro
     */
    public static function sanitizeInt($value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Valida CPF
     */
    public static function isValidCPF(?string $cpf): bool
    {
        if (!$cpf) {
            return false;
        }
        
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        
        // Validação dos dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Valida CNPJ
     */
    public static function isValidCNPJ(?string $cnpj): bool
    {
        if (!$cnpj) {
            return false;
        }
        
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        // Verifica se tem 14 dígitos
        if (strlen($cnpj) != 14) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }
        
        // Validação do primeiro dígito verificador
        $sum = 0;
        $multipliers = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $multipliers[$i];
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;
        
        if ($cnpj[12] != $digit1) {
            return false;
        }
        
        // Validação do segundo dígito verificador
        $sum = 0;
        $multipliers = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $multipliers[$i];
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;
        
        return $cnpj[13] == $digit2;
    }
}
