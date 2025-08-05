$(function () {

    let currentUri = location.origin + location.pathname.replace(/\/$/, '');
    $('.navbar-menu a').each(function () {
        let href = $(this).attr('href').replace(/\/$/, '');
        if (href === currentUri) {
            $(this).addClass('active');
        }
    });

    let iziModalAlertSuccess = $('.iziModal-alert-success');
    let iziModalAlertError = $('.iziModal-alert-error');

    iziModalAlertSuccess.iziModal({
        padding: 20,
        title: 'Success',
        headerColor: '#00897b'
    });
    iziModalAlertError.iziModal({
        padding: 20,
        title: 'Error',
        headerColor: '#e53935'
    });

    /*let form = document.querySelector('.ajax-form2');
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        let res = fetch('https://localhost/register', {
            method: 'post',
            body: new FormData(form),
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
            .then((response) => response.json())
            .then((data) => {
                console.log(data);
            });
    });

    $('.ajax-form').on('submit', function (e) {
        e.preventDefault();

        let form = $(this); // Сама форма
        let btn = form.find('button'); // Кнопка отправки
        let btnText = btn.text(); // Исходный текст кнопки
        let method = form.attr('method'); // Метод отправки
        if (method) {
            method = method.toLowerCase();
        }
        let action = form.attr('action') ? form.attr('action') : location.href; // URL обработчика

        $.ajax({
            url: action,
            type: method === 'post' ? 'post' : 'get',
            data: form.serialize(),
            beforeSend: function () {
                btn.prop('disabled', true).text('Отправляю...');
            },
            success: function (res) {
                res = JSON.parse(res);
                if (res.status === 'success') {
                    iziModalAlertSuccess.iziModal('setContent', {
                        content: res.data
                    });
                    form.trigger('reset');
                    iziModalAlertSuccess.iziModal('open');
                    if (res.redirect) {
                        $(document).on('closed', iziModalAlertSuccess, function (e) {
                            location = res.redirect;
                        });
                    }
                } else {
                    iziModalAlertError.iziModal('setContent', {
                        content: res.data
                    });
                    iziModalAlertError.iziModal('open');
                }
                btn.prop('disabled', false).text(btnText);
            },
            error: function () {
                alert('Error!');
                btn.prop('disabled', false).text(btnText);
            },
        });
    });
    */

    // Изменения чекбокса удаления лого компании (Админ)
    $('input[name="remove_logo"]').change(function() {
        if ($(this).is(':checked')) {
            const companyId = $(this).closest('div[company_id]').attr('company_id');
            
            $.ajax({
                url: '/admin/company/delete-logo',
                method: 'POST',
                data: {
                    id: companyId
                },
                success: function(response) {
                    if (response.success) {
                        // Меняем изображение на дефолтное
                        $('img[src*="/photodata/companies/"]').attr('src', '/public/default.png');
                    }
                },
                error: function(xhr) {
                    const parentElement = $(this).closest('div[company_id]');
                    const errorBlock = parentElement.find('.error_block');

                    errorBlock.html('Ошибка:' + xhr.responseText);
                }
            });
        }
    });

    // Одобрить отзыв (Админ)
    $('.admin_review_form .approve-button').on('click', function(e) {
        e.preventDefault();

        const form = $(this);
        const errorBlock = form.find('.error_block');
        const successBlock = form.find('.success_block');

        errorBlock.html('').removeClass('success error');
        successBlock.html('').removeClass('success error');

        var reviewId = $('input[name="review_id"]').val();
        var companyId = $('.company_review').attr('id_company');

        $.ajax({
            url: '/admin/company/' + companyId + '/approve/' + reviewId,
            method: 'POST',
            data: {
                id: companyId,
                id_review: reviewId
            },
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    successBlock.html(response.data);
                } else {
                    errorBlock.html(response.error || 'Error occurred');
                }
            },
            error: function(xhr) {
                errorBlock.html(xhr.responseJSON?.error || 'Server error');
            }
        });
    });

    // Заблокировать отзыв (Админ)
    $('.admin_review_form .denied-button').on('click', function(e) {
        e.preventDefault();

        const form = $(this);
        const errorBlock = form.find('.error_block');
        const successBlock = form.find('.success_block');

        errorBlock.html('').removeClass('success error');
        successBlock.html('').removeClass('success error');

        var reviewId = $('input[name="review_id"]').val();
        var companyId = $('.company_review').attr('id_company');

        $.ajax({
            url: '/admin/company/' + companyId + '/denied/' + reviewId,
            method: 'POST',
            data: {
                id: companyId,
                id_review: reviewId
            },
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    successBlock.html(response.data);
                } else {
                    errorBlock.html(response.error || 'Error occurred');
                }
            },
            error: function(xhr) {
                errorBlock.html(xhr.responseJSON?.error || 'Server error');
            }
        });
    });

    // Обновление данных о компании (Админ)
    $('.company_wrapper .save-button').on('submit', function(e) {
        e.preventDefault();

        // Проверка поддержки FormData
        if (!window.FormData) {
            console.error('Браузер не поддерживает FormData.');
            alert('Ваш браузер не поддерживает отправку файлов. Пожалуйста, обновите браузер.');
            return;
        }
        
        const form = $(this);
        const formData = new FormData(this);
        const errorBlock = form.find('.error_block');
        const successBlock = form.find('.success_block');

        errorBlock.html('').removeClass('success error');
        successBlock.html('').removeClass('success error');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    successBlock.html(response.message);
                } else {
                    errorBlock.html(response.error || 'Error occurred');
                }
            },
            error: function(xhr) {
                errorBlock.html(xhr.responseJSON?.error || 'Server error');
            }
        });
    });

    // Изменение отзыва (Админ)
    $('.admin_review_form .save-button').on('click', function(e) {
        e.preventDefault();

        // Проверка поддержки FormData
        if (!window.FormData) {
            console.error('Браузер не поддерживает FormData.');
            alert('Ваш браузер не поддерживает отправку файлов. Пожалуйста, обновите браузер.');
            return;
        }
        
        const form = $('form.admin_review_form').get(0);
        const formData = new FormData(form);
        const errorBlock = $('.admin_review_form').find('.error_block');
        const successBlock = $('.admin_review_form').find('.success_block');

        errorBlock.html('').removeClass('success error');
        successBlock.html('').removeClass('success error');

        $.ajax({
            url: $('form.admin_review_form').attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    successBlock.html(response.message);
                } else {
                    errorBlock.html(response.error || 'Error occurred');
                }
            },
            error: function(xhr) {
                errorBlock.html(xhr.responseJSON?.error || 'Server error');
            }
        });
    });

    // Удаление компании и всех отзывов о ней (Админ)
    $('.delete-button').on('click', function(e) {
        const companyId = $(this).closest('div[company_id]').attr('company_id');
            
        $.ajax({
            url: '/admin/company/delete-company',
            method: 'POST',
            data: {
                id: companyId
            },
            success: function(response) {
                if (response.success) {
                    $(this).closest('div[company_id]').html('<div>Данные о компании и отзывах успешно удалены</div>');
                }
            },
            error: function(xhr) {
                var errorBlock = $(this).siblings('.error_block');
                errorBlock.text('Ошибка:' + xhr.responseText);
            }
        });
    });

    // Авторизация админа
    $('#admin_login_form').on('submit', function(e) {
        e.preventDefault(); // Отменяем стандартную отправку формы

        $('.admin_login_error').empty();
        $('.admin_login_success').empty();

        // Проверка поддержки FormData
        if (!window.FormData) {
            console.error('Браузер не поддерживает FormData.');
            alert('Ваш браузер не поддерживает отправку файлов. Пожалуйста, обновите браузер.');
            return;
        }

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'), // URL для отправки
            type: 'POST',
            data: formData,
            processData: false, // Не обрабатывать данные
            contentType: false, // Не устанавливать тип содержимого
            beforeSend: function() {
                $('.admin_login_success').html('Отправляю...');
            },
            success: function(res) {
                console.log(res);
                res = JSON.parse(res);
                console.log(res);
                if (res.status === 'success') {
                    $('.admin_login_success').empty();
                    iziModalAlertSuccess.iziModal('setContent', {
                        content: res.data
                    });
                    $(this).trigger('reset');
                    iziModalAlertSuccess.iziModal('open');
                } else {
                    $('.admin_login_success').empty();
                    $('.admin_login_error').html(res.data);
                }
                if (res.redirect) {
                    $(document).on('closed', iziModalAlertSuccess, function (e) {
                        location = res.redirect;
                    });
                }
            },
            error: function(res) {
                $('.admin_login_error').html(res.data);
            }
        });
    });

    // Оставить отзыв о компание
    $('#reviewForm').on('submit', function(e) {
        e.preventDefault(); // Отменяем стандартную отправку формы

        $('.review_success').empty();
        $('.review_error').empty();

        // Проверка поддержки FormData
        if (!window.FormData) {
            console.error('Браузер не поддерживает FormData.');
            alert('Ваш браузер не поддерживает отправку файлов. Пожалуйста, обновите браузер.');
            return;
        }

        let formData = new FormData(this);

        console.log(formData);

        $.ajax({
            url: $(this).attr('action'), // URL для отправки
            type: 'POST',
            data: formData,
            processData: false, // Не обрабатывать данные
            contentType: false, // Не устанавливать тип содержимого
            beforeSend: function() {
                $('.review_success').html('Отправляю...');
            },
            success: function(res) {
                res = JSON.parse(res);
                if (res.status === 'success') {
                    $('.review_success').html('Отзыв отправлен на модерацию');
                    $('#reviewForm')[0].reset(); // Очищаем форму
                } else {
                    $('.review_success').empty();
                    $('.review_error').html('Произошла ошибка при отправке');
                }
            },
            error: function(error) {
                $('.review_success').empty();
                $('.review_error').html('Произошла ошибка при отправке: ' + error);
            }
        });
    });
});