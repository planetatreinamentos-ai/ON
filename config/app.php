<?php
/**
 * Configurações Gerais da Aplicação
 * 
 * @package PlanetaTreinamentos
 * @since 1.0
 */

return [
    // Ambiente
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'https://planetatreinamentos.com.br',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo',
    
    // Segurança
    'key' => $_ENV['APP_KEY'] ?? '',
    
    // Sessão
    'session' => [
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 120), // minutos
        'secure' => filter_var($_ENV['SESSION_SECURE'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'httponly' => filter_var($_ENV['SESSION_HTTPONLY'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'samesite' => $_ENV['SESSION_SAMESITE'] ?? 'Strict',
        'name' => 'PLANETA_SESSION',
        'path' => '/',
        'domain' => '',
    ],
    
    // Rate Limiting
    'rate_limit' => [
        'enabled' => filter_var($_ENV['RATE_LIMIT_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'max_attempts' => (int) ($_ENV['RATE_LIMIT_MAX_ATTEMPTS'] ?? 5),
        'decay_minutes' => (int) ($_ENV['RATE_LIMIT_DECAY_MINUTES'] ?? 15),
    ],
    
    // Uploads
    'upload' => [
        'max_size' => (int) ($_ENV['UPLOAD_MAX_SIZE'] ?? 5242880), // 5MB em bytes
        'allowed_images' => explode(',', $_ENV['UPLOAD_ALLOWED_IMAGES'] ?? 'jpg,jpeg,png,gif,webp'),
        'allowed_docs' => explode(',', $_ENV['UPLOAD_ALLOWED_DOCS'] ?? 'pdf,doc,docx'),
        'path' => __DIR__ . '/../public/uploads/',
    ],
    
    // Logging
    'log' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'error', // debug, info, warning, error, critical
        'path' => __DIR__ . '/../storage/logs/',
        'max_files' => (int) ($_ENV['LOG_MAX_FILES'] ?? 30),
    ],
    
    // Certificados
    'certificate' => [
        'template' => $_ENV['CERTIFICATE_TEMPLATE'] ?? 'base.png',
        'quality' => (int) ($_ENV['CERTIFICATE_QUALITY'] ?? 95),
        'format' => $_ENV['CERTIFICATE_FORMAT'] ?? 'jpg',
        'path' => __DIR__ . '/../storage/certificates/',
        'template_path' => __DIR__ . '/../public/assets/img/certificates/',
    ],
    
    // Google Drive
    'gdrive' => [
        'enabled' => filter_var($_ENV['GDRIVE_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'client_id' => $_ENV['GDRIVE_CLIENT_ID'] ?? '',
        'client_secret' => $_ENV['GDRIVE_CLIENT_SECRET'] ?? '',
        'refresh_token' => $_ENV['GDRIVE_REFRESH_TOKEN'] ?? '',
        'folder_id' => $_ENV['GDRIVE_FOLDER_ID'] ?? '',
    ],
    
    // WhatsApp (Evolution API)
    'whatsapp' => [
        'enabled' => filter_var($_ENV['WHATSAPP_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'api_url' => $_ENV['WHATSAPP_API_URL'] ?? '',
        'api_key' => $_ENV['WHATSAPP_API_KEY'] ?? '',
        'instance' => $_ENV['WHATSAPP_INSTANCE'] ?? '',
        'templates' => [
            'certificate_ready' => "🎓 *Parabéns {nome}!*\n\nSeu certificado do curso *{curso}* está pronto!\n\n📄 Acesse aqui: {url}\n\n✨ Parabéns pela conquista!",
            'welcome' => "👋 *Bem-vindo(a) {nome}!*\n\nEstamos felizes em tê-lo(a) no curso *{curso}*.\n\n🚀 Sua jornada de aprendizado começa agora!\n\n_Planeta Treinamentos_",
        ],
    ],
    
    // Informações da Empresa (White-label - podem ser editadas no admin)
    'company' => [
        'name' => $_ENV['COMPANY_NAME'] ?? 'Planeta Treinamentos',
        'cnpj' => $_ENV['COMPANY_CNPJ'] ?? '',
        'phone' => $_ENV['COMPANY_PHONE'] ?? '',
        'email' => $_ENV['COMPANY_EMAIL'] ?? '',
        'address' => $_ENV['COMPANY_ADDRESS'] ?? '',
    ],
];
