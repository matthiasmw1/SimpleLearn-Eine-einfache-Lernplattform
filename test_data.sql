
USE simplelearn;

-- =============================================
-- TEST USERS mit sicheren Passwörtern
-- =============================================
-- Admin-Passwort: aB1cDeFgHiJkLmNoPqRsTuVwXyZ
-- TestUser1: SecurePass123AbcdEfghIjklMnop
-- TestUser2: MyPassword2026TestDataAustria
-- TestUser3: ComplexPass123XyzAbcDefGhijkl

INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@simplelearn.test', '$2y$10$8hU7Q.5V9.W2L3M4N5O6P7Q8R9S0T1U2V3W4X5Y6Z7a8b9c0d1e2f', 'admin'),
('testuser1', 'anna.mueller@simplelearn.test', '$2y$10$9iV8R.6W0.X3M4N5O6P7Q8R9S0T1U2V3W4X5Y6Z7a8b9c0d1e2f3g', 'user'),
('testuser2', 'michael.schmidt@simplelearn.test', '$2y$10$0jW9S.7X1.Y4M5N6O7P8Q9R0S1T2U3V4W5X6Y7Z8a9b0c1d2e3f4h', 'user'),
('testuser3', 'sophia.weber@simplelearn.test', '$2y$10$1kX0T.8Y2.Z5M6N7O8P9Q0R1S2T3U4V5W6X7Y8Z9a0b1c2d3e4f5i', 'user');


-- =============================================
-- TEST COURSES - Verschiedene Fachbereiche
-- =============================================

-- Kurs 1: Von Admin erstellt
INSERT INTO courses (id, title, description, content, created_by) VALUES
(1, 'Webentwicklung mit PHP', 
 'Lerne die Grundlagen von PHP und erstelle dynamische Webseiten. Dieser umfassende Kurs behandelt Variablen, Funktionen, Arrays, Datenbankverbindungen und Best Practices.',
 'KAPITEL 1: PHP Grundlagen\n- Variablen und Datentypen\n- Kontrollstrukturen (if, else, switch)\n- Schleifen (for, while, foreach)\n\nKAPITEL 2: Funktionen\n- Funktionsdefinition\n- Parameter und Rückgabewerte\n- Scope und Variablenbereich\n\nKAPITEL 3: Arrays und Objekte\n- Indexed Arrays\n- Associative Arrays\n- Multidimensionale Arrays\n\nKAPITEL 4: Datenbankanbindung\n- MySQL/MariaDB Grundlagen\n- PDO und prepared Statements\n- CRUD Operationen',
 1);

-- Kurs 2: Von Admin erstellt
INSERT INTO courses (id, title, description, content, created_by) VALUES
(2, 'Frontend Design - HTML, CSS & Bootstrap',
 'Gestalte moderne, responsive Webseiten mit HTML5, CSS3 und Bootstrap. Lerne Best Practices für benutzerfreundliches Web Design.',
 'KAPITEL 1: HTML5 Struktur\n- Semantisches HTML\n- Forms und Input-Typen\n- Barrierefreie HTML\n\nKAPITEL 2: CSS3 Styling\n- Selektoren und Spezifität\n- Box-Modell\n- Flexbox und Grid\n- Responsive Design mit Media Queries\n\nKAPITEL 3: Bootstrap Framework\n- Bootstrap Grid System\n- Komponenten (Buttons, Cards, Navbar)\n- Customization und Theming\n\nKAPITEL 4: Praxisprojekte\n- Landing Page\n- Blog Layout\n- E-Commerce Produktseite',
 1);

-- Kurs 3: Von TestUser1 erstellt
INSERT INTO courses (id, title, description, content, created_by) VALUES
(3, 'JavaScript - Interaktive Webseiten',
 'Meistere JavaScript und erstelle interaktive, dynamische Webseiten. Von Anfänger bis fortgeschrittene Konzepte wie Async/Await und APIs.',
 'KAPITEL 1: JavaScript Grundlagen\n- Variablen (var, let, const)\n- Datentypen\n- Operatoren\n\nKAPITEL 2: DOM Manipulation\n- DOM Auswahl (querySelector, getElementById)\n- Event Handling\n- DOM Modifikation\n\nKAPITEL 3: Asynchrones JavaScript\n- Callbacks\n- Promises\n- Async/Await\n\nKAPITEL 4: APIs und Fetch\n- REST APIs verstehen\n- Fetch API\n- JSON Datenformat\n- Fehlerbehandlung',
 2);

-- Kurs 4: Von TestUser2 erstellt
INSERT INTO courses (id, title, description, content, created_by) VALUES
(4, 'Datenbanken & SQL Mastery',
 'Lerne relationale Datenbanken, SQL-Queries und Datenbankoptimierung. Perfekt für Backend-Entwickler und Datenbankadministratoren.',
 'KAPITEL 1: Datenbankkonzepte\n- Normalisierung (1NF, 2NF, 3NF)\n- Entity-Relationship Modell\n- Beziehungen (1:1, 1:N, N:M)\n\nKAPITEL 2: SQL Grundlagen\n- SELECT, INSERT, UPDATE, DELETE\n- WHERE Klauseln\n- ORDER BY und GROUP BY\n\nKAPITEL 3: Fortgeschrittene SQL\n- JOINs (INNER, LEFT, RIGHT, FULL)\n- Subqueries\n- Aggregationsfunktionen\n\nKAPITEL 4: Optimierung\n- Indexe\n- Query Optimization\n- Performance Tuning',
 3);


-- =============================================
-- TEST TASKS - Aufgaben für die Kurse
-- =============================================

-- Tasks für Kurs 1 (PHP)
INSERT INTO tasks (course_id, title, description, due_date, status) VALUES
(1, 'Hello World in PHP',
 'Schreibe dein erstes PHP-Skript, das "Hello World" auf dem Bildschirm ausgibt. Nutze echo oder print.',
 DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'pending'),

(1, 'Variablen und Datentypen',
 'Erstelle ein PHP-Skript mit verschiedenen Variablentypen (String, Int, Float, Boolean, Array) und gib sie aus.',
 DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'pending'),

(1, 'Funktionen implementieren',
 'Schreibe eine Funktion "addNumbers", die zwei Parameter akzeptiert und deren Summe zurückgibt. Teste die Funktion.',
 DATE_ADD(CURDATE(), INTERVAL 21 DAY), 'submitted'),

(1, 'CRUD Operationen mit Datenbank',
 'Erstelle ein PHP-Skript mit Create, Read, Update, Delete Operationen für eine einfache Benutzertabelle.',
 DATE_ADD(CURDATE(), INTERVAL 28 DAY), 'pending');

-- Tasks für Kurs 2 (Frontend Design)
INSERT INTO tasks (course_id, title, description, due_date, status) VALUES
(2, 'Responsive Webseite',
 'Erstelle eine responsive HTML/CSS-Webseite mit Mobile, Tablet und Desktop Layout. Nutze Media Queries.',
 DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'submitted'),

(2, 'Bootstrap Landing Page',
 'Designen Sie eine moderne Landing Page mit Bootstrap. Include: Header, Hero Section, Features, Call-to-Action, Footer.',
 DATE_ADD(CURDATE(), INTERVAL 17 DAY), 'pending'),

(2, 'Formular mit Validierung',
 'Erstelle ein HTML-Formular mit CSS-Styling und Basic Validierung für E-Mail, Name und Nachricht.',
 DATE_ADD(CURDATE(), INTERVAL 24 DAY), 'graded');

-- Tasks für Kurs 3 (JavaScript)
INSERT INTO tasks (course_id, title, description, due_date, status) VALUES
(3, 'DOM Manipulation Todo-Liste',
 'Erstelle eine Todo-Liste mit JavaScript: Add, Delete, Mark as Done Funktionalität.',
 DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'submitted'),

(3, 'Wetter-API Integration',
 'Nutze eine öffentliche Wetter-API (z.B. OpenWeatherMap) um aktuelle Wetterdaten anzuzeigen.',
 DATE_ADD(CURDATE(), INTERVAL 12 DAY), 'pending'),

(3, 'Event Handling & Listeners',
 'Implementiere verschiedene Event Listener (click, change, submit, keyup) mit Callbacks.',
 DATE_ADD(CURDATE(), INTERVAL 19 DAY), 'pending');

-- Tasks für Kurs 4 (SQL)
INSERT INTO tasks (course_id, title, description, due_date, status) VALUES
(4, 'SELECT & WHERE Queries',
 'Schreibe 5 verschiedene SELECT Queries mit WHERE, ORDER BY und LIMIT.',
 DATE_ADD(CURDATE(), INTERVAL 8 DAY), 'graded'),

(4, 'JOINs verstehen',
 'Praktiziere INNER JOIN, LEFT JOIN und RIGHT JOIN mit Beispieldaten. Nutze 2+ Tabellen.',
 DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'submitted'),

(4, 'Indexe und Performance',
 'Erstelle Indizes für häufig gesuchte Spalten und vergleiche Query-Performance vorher/nachher.',
 DATE_ADD(CURDATE(), INTERVAL 22 DAY), 'pending');


-- =============================================
-- TEST FILES - Dateien pro Kurs
-- =============================================

-- Files für Kurs 1 (PHP)
INSERT INTO files (course_id, file_name, file_path, file_size, file_type, uploaded_at) VALUES
(1, 'PHP_Grundlagen_Skript.pdf', '/uploads/courses/1/PHP_Grundlagen_Skript.pdf', 2048576, 'application/pdf', NOW()),
(1, 'Beispiel_Code.zip', '/uploads/courses/1/Beispiel_Code.zip', 5242880, 'application/zip', NOW()),
(1, 'Cheatsheet_PHP.docx', '/uploads/courses/1/Cheatsheet_PHP.docx', 1048576, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', NOW());

-- Files für Kurs 2 (Frontend)
INSERT INTO files (course_id, file_name, file_path, file_size, file_type, uploaded_at) VALUES
(2, 'Bootstrap_Dokumentation.pdf', '/uploads/courses/2/Bootstrap_Dokumentation.pdf', 3145728, 'application/pdf', NOW()),
(2, 'CSS_Grid_Guide.docx', '/uploads/courses/2/CSS_Grid_Guide.docx', 1572864, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', NOW()),
(2, 'Responsive_Templates.zip', '/uploads/courses/2/Responsive_Templates.zip', 8388608, 'application/zip', NOW());

-- Files für Kurs 3 (JavaScript)
INSERT INTO files (course_id, file_name, file_path, file_size, file_type, uploaded_at) VALUES
(3, 'JavaScript_ES6_Cheatsheet.pdf', '/uploads/courses/3/JavaScript_ES6_Cheatsheet.pdf', 2097152, 'application/pdf', NOW()),
(3, 'DOM_Beispiele.zip', '/uploads/courses/3/DOM_Beispiele.zip', 4194304, 'application/zip', NOW());

-- Files für Kurs 4 (SQL)
INSERT INTO files (course_id, file_name, file_path, file_size, file_type, uploaded_at) VALUES
(4, 'SQL_Joins_Visualisiert.pdf', '/uploads/courses/4/SQL_Joins_Visualisiert.pdf', 2560000, 'application/pdf', NOW()),
(4, 'Datenbank_Schema.docx', '/uploads/courses/4/Datenbank_Schema.docx', 1835008, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', NOW()),
(4, 'Sample_Datenbank.sql', '/uploads/courses/4/Sample_Datenbank.sql', 512000, 'text/plain', NOW());


-- =============================================
-- DATEN ÜBERSICHT
-- =============================================
-- Users: 4 (1 Admin, 3 Regular)
-- Courses: 4
-- Tasks: 12 (verschiedene Status)
-- Files: 10
-- =============================================
-- Test Benutzer Login-Daten:
-- =============================================
-- Admin: admin / aB1cDeFgHiJkLmNoPqRsTuVwXyZ
-- User1: testuser1 / SecurePass123AbcdEfghIjklMnop
-- User2: testuser2 / MyPassword2026TestDataAustria
-- User3: testuser3 / ComplexPass123XyzAbcDefGhijkl
-- =============================================
