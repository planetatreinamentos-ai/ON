<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <h1><i class="fas fa-key"></i></h1>
            <h2>Recuperar Senha</h2>
            <p>Digite seu email para receber um link de recuperação</p>
        </div>
        
        <form method="POST" action="/esqueci-senha" class="login-form">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    name="email" 
                    required 
                    autofocus
                    placeholder="seu@email.com"
                >
            </div>
            
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-paper-plane"></i> Enviar Link de Recuperação
            </button>
            
            <div class="mt-3 text-center">
                <a href="/login" class="text-muted">
                    <i class="fas fa-arrow-left"></i> Voltar para o login
                </a>
            </div>
        </form>
        
        <div class="login-footer">
            <p class="text-muted text-center mb-0">
                © <?= date('Y') ?> <?= e($appName) ?>. Todos os direitos reservados.
            </p>
        </div>
    </div>
</div>

<style>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
}

.login-box {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    padding: 40px;
    width: 100%;
    max-width: 450px;
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-header h1 {
    font-size: 48px;
    color: #667eea;
    margin-bottom: 15px;
}

.login-header h2 {
    color: #333;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
}

.login-header p {
    color: #666;
    font-size: 14px;
    margin: 0;
}

.login-form .form-label {
    font-weight: 600;
    color: #555;
    margin-bottom: 8px;
}

.login-form .form-control {
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.login-form .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.login-form .btn-primary {
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    margin-top: 10px;
}

.login-form .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.login-footer {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}
</style>
