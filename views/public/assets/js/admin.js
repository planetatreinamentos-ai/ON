/**
 * Planeta Treinamentos - Admin.js
 * JavaScript para área administrativa
 */

(function() {
    'use strict';
    
    // Executar quando DOM carregar
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ Admin Panel - Sistema carregado!');
        
        // Inicializar componentes
        initSidebar();
        initTooltips();
        initConfirmDelete();
        initFormValidation();
        initDataTables();
    });
    
    /**
     * Inicializar Sidebar Toggle
     */
    function initSidebar() {
        const sidebarCollapse = document.getElementById('sidebarCollapse');
        const sidebar = document.getElementById('sidebar');
        
        if (sidebarCollapse && sidebar) {
            sidebarCollapse.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        }
    }
    
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
     * Confirmação de Delete
     */
    function initConfirmDelete() {
        const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const message = this.getAttribute('data-confirm-delete') || 
                                'Tem certeza que deseja excluir este registro?';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Confirmar Exclusão',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sim, excluir!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submeter formulário ou fazer requisição
                            const form = this.closest('form');
                            if (form) {
                                form.submit();
                            } else {
                                window.location.href = this.href;
                            }
                        }
                    });
                } else {
                    // Fallback para confirm nativo
                    if (confirm(message)) {
                        const form = this.closest('form');
                        if (form) {
                            form.submit();
                        } else {
                            window.location.href = this.href;
                        }
                    }
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
    
    /**
     * Inicializar DataTables
     */
    function initDataTables() {
        // Verifica se jQuery e DataTables estão disponíveis
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined') {
            return;
        }
        
        // Inicializa todas as tabelas com classe .datatable
        jQuery('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            pageLength: 25,
            responsive: true,
            order: [[0, 'desc']] // Ordem decrescente na primeira coluna
        });
    }
    
    /**
     * Preview de Imagem em Upload
     */
    window.previewImage = function(input) {
        const file = input.files[0];
        const preview = document.getElementById(input.getAttribute('data-preview'));
        
        if (file && preview) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            
            reader.readAsDataURL(file);
        }
    };
    
    /**
     * Copiar para Clipboard
     */
    window.copyToClipboard = function(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copiado!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    alert('Copiado para a área de transferência!');
                }
            });
        }
    };
    
})();