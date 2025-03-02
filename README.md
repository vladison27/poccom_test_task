# Тестовое задание POCCOM
## Установка и запуск
### 1. Клонирование репозитория
```bash
git clone https://github.com/vladison27/poccom_test_task.git
cd poccomtest_task
```
### 2. Запуск Docker-compose
#### Для Windows
```bash
docker compose build --no-cache
docker compose up --pull always -d --wait
```
#### Для Linux/MacOS
```bash
make build
make up
```
### 3. Миграция базы данныз
```bash
docker compose exec php bin/console doctrine:migrations:migrate
```
### 4. Применение фикстур (тестовых данных)
```bash
docker compose exec php bin/console doctrine:fixtures:load
```
Сервер запустится на `http://127.0.0.1:8000`

## REST API

### **`GET`** /api/authors/popular
- **`from`** – Начало периода
- **`to`** – Конец периода
- **`genreId`** – Идентификатор жанра
- **`limit`** – Лимит вывода

### **`GET`** /api/books/largest-checks
- **`from`** – Начало периода
- **`to`** – Конец периода
- **`genreId`** – Идентификатор жанра
- **`limit`** – Лимит вывода

### **`POST`** /api/books/new
- **`name`** – Наименование книги
- **`year`** – Год выхода книги
- **`authors`** – Авторы через запятую
- **`genres`** – Жанры через запятую
