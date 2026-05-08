$(document).ready(function() {
    // Инициализация вкладок
    $('.settings-tab-btn').click(function() {
        var tabId = $(this).data('tab');

        $('.settings-tab-btn').removeClass('active');
        $('.settings-tab-content').removeClass('active');

        $(this).addClass('active');
        $('#tab-' + tabId).addClass('active');
    });

    // Превью логотипа
    $('#company_logo').change(function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(event) {
                $('#logo-preview').replaceWith('<img id="logo-preview" src="' + event.target.result + '" alt="Логотип" class="logo-preview">');
            };
            reader.readAsDataURL(file);
        }
    });

    // Загрузка списка резервных копий
    function loadBackups() {
        $.ajax({
            url: '/settings/backups',
            type: 'GET',
            success: function(response) {
                if (response.success && response.backups.length > 0) {
                    var html = '';
                    $.each(response.backups, function(i, backup) {
                        html += '<tr>' +
                            '<td>' + backup.date + '</td>' +
                            '<td>' + backup.size + '</td>' +
                            '<td><span class="badge badge-info">База данных</span></td>' +
                            '<td>' +
                            '<a href="/settings/backup/download/' + backup.filename + '" class="btn btn-sm btn-outline" title="Скачать">' +
                            '<i class="fas fa-download"></i>' +
                            '</a>' +
                            '<button class="btn btn-sm btn-outline-danger ms-1" onclick="deleteBackup(\'' + backup.filename + '\')" title="Удалить">' +
                            '<i class="fas fa-trash"></i>' +
                            '</button>' +
                            '</td>' +
                            '</tr>';
                    });
                    $('#backup-list').html(html);
                } else {
                    $('#backup-list').html('<tr><td colspan="4" class="text-center text-muted">Нет резервных копий</td></tr>');
                }
            },
            error: function() {
                $('#backup-list').html('<tr><td colspan="4" class="text-center text-danger">Ошибка загрузки</td></tr>');
            }
        });
    }

    // Удаление резервной копии
    window.deleteBackup = function(filename) {
        if (confirm('Вы уверены, что хотите удалить эту резервную копию?')) {
            $.ajax({
                url: '/settings/backup/' + filename,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('Резервная копия удалена', 'success');
                        loadBackups();
                    } else {
                        showMessage(response.message || 'Ошибка при удалении', 'error');
                    }
                },
                error: function() {
                    showMessage('Ошибка при удалении', 'error');
                }
            });
        }
    };

    // Удаление логотипа
    window.deleteLogo = function() {
        if (confirm('Вы уверены, что хотите удалить логотип?')) {
            $.ajax({
                url: '/settings/logo',
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('Логотип удален', 'success');
                        location.reload();
                    } else {
                        showMessage(response.message || 'Ошибка при удалении', 'error');
                    }
                },
                error: function() {
                    showMessage('Ошибка при удалении логотипа', 'error');
                }
            });
        }
    };

    // Функция показа сообщений
    function showMessage(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
            '<i class="fas ' + icon + '"></i> ' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>';

        $('body').append(alertHtml);

        setTimeout(function() {
            $('.alert').fadeOut(500, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Загружаем список бэкапов
    loadBackups();
});
