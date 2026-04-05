/**
 * JavaScript для интеграции с календарем Битрикс24
 * Добавляет поля типа события и компании в форму создания/редактирования
 */

(function() {
    'use strict';
    
    // Функция для внедрения полей в форму календаря
    function injectCustomFields() {
        // Ищем форму календаря
        var formContainer = document.querySelector('.bx-cal-event-form, .calendar-event-form, [data-bx-calendar-event-form]');
        
        if (!formContainer) {
            // Пробуем найти по другим селекторам
            formContainer = document.querySelector('form[id*="calendar"], form[class*="calendar"]');
        }
        
        if (!formContainer) {
            setTimeout(injectCustomFields, 500);
            return;
        }
        
        // Проверяем, не добавлены ли уже поля
        if (document.getElementById('custom_event_type')) {
            return;
        }
        
        // Находим место для вставки (перед кнопками сохранения)
        var submitContainer = formContainer.querySelector('.bx-cal-save-buttons, .calendar-save-buttons, .footer-buttons');
        
        if (!submitContainer) {
            submitContainer = formContainer.querySelector('button[type="submit"]');
            if (submitContainer) {
                submitContainer = submitContainer.parentNode;
            }
        }
        
        if (!submitContainer) {
            submitContainer = formContainer;
        }
        
        // Создаем HTML полей
        var fieldsHtml = `
            <div class="custom-calendar-fields" style="margin: 15px 0; padding: 15px; background: #f5f5f5; border-radius: 4px; border: 1px solid #e0e0e0;">
                <div class="calendar-field-wrapper" style="margin-bottom: 15px;">
                    <label for="custom_event_type" style="display: block; margin-bottom: 5px; font-weight: bold;">
                        Тип события <span style="color: red;">*</span>
                    </label>
                    <select id="custom_event_type" name="custom_event_type" class="calendar-event-type-select" style="width: 100%; padding: 8px; border: 1px solid #c6cdd3; border-radius: 2px;">
                        <option value="">-- Выберите тип --</option>
                        <option value="meeting">Встреча</option>
                        <option value="interview">Собеседование</option>
                        <option value="call">Созвон</option>
                        <option value="other">Другое</option>
                    </select>
                </div>
                
                <div class="calendar-company-wrapper" id="company_field_wrapper" style="display: none;">
                    <label for="custom_company_id" style="display: block; margin-bottom: 5px; font-weight: bold;">
                        Компания <span id="company_required_mark" style="color: red;">*</span>
                    </label>
                    <select id="custom_company_id" name="custom_company_id" class="calendar-company-select" style="width: 100%; padding: 8px; border: 1px solid #c6cdd3; border-radius: 2px;">
                        <option value="">-- Выберите компанию --</option>
                    </select>
                    <div id="company_loader" style="display: none; margin-top: 5px; color: #999; font-size: 12px;">Загрузка...</div>
                </div>
            </div>
        `;
        
        // Вставляем перед кнопками сохранения
        submitContainer.insertAdjacentHTML('beforebegin', fieldsHtml);
        
        // Инициализируем логику
        initCustomFieldsLogic();
        
        // Загружаем список компаний при необходимости
        loadCompaniesList();
    }
    
    // Логика работы полей
    function initCustomFieldsLogic() {
        var eventTypeSelect = document.getElementById('custom_event_type');
        var companyFieldWrapper = document.getElementById('company_field_wrapper');
        var companyRequiredMark = document.getElementById('company_required_mark');
        var companySelect = document.getElementById('custom_company_id');
        
        if (!eventTypeSelect || !companyFieldWrapper) {
            return;
        }
        
        // Функция показа/скрытия поля компании
        function toggleCompanyField() {
            var selectedType = eventTypeSelect.value;
            if (selectedType === 'meeting') {
                companyFieldWrapper.style.display = 'block';
                companyRequiredMark.style.display = 'inline';
                companySelect.setAttribute('required', 'required');
            } else {
                companyFieldWrapper.style.display = 'none';
                companyRequiredMark.style.display = 'none';
                companySelect.removeAttribute('required');
                companySelect.value = '';
            }
        }
        
        // Обработчик изменения типа события
        eventTypeSelect.addEventListener('change', toggleCompanyField);
        
        // Обработчик отправки формы
        var form = eventTypeSelect.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                var eventType = eventTypeSelect.value;
                var companyId = companySelect.value;
                
                // Проверка обязательности типа
                if (!eventType) {
                    e.preventDefault();
                    alert('Необходимо выбрать тип события (встреча/собеседование/созвон/другое)');
                    eventTypeSelect.focus();
                    return false;
                }
                
                // Проверка обязательности компании для встреч
                if (eventType === 'meeting' && !companyId) {
                    e.preventDefault();
                    alert('Для типа события "Встреча" необходимо выбрать компанию');
                    companySelect.focus();
                    return false;
                }
            });
        }
        
        // Восстанавливаем сохраненные значения если есть
        restoreSavedValues();
    }
    
    // Загрузка списка компаний через AJAX
    function loadCompaniesList(searchTerm) {
        var loader = document.getElementById('company_loader');
        var companySelect = document.getElementById('custom_company_id');
        
        if (!companySelect) {
            return;
        }
        
        if (loader) {
            loader.style.display = 'block';
        }
        
        // Используем BX.ajax или XMLHttpRequest
        var ajaxUrl = '/local/components/custom/calendar.event.edit/ajax.php';
        
        BX.ajax({
            method: 'POST',
            url: ajaxUrl,
            data: {
                action: 'get_companies',
                search: searchTerm || '',
                sessid: BX.bitrix_sessid()
            },
            onsuccess: function(response) {
                if (loader) {
                    loader.style.display = 'none';
                }
                
                try {
                    var data = JSON.parse(response);
                    if (data.success && Array.isArray(data.companies)) {
                        // Сохраняем текущее значение
                        var currentValue = companySelect.value;
                        
                        // Очищаем список кроме первого элемента
                        companySelect.innerHTML = '<option value="">-- Выберите компанию --</option>';
                        
                        // Добавляем компании
                        data.companies.forEach(function(company) {
                            var option = document.createElement('option');
                            option.value = company.id;
                            option.textContent = company.name;
                            companySelect.appendChild(option);
                        });
                        
                        // Восстанавливаем значение
                        if (currentValue) {
                            companySelect.value = currentValue;
                        }
                    }
                } catch (e) {
                    console.error('Error parsing companies response:', e);
                }
            },
            onfailure: function() {
                if (loader) {
                    loader.style.display = 'none';
                }
                console.error('Failed to load companies list');
            }
        });
    }
    
    // Восстановление сохраненных значений из описания события
    function restoreSavedValues() {
        // Ищем поле описания
        var descriptionField = document.querySelector('textarea[name*="description"], textarea[id*="description"]');
        
        if (!descriptionField || !descriptionField.value) {
            return;
        }
        
        var description = descriptionField.value;
        var match = description.match(/\[CUSTOM_DATA\](.*?)\[\/CUSTOM_DATA\]/s);
        
        if (!match) {
            return;
        }
        
        var data = match[1];
        var eventTypeMatch = data.match(/EVENT_TYPE:(\w+)/);
        var companyIdMatch = data.match(/COMPANY_ID:(\d+)/);
        
        var eventTypeSelect = document.getElementById('custom_event_type');
        var companySelect = document.getElementById('custom_company_id');
        var companyFieldWrapper = document.getElementById('company_field_wrapper');
        var companyRequiredMark = document.getElementById('company_required_mark');
        
        if (eventTypeMatch && eventTypeSelect) {
            eventTypeSelect.value = eventTypeMatch[1];
            
            // Показываем поле компании если выбрана встреча
            if (eventTypeMatch[1] === 'meeting') {
                companyFieldWrapper.style.display = 'block';
                companyRequiredMark.style.display = 'inline';
                companySelect.setAttribute('required', 'required');
            }
        }
        
        if (companyIdMatch && companySelect) {
            // Сначала загружаем список компаний если он пуст
            if (companySelect.options.length <= 1) {
                loadCompaniesList().then(function() {
                    companySelect.value = companyIdMatch[1];
                });
            } else {
                companySelect.value = companyIdMatch[1];
            }
        }
    }
    
    // Слушаем открытие формы календаря
    if (typeof BX !== 'undefined' && BX.Calendar) {
        BX.addCustomEvent('onCalendarOpenDialog', function() {
            setTimeout(injectCustomFields, 300);
        });
        
        BX.addCustomEvent('onCalendarEditEvent', function() {
            setTimeout(injectCustomFields, 300);
        });
    }
    
    // Также слушаем готовность DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(injectCustomFields, 500);
        });
    } else {
        setTimeout(injectCustomFields, 500);
    }
    
    // Экспортируем функции для внешнего использования
    window.CustomCalendarFields = {
        inject: injectCustomFields,
        loadCompanies: loadCompaniesList,
        restoreValues: restoreSavedValues
    };
})();
