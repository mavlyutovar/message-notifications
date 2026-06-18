# Message Notifications

Простое приложение для отправки уведомлений с использованием Docker Compose.

## Установка и запуск через Docker Compose

### Требования

- [Docker](https://www.docker.com/products/docker-desktop) 20.10+
- [Docker Compose](https://docs.docker.com/compose/install/) v2.0+

### Быстрый старт

```bash
# Для запуска нужно перейти в папку docker
cd message-notifications/docker

# Запускаем все сервисы в фоновом режиме
docker-compose up -d --build

# Проверка статуса контейнеров
docker-compose ps
```

### Запуск Laravel

```bash
# Открыть контейнер
docker exec -it notification-app bash 

# Копируем файл переменных окружения 
cp .env.example .env 

# Установить зависимости
composer install

# Сгенерировать ключ
php artisan key:generate

# Выполнить миграцию
php artisan migrate

# Генерация swagger документации
php artisan l5-swagger:generate

# Обновить конфиги/роуты и весь проект
php artisan optimize
```

### Доступ к сервисам

- Web приложение: http://localhost:8000
- Swagger: http://localhost:8000/api/documentation
- PostgreSQL: localhost:5432 
- Redis: localhost:6379 
- Kafka: localhost:9092 

### Управление контейнерами

```bash
# Остановить все сервисы
docker-compose down

# Остановить и удалить данные баз данных (остаются только nginx)
docker-compose down -v

# Запустить в интерактивном режиме
docker-compose up

# Логи всех сервисов
docker-compose logs -f

# Перезапуск конкретного сервиса
docker-compose restart app
```

### Настройка Kafka

По умолчанию темы создаются автоматически.

