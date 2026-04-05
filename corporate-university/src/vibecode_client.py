"""
VibeCode API клиент для работы с Битрикс24
"""

import requests
from typing import Optional, Dict, List, Any
from config.settings import VIBE_API_KEY

class VibeCodeClient:
    """Клиент для работы с VibeCode API"""
    
    def __init__(self, api_key: str = VIBE_API_KEY):
        self.api_key = api_key
        self.base_url = "https://vibecode.bitrix24.tech"
        self.session = requests.Session()
        self.session.headers.update({
            "X-Api-Key": self.api_key,
            "Content-Type": "application/json"
        })
        self.portal = None
        self.scopes = []
        self._init_connection()
    
    def _init_connection(self):
        """Проверка подключения и получение информации о портале"""
        try:
            response = self.session.get(f"{self.base_url}/v1/me", timeout=10)
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    self.portal = data["data"].get("portal")
                    self.scopes = data["data"].get("scopes", [])
                    print(f"[VibeCode] Подключено к порталу: {self.portal}")
                    print(f"[VibeCode] Доступные скоупы: {', '.join(self.scopes[:5])}...")
                else:
                    print(f"[VibeCode] Ошибка авторизации: {data}")
            else:
                print(f"[VibeCode] HTTP {response.status_code}: {response.text}")
        except Exception as e:
            print(f"[VibeCode] Ошибка подключения: {e}")
    
    def _request(self, method: str, endpoint: str, **kwargs) -> Optional[Dict]:
        """Выполнение HTTP запроса к API"""
        url = f"{self.base_url}/v1/{endpoint}"
        try:
            response = self.session.request(method, url, **kwargs)
            response.raise_for_status()
            result = response.json()
            return result.get("data") if result.get("success") else None
        except requests.exceptions.RequestException as e:
            print(f"API Error: {e}")
            return None
    
    def get_users(self, limit: int = 50, filters: Optional[Dict] = None) -> List[Dict]:
        """Получение списка сотрудников"""
        params = {"limit": limit}
        if filters:
            params["filter"] = filters
        result = self._request("GET", "users", params=params)
        return result if result else []
    
    def get_user(self, user_id: int) -> Optional[Dict]:
        """Получение информации о пользователе"""
        return self._request("GET", f"users/{user_id}")
    
    def get_tasks(self, limit: int = 50, filters: Optional[Dict] = None) -> List[Dict]:
        """Получение списка задач"""
        params = {"limit": limit}
        if filters:
            params["filter"] = filters
        result = self._request("GET", "tasks", params=params)
        return result if result else []
    
    def get_task(self, task_id: int) -> Optional[Dict]:
        """Получение информации о задаче"""
        return self._request("GET", f"tasks/{task_id}")
    
    def create_task(self, data: Dict) -> Optional[Dict]:
        """Создание задачи"""
        return self._request("POST", "tasks", json=data)
    
    def update_task(self, task_id: int, data: Dict) -> Optional[Dict]:
        """Обновление задачи"""
        return self._request("PATCH", f"tasks/{task_id}", json=data)
    
    def get_departments(self) -> List[Dict]:
        """Получение списка отделов"""
        result = self._request("GET", "departments")
        return result if result else []
    
    def get_crm_contacts(self, limit: int = 50) -> List[Dict]:
        """Получение контактов из CRM"""
        result = self._request("GET", "contacts", params={"limit": limit})
        return result if result else []
    
    def get_crm_companies(self, limit: int = 50) -> List[Dict]:
        """Получение компаний из CRM"""
        result = self._request("GET", "companies", params={"limit": limit})
        return result if result else []
    
    def get_crm_deals(self, limit: int = 50) -> List[Dict]:
        """Получение сделок из CRM"""
        result = self._request("GET", "deals", params={"limit": limit})
        return result if result else []
    
    def get_calendar_events(self, owner_id: int = 0, event_type: str = "user", limit: int = 50) -> List[Dict]:
        """Получение событий календаря (вебинары, встречи)"""
        params = {"limit": limit, "type": event_type, "ownerId": owner_id}
        result = self._request("GET", "calendar-events", params=params)
        return result if result else []
    
    def create_calendar_event(self, data: Dict) -> Optional[Dict]:
        """Создание события в календаре (для вебинаров)"""
        return self._request("POST", "calendar-events", json=data)
    
    def get_workgroups(self, limit: int = 50) -> List[Dict]:
        """Получение рабочих групп (можно использовать для курсов)"""
        result = self._request("GET", "workgroups", params={"limit": limit})
        return result if result else []
    
    def get_feed_posts(self, limit: int = 50) -> List[Dict]:
        """Получение постов из ленты"""
        result = self._request("GET", "feed/posts", params={"limit": limit})
        return result if result else []
    
    def create_feed_post(self, data: Dict) -> Optional[Dict]:
        """Создание поста в ленте"""
        return self._request("POST", "feed/posts", json=data)
    
    def send_notification(self, user_id: int, message: str) -> Optional[Dict]:
        """Отправка уведомления пользователю"""
        return self._request("POST", "notifications", json={
            "user_id": user_id,
            "message": message
        })
    
    def batch_request(self, commands: Dict[str, str]) -> Optional[Dict]:
        """Выполнение пакетного запроса"""
        return self._request("POST", "batch", json=commands)
    
    def get_infra_providers(self) -> List[Dict]:
        """Получение доступных провайдеров инфраструктуры"""
        result = self._request("GET", "infra/providers")
        return result if result else []
    
    def create_server(self, data: Dict) -> Optional[Dict]:
        """Создание сервера для размещения приложения"""
        return self._request("POST", "infra/servers", json=data)
