/**
 * Planeta Treinamentos - App.js
 * JavaScript para páginas públicas
 */

(function() {
    'use strict';
    
    // Executar quando DOM carregar
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ Planeta Treinamentos - Sistema carregado!');
        
        // Inicializar tooltips do Bootstrap
        initTooltips();
        
        // Smooth scroll para links âncora
        initSmoothScroll();
        
        // Validação de formulários
        initFormValidation();
    });
    
    /**
     * Inicializar Tooltips do Bootstrap
     */
    function initTooltips() {
        if (typeof bootstrap === 'undefined') return;
        
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    /**
     * Smooth Scroll para Links Âncora
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Ignora # sozinho
                if (href === '#' || href === '#!') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
    
    /**
     * Validação de Formulários
     */
    function initFormValidation() {
        const forms = document.querySelectorAll('.needs-validation');
        
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    }
    
})();