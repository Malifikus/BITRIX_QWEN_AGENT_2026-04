<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="custom-calendar-fields" style="margin-top: 15px;">
    <div class="calendar-field-wrapper">
        <label for="custom_event_type" style="display: block; margin-bottom: 5px; font-weight: bold;">
            Тип события <span style="color: red;">*</span>
        </label>
        <select id="custom_event_type" name="custom_event_type" class="calendar-event-type-select" style="width: 100%; padding: 8px;">
            <option value="">-- Выберите тип --</option>
            <option value="meeting" <?= ($arResult['EVENT_TYPE'] ?? '') === 'meeting' ? 'selected' : '' ?>>Встреча</option>
            <option value="interview" <?= ($arResult['EVENT_TYPE'] ?? '') === 'interview' ? 'selected' : '' ?>>Собеседование</option>
            <option value="call" <?= ($arResult['EVENT_TYPE'] ?? '') === 'call' ? 'selected' : '' ?>>Созвон</option>
            <option value="other" <?= ($arResult['EVENT_TYPE'] ?? '') === 'other' ? 'selected' : '' ?>>Другое</option>
        </select>
    </div>
    
    <div class="calendar-company-wrapper" id="company_field_wrapper" style="margin-top: 15px; display: <?= (($arResult['EVENT_TYPE'] ?? '') === 'meeting') ? 'block' : 'none' ?>;">
        <label for="custom_company_id" style="display: block; margin-bottom: 5px; font-weight: bold;">
            Компания <span id="company_required_mark" style="color: red; display: <?= (($arResult['EVENT_TYPE'] ?? '') === 'meeting') ? 'inline' : 'none' ?>;">*</span>
        </label>
        <select id="custom_company_id" name="custom_company_id" class="calendar-company-select" style="width: 100%; padding: 8px;">
            <option value="">-- Выберите компанию --</option>
            <?php foreach ($arResult['COMPANIES'] ?? [] as $company): ?>
                <option value="<?= $company['id'] ?>" <?= ((int)($arResult['COMPANY_ID'] ?? 0) === (int)$company['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialcharsbx($company['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div id="company_loader" style="display: none; margin-top: 5px; color: #999;">Загрузка...</div>
    </div>
</div>

<script>
(function() {
    var eventTypeSelect = document.getElementById('custom_event_type');
    var companyFieldWrapper = document.getElementById('company_field_wrapper');
    var companyRequiredMark = document.getElementById('company_required_mark');
    var companySelect = document.getElementById('custom_company_id');
    var companyLoader = document.getElementById('company_loader');
    
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
    if (eventTypeSelect) {
        eventTypeSelect.addEventListener('change', toggleCompanyField);
    }
    
    // Инициализация при загрузке
    toggleCompanyField();
    
    // Перехват отправки формы календаря
    BX.addCustomEvent('onCalendarFormSubmit', function(eventData) {
        var eventType = document.getElementById('custom_event_type').value;
        var companyId = document.getElementById('custom_company_id').value;
        
        // Проверка обязательности типа
        if (!eventType) {
            alert('Необходимо выбрать тип события (встреча/собеседование/созвон/другое)');
            return false;
        }
        
        // Проверка обязательности компании для встреч
        if (eventType === 'meeting' && !companyId) {
            alert('Для типа события "Встреча" необходимо выбрать компанию');
            return false;
        }
        
        return true;
    });
})();
</script>

<style>
.custom-calendar-fields {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
}

.calendar-field-wrapper,
.calendar-company-wrapper {
    margin-bottom: 10px;
}

.calendar-event-type-select,
.calendar-company-select {
    border: 1px solid #c6cdd3;
    border-radius: 2px;
    font-size: 13px;
}

.calendar-event-type-select:focus,
.calendar-company-select:focus {
    border-color: #2fc6f6;
    outline: none;
}
</style>
