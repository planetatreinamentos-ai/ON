/* =====================================================
   PLANETA TREINAMENTOS - JS PÚBLICO
   JavaScript para homepage e páginas públicas
   ===================================================== */

// Esperar DOM carregar
document.addEventListener('DOMContentLoaded', function() {
    
    // ===== MENU MOBILE =====
    initMobileMenu();
    
    // ===== ANIMAÇÕES DE SCROLL =====
    initScrollAnimations();
    
    // ===== FORMULÁRIO DE INTERESSE =====
    initInteresseForm();
    
    // ===== MÁSCARAS DE INPUT =====
    initInputMasks();
    
    // ===== LAZY LOADING DE IMAGENS =====
    initLazyLoading();
    
    // ===== SMOOTH SCROLL =====
    initSmoothScroll();
});

/* ===== MENU MOBILE ===== */
function initMobileMenu() {
    const toggle = document.querySelector('.navbar-toggle');
    const menu = document.querySelector('.navbar-menu');
    
    if (toggle && menu) {
        toggle.addEventListener('click', function() {
            menu.classList.toggle('active');
            this.innerHTML = menu.classList.contains('active') ? '✕' : '☰';
        });
        
        // Fechar menu ao clicar em link
        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                menu.classList.remove('active');
                toggle.innerHTML = '☰';
            });
        });
        
        // Fechar menu ao clicar fora
        document.addEventListener('click', function(e) {
            if (!toggle.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('active');
                toggle.innerHTML = '☰';
            }
        });
    }
}

/* ===== ANIMAÇÕES DE SCROLL ===== */
function initScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, {
        threshold: 0.1
    });
    
    // Observar cards e seções
    document.querySelectorAll('.card, .section').forEach(el => {
        observer.observe(el);
    });
}

/* ===== FORMULÁRIO DE INTERESSE ===== */
function initInteresseForm() {
    const form = document.getElementById('form-interesse');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar campos
            if (!validateForm(form)) {
                return;
            }
            
            // Mostrar loading
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading"></span> Enviando...';
            submitBtn.disabled = true;
            
            // Enviar via AJAX
            const formData = new FormData(form);
            
            fetch('/interessados', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', 'Obrigado! Entraremos em contato em breve.');
                    form.reset();
                } else {
                    showMessage('error', data.message || 'Erro ao enviar. Tente novamente.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showMessage('error', 'Erro ao enviar. Tente novamente.');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
}

/* ===== VALIDAÇÃO DE FORMULÁRIO ===== */
function validateForm(form) {
    let isValid = true;
    
    // Limpar erros anteriores
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
    
    // Validar campos obrigatórios
    form.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'Este campo é obrigatório');
            isValid = false;
        }
    });
    
    // Validar email
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        if (field.value && !isValidEmail(field.value)) {
            showFieldError(field, 'Email inválido');
            isValid = false;
        }
    });
    
    // Validar WhatsApp
    const whatsappFields = form.querySelectorAll('input[name="whatsapp"]');
    whatsappFields.forEach(field => {
        if (field.value && field.value.replace(/\D/g, '').length < 10) {
            showFieldError(field, 'WhatsApp inválido');
            isValid = false;
        }
    });
    
    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('error');
    const errorEl = document.createElement('div');
    errorEl.className = 'error-message';
    errorEl.style.color = '#f56565';
    errorEl.style.fontSize = '0.875rem';
    errorEl.style.marginTop = '0.25rem';
    errorEl.textContent = message;
    field.parentElement.appendChild(errorEl);
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/* ===== MÁSCARAS DE INPUT ===== */
function initInputMasks() {
    // Máscara de WhatsApp
    document.querySelectorAll('input[name="whatsapp"]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
                value = value.replace(/(\d)(\d{4})$/, '$1-$2');
            }
            
            e.target.value = value;
        });
    });
    
    // Máscara de CPF
    document.querySelectorAll('input[name="cpf"]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });
    });
    
    // Máscara de CEP
    document.querySelectorAll('input[name="cep"]').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
    });
}

/* ===== LAZY LOADING ===== */
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

/* ===== SMOOTH SCROLL ===== */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href === '#' || href === '#!') return;
            
            e.preventDefault();
            
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/* ===== MENSAGENS ===== */
function showMessage(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#48bb78' : '#f56565'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 9999;
        animation: slideInRight 0.3s ease;
    `;
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

/* ===== CONTADOR DE ESTATÍSTICAS ===== */
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            element.textContent = target.toLocaleString('pt-BR');
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(start).toLocaleString('pt-BR');
        }
    }, 16);
}

// Animar contadores quando visíveis
const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
            const target = parseInt(entry.target.dataset.count);
            animateCounter(entry.target, target);
            entry.target.classList.add('counted');
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('.stat-number[data-count]').forEach(el => {
    counterObserver.observe(el);
});

/* ===== ADICIONAR ANIMAÇÕES CSS ===== */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .error {
        border-color: #f56565 !important;
    }
`;
document.head.appendChild(style);