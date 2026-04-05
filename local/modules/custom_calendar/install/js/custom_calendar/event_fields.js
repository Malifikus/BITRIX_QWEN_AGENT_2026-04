/**
 * Модуль "Расширение календаря" для Битрикс24
 * Добавляет обязательный выбор типа события и привязку компании
 */

(function() {
    'use strict';
    
    var CustomCalendarJS = {
        init: function() {
            this.bindToForm();
        },
        
        bindToForm: function() {
            document.addEventListener('DOMContentLoaded', function() {
                // Поиск формы редактирования события календаря
                var forms = document.querySelectorAll('form');
                for (var i = 0; i < forms.length; i++) {
                    var form = forms[i];
                    if (form.innerHTML.indexOf('calendar') !== -1 || 
                        form.className.indexOf('calendar') !== -1) {
                        CustomCalendarJS.attachValidator(form);
                        break;
                    }
                }
            });
        },
        
        attachValidator: function(form) {
            var originalSubmit = form.onsubmit;
            form.onsubmit = function(e) {
                var eventType = document.getElementById('CUSTOM_EVENT_TYPE');
                if (eventType && !eventType.value) {
                    alert('Пожалуйста, выберите тип события');
                    if (e.preventDefault) e.preventDefault();
                    return false;
                }
                
                if (eventType && eventType.value === 'meeting') {
                    var companyId = document.getElementById('CUSTOM_COMPANY_ID');
                    if (companyId && !companyId.value) {
                        alert('Для типа "Встреча" необходимо выбрать компанию');
                        if (e.preventDefault) e.preventDefault();
                        return false;
                    }
                }
                
                if (originalSubmit) {
                    return originalSubmit.call(this, e);
                }
                return true;
            };
        }
    };
    
    CustomCalendarJS.init();
})();
