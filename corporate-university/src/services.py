"""
Сервисы для работы с данными Корпоративного Университета
"""

from typing import List, Dict, Optional
from src.vibecode_client import VibeCodeClient
from src.models import (
    User, Course, Lesson, LearningProgress, Task,
    Department, Certificate, Webinar, Skill, LearningStats
)


class UniversityService:
    """Сервис для управления учебным процессом"""
    
    def __init__(self, client: VibeCodeClient):
        self.client = client
    
    def get_all_users(self) -> List[User]:
        """Получение всех сотрудников"""
        users_data = self.client.get_users(limit=100)
        return [User.from_b24(u) for u in users_data]
    
    def get_user_by_id(self, user_id: int) -> Optional[User]:
        """Получение сотрудника по ID"""
        user_data = self.client.get_user(user_id)
        return User.from_b24(user_data) if user_data else None
    
    def get_departments(self) -> List[Department]:
        """Получение всех отделов"""
        depts_data = self.client.get_departments()
        return [Department.from_b24(d) for d in depts_data]
    
    def get_courses(self) -> List[Course]:
        """Получение всех курсов"""
        # В реальной реализации - запрос к каталогу
        return self._get_demo_courses()
    
    def _get_demo_courses(self) -> List[Course]:
        """Демо-курсы для примера"""
        return [
            Course(
                id=1,
                title="Введение в Битрикс24",
                description="Базовый курс по работе с порталом Битрикс24",
                instructor_id=1,
                duration_hours=4,
                lessons_count=8,
                enrolled_count=45,
                status="active",
            ),
            Course(
                id=2,
                title="Эффективные продажи",
                description="Техники и методики продаж для менеджеров",
                instructor_id=2,
                duration_hours=12,
                lessons_count=15,
                enrolled_count=32,
                status="active",
            ),
            Course(
                id=3,
                title="Управление проектами",
                description="Основы проектного управления по методологии Agile",
                instructor_id=1,
                duration_hours=8,
                lessons_count=10,
                enrolled_count=28,
                status="active",
            ),
            Course(
                id=4,
                title="Кибербезопасность",
                description="Основы информационной безопасности для сотрудников",
                instructor_id=3,
                duration_hours=3,
                lessons_count=6,
                enrolled_count=67,
                status="active",
            ),
            Course(
                id=5,
                title="Клиентский сервис",
                description="Стандарты обслуживания клиентов компании",
                instructor_id=2,
                duration_hours=6,
                lessons_count=9,
                enrolled_count=41,
                status="active",
            ),
        ]
    
    def get_course_by_id(self, course_id: int) -> Optional[Course]:
        """Получение курса по ID"""
        courses = self.get_courses()
        for course in courses:
            if course.id == course_id:
                return course
        return None
    
    def get_lessons_for_course(self, course_id: int) -> List[Lesson]:
        """Получение уроков для курса"""
        return self._get_demo_lessons(course_id)
    
    def _get_demo_lessons(self, course_id: int) -> List[Lesson]:
        """Демо-уроки для примера"""
        lessons_db = {
            1: [
                Lesson(id=1, course_id=1, title="Знакомство с интерфейсом", 
                      content_type="video", duration_minutes=15, order=1),
                Lesson(id=2, course_id=1, title="Работа с задачами", 
                      content_type="video", duration_minutes=20, order=2),
                Lesson(id=3, course_id=1, title="Календарь и встречи", 
                      content_type="text", duration_minutes=10, order=3),
                Lesson(id=4, course_id=1, title="Чат и уведомления", 
                      content_type="quiz", duration_minutes=5, order=4),
            ],
            2: [
                Lesson(id=5, course_id=2, title="Техника SPIN", 
                      content_type="video", duration_minutes=25, order=1),
                Lesson(id=6, course_id=2, title="Работа с возражениями", 
                      content_type="video", duration_minutes=30, order=2),
                Lesson(id=7, course_id=2, title="Закрытие сделки", 
                      content_type="assignment", duration_minutes=45, order=3),
            ],
        }
        return lessons_db.get(course_id, [])
    
    def get_user_progress(self, user_id: int) -> List[LearningProgress]:
        """Получение прогресса обучения пользователя"""
        return self._get_demo_progress(user_id)
    
    def _get_demo_progress(self, user_id: int) -> List[LearningProgress]:
        """Демо-прогресс для примера"""
        import random
        progress = []
        for course_id in range(1, 6):
            completed = random.randint(0, 100)
            status = "completed" if completed == 100 else "in_progress" if completed > 0 else "not_started"
            progress.append(LearningProgress(
                user_id=user_id,
                course_id=course_id,
                status=status,
                progress_percent=float(completed),
                lessons_completed=int(completed / 25),
                total_lessons=10,
                score=float(random.randint(60, 100)) if completed >= 80 else 0.0,
            ))
        return progress
    
    def get_learning_stats(self) -> LearningStats:
        """Получение общей статистики обучения"""
        courses = self.get_courses()
        return LearningStats(
            total_courses=len(courses),
            active_learners=156,
            completed_courses=89,
            average_progress=67.5,
            total_learning_hours=1248,
            courses_by_department={
                "Продажи": 45,
                "Маркетинг": 32,
                "Разработка": 28,
                "Поддержка": 51,
            },
            top_courses=[
                {"id": 1, "title": "Введение в Битрикс24", "completions": 67},
                {"id": 4, "title": "Кибербезопасность", "completions": 54},
                {"id": 2, "title": "Эффективные продажи", "completions": 43},
            ],
            recent_completions=[
                {"user": "Иван Петров", "course": "Введение в Битрикс24", "date": "2024-01-15"},
                {"user": "Анна Сидорова", "course": "Клиентский сервис", "date": "2024-01-14"},
                {"user": "Михаил Козлов", "course": "Кибербезопасность", "date": "2024-01-14"},
            ],
        )
    
    def get_user_tasks(self, user_id: int) -> List[Task]:
        """Получение учебных задач пользователя"""
        tasks_data = self.client.get_tasks(limit=50, filters={"filter": {"RESPONSIBLE_ID": user_id}})
        return [Task.from_b24(t) for t in tasks_data]
    
    def create_learning_task(self, user_id: int, course_id: int, 
                            title: str, deadline: str) -> Optional[Task]:
        """Создание учебной задачи"""
        task_data = {
            "fields": {
                "TITLE": title,
                "RESPONSIBLE_ID": user_id,
                "DEADLINE": deadline,
                "DESCRIPTION": f"Пройти курс обучения. Курс ID: {course_id}",
                "TAGS": ["обучение", "курс"],
            }
        }
        result = self.client.create_task(task_data)
        if result:
            return Task.from_b24(result.get("result", {}))
        return None
    
    def notify_user(self, user_id: int, message: str) -> bool:
        """Отправка уведомления пользователю"""
        result = self.client.send_notification(user_id, message)
        return result is not None
    
    def get_webinars(self) -> List[Webinar]:
        """Получение списка вебинаров"""
        return self._get_demo_webinars()
    
    def _get_demo_webinars(self) -> List[Webinar]:
        """Демо-вебинары для примера"""
        from datetime import datetime, timedelta
        now = datetime.now()
        return [
            Webinar(
                id=1,
                title="Новые возможности Битрикс24 2024",
                description="Обзор новых функций и обновлений платформы",
                start_time=now + timedelta(days=3),
                end_time=now + timedelta(days=3, hours=1),
                instructor_id=1,
                meeting_url="https://meet.bitrix24.ru/room123",
                registered_count=45,
            ),
            Webinar(
                id=2,
                title="Лучшие практики продаж",
                description="Делимся опытом успешных сделок",
                start_time=now + timedelta(days=5),
                end_time=now + timedelta(days=5, hours=1, minutes=30),
                instructor_id=2,
                meeting_url="https://meet.bitrix24.ru/room456",
                registered_count=32,
            ),
        ]
    
    def get_skills_catalog(self) -> List[Skill]:
        """Получение каталога навыков"""
        stages_data = self.client.get_crm_stages()
        skills = [
            Skill(id="1", name="Битрикс24", category="IT"),
            Skill(id="2", name="Продажи B2B", category="Sales"),
            Skill(id="3", name="Переговоры", category="Soft Skills"),
            Skill(id="4", name="Python", category="Development"),
            Skill(id="5", name="Управление командой", category="Management"),
            Skill(id="6", name="Английский язык", category="Languages"),
        ]
        return skills
    
    def enroll_user_to_course(self, user_id: int, course_id: int) -> bool:
        """Запись пользователя на курс"""
        # В реальной реализации - создание записи в базе прогресса
        message = f"Вы записаны на курс: {course_id}"
        return self.notify_user(user_id, message)
    
    def issue_certificate(self, user_id: int, course_id: int) -> Optional[Certificate]:
        """Выдача сертификата об окончании курса"""
        import uuid
        cert_data = {
            "USER_ID": user_id,
            "COURSE_ID": course_id,
            "CERTIFICATE_NUMBER": f"CERT-{uuid.uuid4().hex[:8].upper()}",
            "ISSUED_DATE": datetime.now().isoformat(),
        }
        # В реальной реализации - сохранение в БД
        return Certificate.from_b24(cert_data)


class CRMService:
    """Сервис для работы с CRM данными"""
    
    def __init__(self, client: VibeCodeClient):
        self.client = client
    
    def get_contacts(self, limit: int = 50) -> List[Dict]:
        """Получение контактов из CRM"""
        return self.client.get_crm_contacts(limit)
    
    def get_companies(self, limit: int = 50) -> List[Dict]:
        """Получение компаний из CRM"""
        return self.client.get_crm_companies(limit)
    
    def get_deals(self, limit: int = 50) -> List[Dict]:
        """Получение сделок из CRM"""
        return self.client.get_crm_deals(limit)
    
    def get_sales_stats(self) -> Dict:
        """Получение статистики продаж"""
        deals = self.get_deals(limit=100)
        
        total_value = sum(float(d.get("OPPORTUNITY", 0)) for d in deals)
        won_deals = [d for d in deals if d.get("STATUS_ID") == "WON"]
        in_progress = [d for d in deals if d.get("STATUS_ID") in ["NEW", "PREPARATION", "NEGOTIATION"]]
        
        return {
            "total_deals": len(deals),
            "won_deals": len(won_deals),
            "in_progress": len(in_progress),
            "total_value": total_value,
            "average_deal": total_value / len(deals) if deals else 0,
            "conversion_rate": len(won_deals) / len(deals) * 100 if deals else 0,
        }
