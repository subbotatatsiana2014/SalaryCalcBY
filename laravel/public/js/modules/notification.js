(function() {
    'use strict';

    if (window.SalaryCalc) {
        window.SalaryCalc.notifications = {
            count: 0,
            items: [],

            add: function(title, message, type = 'info') {
                this.items.unshift({
                    id: Date.now(),
                    title: title,
                    message: message,
                    type: type,
                    time: new Date(),
                    read: false
                });
                this.count++;
                this.updateBadge();
                this.renderList();
            },

            updateBadge: function() {
                const badge = document.querySelector('.notification-count');
                if (badge) {
                    badge.textContent = this.count;
                    badge.style.display = this.count > 0 ? 'flex' : 'none';
                }
            },

            renderList: function() {
                const container = document.querySelector('.notification-list');
                if (!container) return;

                if (this.items.length === 0) {
                    container.innerHTML = '<div class="notification-empty">Нет уведомлений</div>';
                    return;
                }

                container.innerHTML = this.items.slice(0, 10).map(item => `
                    <div class="notification-item ${!item.read ? 'unread' : ''}" data-id="${item.id}">
                        <div class="notification-icon">
                            <i class="fas ${item.type === 'success' ? 'fa-check-circle text-success' :
                    item.type === 'warning' ? 'fa-exclamation-triangle text-warning' :
                        'fa-info-circle text-primary'}"></i>
                        </div>
                        <div class="notification-text">
                            <p>${window.SalaryCalc.escapeHtml(item.title)}</p>
                            <small>${item.message}</small>
                        </div>
                    </div>
                `).join('');
            }
        };
    }
})();
