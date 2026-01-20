

USE employee_system;

-- جدول الموظفين الدائمين
CREATE TABLE IF NOT EXISTS permanent_employees (
    id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    base_salary DECIMAL(10,2) NOT NULL
);

-- جدول الموظفين بعقد
CREATE TABLE IF NOT EXISTS contract_employees (
    id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    hours INT NOT NULL,
    rate DECIMAL(10,2) NOT NULL
);

-- جدول المدراء
CREATE TABLE IF NOT EXISTS managers (
    id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    base_salary DECIMAL(10,2) NOT NULL,
    bonus DECIMAL(10,2) NOT NULL
);


