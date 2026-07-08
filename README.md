# Учет сотрудников (HR-система)

Веб-приложение для ведения кадрового учета сотрудников организации. Реализованы все основные CRUD-операции, фильтрация, поиск, увольнение и восстановление.

## Функциональные возможности

- Просмотр всех сотрудников в виде таблицы
- Добавление нового сотрудника с валидацией полей
- Редактирование данных работающего сотрудника
- Увольнение (без удаления из базы, с меткой "Уволен")
- Восстановление уволенного сотрудника
- Поиск по ФИО (без учета регистра)
- Фильтрация по отделу и должности
- Маски ввода для телефона и паспорта
- Визуальное выделение уволенных сотрудников

## Стек технологий

- Backend: PHP 8.x
- Frontend: HTML, CSS
- База данных: PostgreSQL
- Инструменты: Git, pgAdmin, встроенный PHP-сервер
- Библиотеки: IMask (маски ввода)

## Структура проекта

    hr-app/
    ├── add.php                # добавление сотрудника
    ├── edit.php               # редактирование сотрудника
    ├── delete.php             # увольнение
    ├── restore.php            # восстановление
    ├── index.php              # главная страница
    ├── config/
    │   └── database.php       # подключение к БД
    ├── css/
    │   └── style.css          # стили
    ├── .gitignore
    └── README.md
## Запуск проекта

1. Убедитесь, что PostgreSQL запущен и создана база данных (например, hr).
2. Выполните SQL-скрипты для создания таблиц (см. раздел "Проектирование базы данных").
3. В папке проекта запустите встроенный PHP-сервер:

php -S localhost:8000

4. Откройте в браузере:
http://localhost:8000

## Проектирование базы данных

Схема базы данных:

<img width="1073" height="784" alt="image" src="https://github.com/user-attachments/assets/15ce5f1b-b9a1-4dd9-9f03-0cdc43962ebb" />

### Создание таблиц

Таблица departments:

    CREATE TABLE departments (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL
    );

Таблица positions:

    CREATE TABLE positions (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL
    );

Таблица employees:

    CREATE TABLE employees (
        id SERIAL PRIMARY KEY,
        last_name VARCHAR(100) NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        patronymic VARCHAR(100),
        birth_date DATE NOT NULL,
        passport VARCHAR(20) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        address TEXT NOT NULL,
        department_id INTEGER REFERENCES departments(id) ON DELETE SET NULL,
        position_id INTEGER REFERENCES positions(id) ON DELETE SET NULL,
        salary DECIMAL(10, 2) NOT NULL CHECK (salary > 0),
        hire_date DATE NOT NULL,
        fired BOOLEAN DEFAULT FALSE,
        fired_date DATE,
        CHECK (fired_date IS NULL OR fired = TRUE)
    );

### Наполнение справочников

После создания таблиц добавьте начальные данные:

    INSERT INTO departments (name) VALUES 
    ('Разработка'), ('Маркетинг'), ('Продажи'), ('HR'), ('Бухгалтерия');

    INSERT INTO positions (name) VALUES 
    ('Разработчик'), ('Тестировщик'), ('Менеджер'), ('Директор'), ('Бухгалтер');

## Настройка подключения

В файле config/database.php укажите свои параметры подключения к PostgreSQL:

    $host = 'localhost';
    $port = '5432';
    $dbname = 'hr';
    $user = 'postgres';
    $password = 'ваш_пароль';

## Безопасность

- Все запросы к БД выполняются через подготовленные выражения (PDO::prepare)
- Все выводы данных экранируются через htmlspecialchars()
- Входные данные валидируются на серверной стороне

## Особенности интерфейса

- Уволенные сотрудники подсвечиваются красной полосой слева
- Редактирование недоступно для уволенных
- Кнопки с интуитивно понятными цветами (редактирование, увольнение, восстановление)
- Адаптивная верстка под мобильные экраны

Проект выполнен в рамках учебного задания по разработке веб-приложений на PHP и PostgreSQL.
