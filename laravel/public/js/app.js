(function() {
    'use strict';
    window.SalaryCalc = window.SalaryCalc || {};
    // CSRF токен для AJAX запросов
    window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    window.SalaryCalc.modal = {
        open: function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.classList.add('modal-open');

                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        window.SalaryCalc.modal.close(modalId);
                    }
                });
            }
        },

        close: function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.classList.remove('modal-open');
            }
        },

        closeAll: function() {
            document.querySelectorAll('.modal.active').forEach(modal => {
                window.SalaryCalc.modal.close(modal.id);
            });
        }
    };

    window.SalaryCalc.notify = function(message, type = 'info') {
        // Удаляем старые уведомления
        const oldNotifications = document.querySelectorAll('.notification');
        oldNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        const icon = icons[type] || icons.info;

        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${icon}"></i>
                <span>${window.SalaryCalc.escapeHtml(message)}</span>
                <button class="notification-close" onclick="this.closest('.notification').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Автоматическое удаление через 5 секунд
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    };

    window.SalaryCalc.escapeHtml = function(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    window.SalaryCalc.showLoading = function(button, text = 'Загрузка...') {
        const originalText = button.innerHTML;
        button.disabled = true;
        button.dataset.originalText = originalText;
        button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${text}`;
        return originalText;
    };

    window.SalaryCalc.hideLoading = function(button) {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText || button.innerHTML;
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Закрытие модалок по ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.SalaryCalc.modal.closeAll();
            }
        });

        if (window.initHeader) window.initHeader();
        if (window.initSidebar) window.initSidebar();

    });
})();
