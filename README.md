проектирование бд:
<img width="1073" height="784" alt="image" src="https://github.com/user-attachments/assets/15ce5f1b-b9a1-4dd9-9f03-0cdc43962ebb" />

Создание таблиц в постгрис

Таблица departments

CREATE TABLE departments (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

Таблица positions

CREATE TABLE positions (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

Таблица employees

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