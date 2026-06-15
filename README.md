# Message Notifications

Простое приложение для отправки уведомлений с использованием Docker Compose.

## Установка и запуск через Docker Compose

### Требования

- [Docker](https://www.docker.com/products/docker-desktop) 20.10+
- [Docker Compose](https://docs.docker.com/compose/install/) v2.0+

### Быстрый старт

```bash
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
```

### Доступ к сервисам

- Web приложение: http://localhost:8000 (порт 8000)
- PostgreSQL: localhost:5432 (порт 5432)
- Redis: localhost:6379 (порт 6379)
- Kafka: localhost:9092 (порт 9092)

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

По умолчанию создаются темы автоматически (`KAFKA_CFG_AUTO_CREATE_TOPICS_ENABLE=true`).

Для ручного создания тем:

```bash
docker exec notification-kafka kafka-topics.sh \
  --create \
  --topic your-topic-name \
  --bootstrap-server localhost:9092 \
  --partitions 3 \
  --replication-factor 1
```

### Настройка PHP

Контейнер включает расширения для работы с PostgreSQL (`pdo_pgsql`) и Kafka (`rdkafka`), а также Redis.
