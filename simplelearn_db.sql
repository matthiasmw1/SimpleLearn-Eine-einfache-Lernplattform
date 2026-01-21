-- =============================================
-- SimpleLearn Database - Complete Setup
-- =============================================
-- Erstellt: 2026-01-20
-- Für: FH Informatik Web-Projekt
-- =============================================

-- Datenbank erstellen
DROP DATABASE IF EXISTS simplelearn;
CREATE DATABASE simplelearn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE simplelearn;

-- =============================================
-- 1. USERS Tabelle (Benutzer)
-- =============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. COURSES Tabelle (Kurse)
-- =============================================
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    content TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_created_by (created_by),
    INDEX idx_created_at (created_at),
    FULLTEXT INDEX ft_search (title, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. TASKS Tabelle (To-Do / Aufgaben)
-- =============================================
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    due_date DATE,
    status ENUM('pending', 'submitted', 'graded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_course_id (course_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. FILES Tabelle (Dateien/Upload)
-- =============================================
CREATE TABLE files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT,
    file_type VARCHAR(50),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_course_id (course_id),
    INDEX idx_uploaded_at (uploaded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- VIEWS für häufige Abfragen
-- =============================================

-- View: Alle Kurse mit Erstellername
CREATE VIEW v_courses_with_creator AS
SELECT 
    c.id,
    c.title,
    c.description,
    c.content,
    c.created_by,
    u.username as creator_name,
    u.email as creator_email,
    c.created_at,
    c.updated_at,
    (SELECT COUNT(*) FROM tasks WHERE course_id = c.id) as task_count,
    (SELECT COUNT(*) FROM files WHERE course_id = c.id) as file_count
FROM courses c
JOIN users u ON c.created_by = u.id;

-- View: Alle ausstehenden Aufgaben
CREATE VIEW v_pending_tasks AS
SELECT 
    t.id,
    t.course_id,
    t.title,
    t.description,
    t.due_date,
    t.status,
    c.title as course_title,
    u.username as course_creator
FROM tasks t
JOIN courses c ON t.course_id = c.id
JOIN users u ON c.created_by = u.id
WHERE t.status = 'pending' AND t.due_date >= CURDATE();

-- View: Benutzer mit Kursanzahl
CREATE VIEW v_users_with_courses AS
SELECT 
    u.id,
    u.username,
    u.email,
    u.role,
    u.created_at,
    (SELECT COUNT(*) FROM courses WHERE created_by = u.id) as course_count,
    (SELECT COUNT(*) FROM tasks t 
     JOIN courses c ON t.course_id = c.id 
     WHERE c.created_by = u.id AND t.status = 'pending') as pending_tasks
FROM users u;

-- =============================================
-- INDICES für bessere Performance
-- =============================================
CREATE INDEX idx_courses_created_by ON courses(created_by);
CREATE INDEX idx_tasks_course_id ON tasks(course_id);
CREATE INDEX idx_files_course_id ON files(course_id);
