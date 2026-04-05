"""
Модели данных для Корпоративного Университета
"""

from dataclasses import dataclass, field
from typing import Optional, List, Dict, Any
from datetime import datetime


@dataclass
class User:
    """Сотрудник компании"""
    id: int
    name: str
    email: str
    position: str = ""
    department: str = ""
    photo: str = ""
    skills: List[str] = field(default_factory=list)
    hire_date: Optional[datetime] = None
    
    @classmethod
    def from_b24(cls, data: Dict) -> "User":
        return cls(
            id=data.get("ID", 0),
            name=data.get("NAME", "") + " " + data.get("LAST_NAME", ""),
            email=data.get("EMAIL", ""),
            position=data.get("POSITION", ""),
            department=data.get("UF_DEPARTMENT", ""),
            photo=data.get("PERSONAL_PHOTO", ""),
            skills=data.get("UF_SKILLS", []),
        )


@dataclass
class Course:
    """Учебный курс"""
    id: int
    title: str
    description: str
    instructor_id: int
    duration_hours: int = 0
    lessons_count: int = 0
    enrolled_count: int = 0
    status: str = "active"
    created_at: Optional[datetime] = None
    thumbnail: str = ""
    
    @classmethod
    def from_b24(cls, data: Dict) -> "Course":
        return cls(
            id=data.get("ID", 0),
            title=data.get("NAME", ""),
            description=data.get("DETAIL_TEXT", ""),
            instructor_id=data.get("CREATED_BY", 0),
            duration_hours=data.get("PROPERTY_DURATION", 0),
            lessons_count=data.get("PROPERTY_LESSONS_COUNT", 0),
            thumbnail=data.get("PREVIEW_PICTURE", ""),
        )


@dataclass
class Lesson:
    """Урок в курсе"""
    id: int
    course_id: int
    title: str
    content_type: str  # video, text, quiz, assignment
    content_url: str = ""
    duration_minutes: int = 0
    order: int = 0
    is_completed: bool = False
    
    @classmethod
    def from_b24(cls, data: Dict) -> "Lesson":
        return cls(
            id=data.get("ID", 0),
            course_id=data.get("PROPERTY_COURSE_ID", 0),
            title=data.get("NAME", ""),
            content_type=data.get("PROPERTY_CONTENT_TYPE", "text"),
            content_url=data.get("DETAIL_TEXT", ""),
            duration_minutes=data.get("PROPERTY_DURATION", 0),
            order=data.get("SORT", 0),
        )


@dataclass
class LearningProgress:
    """Прогресс обучения сотрудника"""
    user_id: int
    course_id: int
    status: str  # not_started, in_progress, completed, failed
    progress_percent: float = 0.0
    lessons_completed: int = 0
    total_lessons: int = 0
    started_at: Optional[datetime] = None
    completed_at: Optional[datetime] = None
    score: float = 0.0
    
    @classmethod
    def from_b24(cls, data: Dict) -> "LearningProgress":
        return cls(
            user_id=data.get("USER_ID", 0),
            course_id=data.get("COURSE_ID", 0),
            status=data.get("STATUS", "not_started"),
            progress_percent=data.get("PROGRESS_PERCENT", 0.0),
            lessons_completed=data.get("LESSONS_COMPLETED", 0),
            total_lessons=data.get("TOTAL_LESSONS", 0),
            score=data.get("SCORE", 0.0),
        )


@dataclass
class Task:
    """Учебная задача"""
    id: int
    title: str
    description: str
    responsible_id: int
    deadline: Optional[datetime] = None
    status: str = "new"  # new, in_progress, completed, overdue
    priority: int = 1
    related_course_id: int = 0
    
    @classmethod
    def from_b24(cls, data: Dict) -> "Task":
        return cls(
            id=data.get("ID", 0),
            title=data.get("TITLE", ""),
            description=data.get("DESCRIPTION", ""),
            responsible_id=data.get("RESPONSIBLE_ID", 0),
            status=data.get("STATUS", "new"),
            priority=data.get("PRIORITY", 1),
        )


@dataclass
class Certificate:
    """Сертификат об окончании курса"""
    id: int
    user_id: int
    course_id: int
    certificate_number: str
    issued_date: datetime
    expiry_date: Optional[datetime] = None
    file_url: str = ""
    
    @classmethod
    def from_b24(cls, data: Dict) -> "Certificate":
        return cls(
            id=data.get("ID", 0),
            user_id=data.get("USER_ID", 0),
            course_id=data.get("COURSE_ID", 0),
            certificate_number=data.get("CERTIFICATE_NUMBER", ""),
            issued_date=data.get("ISSUED_DATE"),
            file_url=data.get("FILE_URL", ""),
        )


@dataclass
class Department:
    """Отдел компании"""
    id: int
    name: str
    parent_id: int = 0
    head_id: int = 0
    employee_count: int = 0
    
    @classmethod
    def from_b24(cls, data: Dict) -> "Department":
        return cls(
            id=data.get("ID", 0),
            name=data.get("NAME", ""),
            parent_id=data.get("PARENT_ID", 0),
            head_id=data.get("HEAD_ID", 0),
        )


@dataclass
class Webinar:
    """Вебинар/онлайн-встреча"""
    id: int
    title: str
    description: str
    start_time: datetime
    end_time: datetime
    instructor_id: int
    meeting_url: str = ""
    registered_count: int = 0
    attended_count: int = 0
    
    @classmethod
    def from_b24(cls, data: Dict) -> "Webinar":
        return cls(
            id=data.get("ID", 0),
            title=data.get("NAME", ""),
            description=data.get("DESCRIPTION", ""),
            start_time=data.get("DT_START"),
            end_time=data.get("DT_END"),
            instructor_id=data.get("OWNER_ID", 0),
            meeting_url=data.get("MEETING_URL", ""),
        )


@dataclass
class Skill:
    """Навык/компетенция"""
    id: str
    name: str
    category: str = ""
    level_required: int = 0
    
    @classmethod
    def from_b24(cls, data: Dict) -> "Skill":
        return cls(
            id=data.get("ID", ""),
            name=data.get("NAME", ""),
            category=data.get("CATEGORY", ""),
        )


@dataclass
class LearningStats:
    """Статистика обучения"""
    total_courses: int = 0
    active_learners: int = 0
    completed_courses: int = 0
    average_progress: float = 0.0
    total_learning_hours: float = 0.0
    courses_by_department: Dict[str, int] = field(default_factory=dict)
    top_courses: List[Dict] = field(default_factory=list)
    recent_completions: List[Dict] = field(default_factory=list)
