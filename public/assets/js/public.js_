/**
 * JavaScript Customizado para Páginas Públicas
 * Planeta Treinamentos
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== MÁSCARAS DE INPUT =====
    const telefoneInputs = document.querySelectorAll('[data-mask="(00) 00000-0000"]');
    telefoneInputs.forEach(input => {
        if (typeof IMask !== 'undefined') {
            IMask(input, {
                mask: '(00) 00000-0000'
            });
        }
    });

    // ===== VALIDAÇÃO DE FORMULÁRIOS =====
    const forms = document.querySelectorAll('form[data-validate="true"], #formContato, #formInteresse');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // ===== SMOOTH SCROLL PARA ÂNCORAS =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Fechar navbar mobile se estiver aberto
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                        bsCollapse.hide();
                    }
                }
            }
        });
    });

    // ===== NAVBAR SCROLL EFFECT =====
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                navbar.classList.add('shadow-lg');
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            } else {
                navbar.classList.remove('shadow-lg');
                navbar.style.background = 'white';
            }
        });
    }

    // ===== LAZY LOADING DE IMAGENS =====
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // ===== CONTADOR ANIMADO =====
    function animateCounter(element) {
        const target = parseInt(element.textContent.replace(/\D/g, ''));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target + (element.textContent.includes('+') ? '+' : '');
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current) + (element.textContent.includes('+') ? '+' : '');
            }
        }, 16);
    }

    // Observar contadores quando ficarem visíveis
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.animated) {
                animateCounter(entry.target);
                entry.target.dataset.animated = 'true';
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.display-4, .display-3').forEach(counter => {
        if (/^\d+\+?$/.test(counter.textContent.trim())) {
            counterObserver.observe(counter);
        }
    });

    // ===== AUTO-DISMISS DE ALERTS =====
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // ===== TOOLTIP BOOTSTRAP =====
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => 
        new bootstrap.Tooltip(tooltipTriggerEl)
    );

    // ===== POPOVER BOOTSTRAP =====
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => 
        new bootstrap.Popover(popoverTriggerEl)
    );

    // ===== LOADING BUTTON STATE =====
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            if (form && form.checkValidity()) {
                const originalText = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
                
                // Restaurar após 10 segundos (fallback)
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = originalText;
                }, 10000);
            }
        });
    });

    // ===== COPY TO CLIPBOARD =====
    document.querySelectorAll('[data-copy]').forEach(button => {
        button.addEventListener('click', function() {
            const text = this.dataset.copy;
            navigator.clipboard.writeText(text).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check me-2"></i>Copiado!';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            });
        });
    });

    // ===== DETECTAR SCROLL PARA ANIMAÇÕES =====
    const fadeElements = document.querySelectorAll('.fade-on-scroll');
    const fadeObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                fadeObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    fadeElements.forEach(element => {
        fadeObserver.observe(element);
    });

    // ===== FORMULÁRIO DE INTERESSE - AJAX =====
    const formInteresse = document.getElementById('formInteresse');
    if (formInteresse) {
        formInteresse.addEventListener('submit', function(e) {
            // Deixar submit normal por enquanto
            // Pode ser convertido para AJAX depois se necessário
        });
    }

    // ===== FORMULÁRIO DE CONTATO - AJAX =====
    const formContato = document.getElementById('formContato');
    if (formContato) {
        formContato.addEventListener('submit', function(e) {
            // Deixar submit normal por enquanto
            // Pode ser convertido para AJAX depois se necessário
        });
    }

    // ===== DETECTAR MOBILE =====
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (isMobile) {
        document.body.classList.add('is-mobile');
    }

    // ===== LINK DO WHATSAPP COM ANALYTICS =====
    document.querySelectorAll('a[href*="wa.me"]').forEach(link => {
        link.addEventListener('click', function() {
            // Enviar evento para Google Analytics se disponível
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': 'WhatsApp',
                    'event_label': 'Click WhatsApp Button'
                });
            }
        });
    });

    // ===== ANIMAÇÃO DE CONFETES (para melhor aluno) =====
    if (document.querySelector('.melhor-aluno-badge')) {
        if (typeof confetti !== 'undefined') {
            setTimeout(() => {
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            }, 500);
        }
    }

    // ===== PREVENIR DUPLO SUBMIT =====
    document.querySelectorAll('form').forEach(form => {
        let isSubmitting = false;
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
            
            // Resetar após 5 segundos (fallback)
            setTimeout(() => {
                isSubmitting = false;
            }, 5000);
        });
    });

    // ===== LOG DE ERROS NO CONSOLE (apenas em dev) =====
    window.addEventListener('error', function(e) {
        // Silenciar em produção
        if (window.location.hostname !== 'localhost') {
            e.preventDefault();
        }
    });

    // ===== INICIALIZAÇÃO COMPLETA =====
    console.log('✅ JavaScript público inicializado com sucesso!');
});

// ===== FUNÇÕES AUXILIARES GLOBAIS =====

/**
 * Mostrar notificação toast
 */
function showToast(message, type = 'success') {
    const toastHTML = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

/**
 * Formatar número para moeda brasileira
 */
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

/**
 * Formatar data brasileira
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}