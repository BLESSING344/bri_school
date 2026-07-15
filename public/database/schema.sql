-- BRI International School Management System
-- PostgreSQL schema (converted from the original MySQL install.php)

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'teacher' CHECK (role IN ('admin', 'teacher', 'bursar')),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS teachers (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    subject VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    class_assigned VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    gender VARCHAR(10) NOT NULL CHECK (gender IN ('Male', 'Female')),
    class VARCHAR(50) NOT NULL,
    parent_name VARCHAR(100),
    parent_contact VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS classes (
    id SERIAL PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL UNIQUE,
    teacher_in_charge VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS attendance (
    id SERIAL PRIMARY KEY,
    student_id INTEGER NOT NULL REFERENCES students(id) ON DELETE CASCADE,
    date DATE NOT NULL,
    status VARCHAR(10) NOT NULL DEFAULT 'Present' CHECK (status IN ('Present', 'Absent')),
    recorded_by VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS exams (
    id SERIAL PRIMARY KEY,
    exam_name VARCHAR(100),
    term VARCHAR(20),
    year INTEGER,
    class VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS marks (
    id SERIAL PRIMARY KEY,
    student_id INTEGER NOT NULL REFERENCES students(id) ON DELETE CASCADE,
    exam_id INTEGER NOT NULL REFERENCES exams(id) ON DELETE CASCADE,
    subject VARCHAR(100),
    score DECIMAL(5,2)
);

CREATE TABLE IF NOT EXISTS fees (
    id SERIAL PRIMARY KEY,
    student_id INTEGER NOT NULL REFERENCES students(id) ON DELETE CASCADE,
    term VARCHAR(20),
    amount_paid DECIMAL(10,2) DEFAULT 0,
    balance DECIMAL(10,2) DEFAULT 0,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    recorded_by VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS settings (
    id SERIAL PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_attendance_student ON attendance(student_id);
CREATE INDEX IF NOT EXISTS idx_attendance_date ON attendance(date);
CREATE INDEX IF NOT EXISTS idx_marks_student ON marks(student_id);
CREATE INDEX IF NOT EXISTS idx_marks_exam ON marks(exam_id);
CREATE INDEX IF NOT EXISTS idx_fees_student ON fees(student_id);
CREATE INDEX IF NOT EXISTS idx_students_class ON students(class);
