"""
Flask веб-приложение Корпоративный Университет
"""

from flask import Flask, render_template, jsonify, request, redirect, url_for
import sys
import os

# Добавляем корень проекта в путь
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from config.settings import APP_NAME, APP_VERSION
from src.vibecode_client import VibeCodeClient
from src.services import UniversityService, CRMService

app = Flask(__name__)
app.config['SECRET_KEY'] = 'corporate-university-secret-key-2024'

# Инициализация клиентов
vibe_client = VibeCodeClient()
university_service = UniversityService(vibe_client)
crm_service = CRMService(vibe_client)


@app.route('/')
def index():
    """Главная страница - дашборд"""
    stats = university_service.get_learning_stats()
    courses = university_service.get_courses()
    webinars = university_service.get_webinars()
    
    return render_template('index.html', 
                         app_name=APP_NAME,
                         version=APP_VERSION,
                         stats=stats,
                         courses=courses[:3],
                         webinars=webinars)


@app.route('/courses')
def courses():
    """Страница всех курсов"""
    courses = university_service.get_courses()
    return render_template('courses.html', 
                         app_name=APP_NAME,
                         courses=courses)


@app.route('/course/<int:course_id>')
def course_detail(course_id):
    """Страница конкретного курса"""
    course = university_service.get_course_by_id(course_id)
    if not course:
        return "Курс не найден", 404
    
    lessons = university_service.get_lessons_for_course(course_id)
    return render_template('course_detail.html',
                         app_name=APP_NAME,
                         course=course,
                         lessons=lessons)


@app.route('/employees')
def employees():
    """Страница сотрудников"""
    users = university_service.get_all_users()
    departments = university_service.get_departments()
    return render_template('employees.html',
                         app_name=APP_NAME,
                         users=users[:20],  # Ограничим для демо
                         departments=departments)


@app.route('/employee/<int:user_id>')
def employee_detail(user_id):
    """Страница сотрудника с прогрессом обучения"""
    user = university_service.get_user_by_id(user_id)
    if not user:
        # Демо-данные если API не доступен
        user = {
            'id': user_id,
            'name': f'Сотрудник {user_id}',
            'email': f'user{user_id}@company.com',
            'position': 'Менеджер',
            'department': 'Продажи',
        }
    
    progress = university_service.get_user_progress(user_id)
    tasks = university_service.get_user_tasks(user_id)
    
    return render_template('employee_detail.html',
                         app_name=APP_NAME,
                         user=user,
                         progress=progress,
                         tasks=tasks)


@app.route('/statistics')
def statistics():
    """Страница статистики обучения"""
    stats = university_service.get_learning_stats()
    sales_stats = crm_service.get_sales_stats()
    
    return render_template('statistics.html',
                         app_name=APP_NAME,
                         learning_stats=stats,
                         sales_stats=sales_stats)


@app.route('/webinars')
def webinars():
    """Страница вебинаров"""
    webinars_list = university_service.get_webinars()
    return render_template('webinars.html',
                         app_name=APP_NAME,
                         webinars=webinars_list)


@app.route('/api/stats')
def api_stats():
    """API endpoint для получения статистики"""
    stats = university_service.get_learning_stats()
    return jsonify({
        'total_courses': stats.total_courses,
        'active_learners': stats.active_learners,
        'completed_courses': stats.completed_courses,
        'average_progress': stats.average_progress,
        'total_learning_hours': stats.total_learning_hours,
    })


@app.route('/api/courses')
def api_courses():
    """API endpoint для получения курсов"""
    courses = university_service.get_courses()
    return jsonify([{
        'id': c.id,
        'title': c.title,
        'description': c.description,
        'duration_hours': c.duration_hours,
        'lessons_count': c.lessons_count,
        'enrolled_count': c.enrolled_count,
    } for c in courses])


@app.route('/api/enroll', methods=['POST'])
def api_enroll():
    """API endpoint для записи на курс"""
    data = request.json
    user_id = data.get('user_id')
    course_id = data.get('course_id')
    
    if not user_id or not course_id:
        return jsonify({'error': 'user_id и course_id обязательны'}), 400
    
    success = university_service.enroll_user_to_course(user_id, course_id)
    
    if success:
        return jsonify({'message': 'Успешно записаны на курс'})
    else:
        return jsonify({'error': 'Ошибка при записи на курс'}), 500


@app.route('/api/notify', methods=['POST'])
def api_notify():
    """API endpoint для отправки уведомления"""
    data = request.json
    user_id = data.get('user_id')
    message = data.get('message')
    
    if not user_id or not message:
        return jsonify({'error': 'user_id и message обязательны'}), 400
    
    success = university_service.notify_user(user_id, message)
    
    if success:
        return jsonify({'message': 'Уведомление отправлено'})
    else:
        return jsonify({'error': 'Ошибка при отправке уведомления'}), 500


@app.route('/health')
def health():
    """Endpoint для проверки здоровья приложения"""
    try:
        me = vibe_client.get_me()
        return jsonify({
            'status': 'healthy',
            'api_connected': me is not None,
            'app_name': APP_NAME,
            'version': APP_VERSION,
        })
    except Exception as e:
        return jsonify({
            'status': 'degraded',
            'error': str(e),
        }), 500


if __name__ == '__main__':
    print(f"🎓 Запуск приложения '{APP_NAME}' версии {APP_VERSION}")
    print(f"📡 Подключение к VibeCode API...")
    
    # Проверка подключения к API
    try:
        me = vibe_client.get_me()
        if me:
            print("✅ Успешное подключение к Битрикс24 через VibeCode")
        else:
            print("⚠️ Не удалось подключиться к API, используем демо-данные")
    except Exception as e:
        print(f"⚠️ Ошибка подключения к API: {e}")
        print("📦 Приложение запустится в демо-режиме")
    
    print("\n🌐 Веб-интерфейс будет доступен по адресу:")
    print("   http://localhost:5000")
    print("\n📊 Доступные страницы:")
    print("   /           - Главный дашборд")
    print("   /courses    - Каталог курсов")
    print("   /employees  - Сотрудники")
    print("   /statistics - Статистика")
    print("   /webinars   - Вебинары")
    print("\n🔌 API endpoints:")
    print("   /api/stats     - Статистика обучения")
    print("   /api/courses   - Список курсов")
    print("   /api/enroll    - Запись на курс (POST)")
    print("   /api/notify    - Отправка уведомления (POST)")
    print("   /health        - Проверка здоровья")
    print("-" * 50)
    
    app.run(debug=True, host='0.0.0.0', port=5000)
