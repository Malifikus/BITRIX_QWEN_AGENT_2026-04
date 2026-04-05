"""
Конфигурация приложения Корпоративный Университет
"""

# API ключ VibeCode
VIBE_API_KEY = "vibe_api_07wWokP8OmvtMpxUPsgRKMbvJdPRp6Ah_0a8b17"

# Базовый URL VibeCode API
VIBE_API_BASE_URL = "https://vibecode.bitrix24.tech/v1"

# Настройки приложения
APP_NAME = "Корпоративный Университет"
APP_VERSION = "1.0.0"

# Сущности Битрикс24 для работы с обучением
ENTITIES = {
    "users": "user",  # Сотрудники
    "tasks": "tasks",  # Задачи
    "courses": "catalog",  # Курсы (через каталог)
    "lessons": "catalog.element",  # Уроки
    "progress": "report",  # Прогресс обучения
    "departments": "department",  # Отделы
    "skills": "crm.status",  # Навыки
    "certificates": "document",  # Сертификаты
}

# Статусы обучения
LEARNING_STATUS = {
    "not_started": "Не начато",
    "in_progress": "В процессе",
    "completed": "Завершено",
    "failed": "Не сдано",
}

# Типы учебных материалов
CONTENT_TYPES = {
    "video": "Видео",
    "text": "Текст",
    "quiz": "Тест",
    "assignment": "Задание",
    "webinar": "Вебинар",
    "scorm": "SCORM пакет",
}
