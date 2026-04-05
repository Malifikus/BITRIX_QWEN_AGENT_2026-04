<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="custom_calendar_fields" style="margin: 15px 0; padding: 15px; background: #f9f9f9; border-radius: 4px;">
    <div class="calendar-field-row" style="margin-bottom: 15px;">
        <label for="CUSTOM_EVENT_TYPE" style="display: block; margin-bottom: 5px; font-weight: bold;">
            Тип события <span style="color: red;">*</span>
        </label>
        <select 
            id="CUSTOM_EVENT_TYPE" 
            name="CUSTOM_EVENT_TYPE" 
            required
            style="width: 100%; max-width: 300px; padding: 8px;"
            onchange="CustomCalendar.toggleCompanyField()"
        >
            <option value="">-- Выберите тип --</option>
            <?foreach($arResult['EVENT_TYPES'] as $code => $name):?>
                <option value="<?=$code?>" <?=($arResult['EXISTING_DATA']['EVENT_TYPE'] == $code) ? 'selected' : ''?>>
                    <?=$name?>
                </option>
            <?endforeach;?>
        </select>
    </div>
    
    <div class="calendar-field-row" id="company_field_row" style="display: none; margin-bottom: 15px;">
        <label for="CUSTOM_COMPANY_ID" style="display: block; margin-bottom: 5px; font-weight: bold;">
            Компания <span style="color: red;">*</span>
        </label>
        <div id="company_selector_container" style="max-width: 400px;">
            <input 
                type="hidden" 
                id="CUSTOM_COMPANY_ID" 
                name="CUSTOM_COMPANY_ID" 
                value="<?=$arResult['EXISTING_DATA']['COMPANY_ID']?>"
                data-company-id="<?=$arResult['EXISTING_DATA']['COMPANY_ID']?>"
            >
            <div 
                id="company_display" 
                style="padding: 8px; border: 1px solid #c6cdd3; background: #fff; border-radius: 3px;"
            >
                <?if($arResult['EXISTING_DATA']['COMPANY_NAME']):?>
                    <span id="company_name"><?=$arResult['EXISTING_DATA']['COMPANY_NAME']?></span>
                    <a href="#" onclick="CustomCalendar.clearCompany(); return false;" style="float: right; color: red; text-decoration: none;">&times;</a>
                <?else:?>
                    <span style="color: #999;">Компания не выбрана</span>
                <?endif;?>
            </div>
            <button 
                type="button" 
                id="btn_select_company" 
                onclick="CustomCalendar.openCompanySelector()"
                style="margin-top: 5px; padding: 6px 12px; cursor: pointer;"
            >Выбрать компанию</button>
        </div>
    </div>
</div>

<script>
var CustomCalendar = {
    toggleCompanyField: function() {
        var eventType = document.getElementById('CUSTOM_EVENT_TYPE').value;
        var companyRow = document.getElementById('company_field_row');
        
        if (eventType === 'meeting') {
            companyRow.style.display = 'block';
            var companyId = document.getElementById('CUSTOM_COMPANY_ID');
            companyId.setAttribute('required', 'required');
        } else {
            companyRow.style.display = 'none';
            var companyId = document.getElementById('CUSTOM_COMPANY_ID');
            companyId.removeAttribute('required');
        }
    },
    
    openCompanySelector: function() {
        if (typeof BX.CrmEntitySelectorDialog !== 'undefined') {
            var dialog = new BX.CrmEntitySelectorDialog({
                entityTypes: ['COMPANY'],
                callback: function(items) {
                    if (items && items.length > 0) {
                        var item = items[0];
                        document.getElementById('CUSTOM_COMPANY_ID').value = item.id;
                        document.getElementById('company_name').textContent = item.title || item.caption;
                        document.getElementById('company_display').innerHTML = 
                            '<span id="company_name">' + (item.title || item.caption) + '</span>' +
                            '<a href="#" onclick="CustomCalendar.clearCompany(); return false;" style="float: right; color: red; text-decoration: none;">&times;</a>';
                    }
                }
            });
            dialog.show();
        } else if (typeof BX.PopupWindow !== 'undefined') {
            // Fallback для старых версий
            alert('Пожалуйста, используйте современный интерфейс Битрикс24 для выбора компании.');
        } else {
            var companyId = prompt('Введите ID компании из CRM:');
            if (companyId) {
                document.getElementById('CUSTOM_COMPANY_ID').value = companyId;
                document.getElementById('company_display').innerHTML = 
                    '<span id="company_name">ID: ' + companyId + '</span>' +
                    '<a href="#" onclick="CustomCalendar.clearCompany(); return false;" style="float: right; color: red; text-decoration: none;">&times;</a>';
            }
        }
    },
    
    clearCompany: function() {
        document.getElementById('CUSTOM_COMPANY_ID').value = '';
        document.getElementById('company_display').innerHTML = 
            '<span style="color: #999;">Компания не выбрана</span>';
    },
    
    validate: function() {
        var eventType = document.getElementById('CUSTOM_EVENT_TYPE').value;
        if (!eventType) {
            alert('Пожалуйста, выберите тип события');
            return false;
        }
        
        if (eventType === 'meeting') {
            var companyId = document.getElementById('CUSTOM_COMPANY_ID').value;
            if (!companyId) {
                alert('Для типа "Встреча" необходимо выбрать компанию');
                return false;
            }
        }
        
        return true;
    }
};

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    CustomCalendar.toggleCompanyField();
    
    // Поиск формы календаря и добавление валидации
    var form = document.querySelector('form[name="calendar_event_form"], form[id*="calendar"], .calendar-event-form');
    if (!form) {
        form = document.querySelector('form');
    }
    
    if (form) {
        var originalSubmit = form.onsubmit;
        form.onsubmit = function(e) {
            if (!CustomCalendar.validate()) {
                if (e.preventDefault) e.preventDefault();
                return false;
            }
            if (originalSubmit) return originalSubmit.call(this, e);
            return true;
        };
    }
});
</script>

<style>
#custom_calendar_fields {
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.calendar-field-row label {
    color: #535c69;
    font-size: 13px;
}
#company_selector_container input[type="text"] {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
}
</style>
