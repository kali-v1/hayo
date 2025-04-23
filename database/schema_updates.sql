-- 1. Create companies table
CREATE TABLE IF NOT EXISTS companies (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    logo VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Add company_id to courses table
ALTER TABLE courses 
ADD COLUMN company_id INT(11) DEFAULT NULL,
ADD COLUMN deleted_at DATETIME DEFAULT NULL,
ADD CONSTRAINT fk_courses_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL;

-- 3. Add company_id to exams table
ALTER TABLE exams 
ADD COLUMN company_id INT(11) DEFAULT NULL,
ADD COLUMN deleted_at DATETIME DEFAULT NULL,
ADD CONSTRAINT fk_exams_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL;

-- 4. Add mobile_number and deleted_at to users table
ALTER TABLE users 
ADD COLUMN mobile_number VARCHAR(20) DEFAULT NULL,
ADD COLUMN deleted_at DATETIME DEFAULT NULL;

-- 5. Add deleted_at to other important tables
ALTER TABLE questions ADD COLUMN deleted_at DATETIME DEFAULT NULL;
ALTER TABLE lessons ADD COLUMN deleted_at DATETIME DEFAULT NULL;
ALTER TABLE enrollments ADD COLUMN deleted_at DATETIME DEFAULT NULL;
ALTER TABLE reviews ADD COLUMN deleted_at DATETIME DEFAULT NULL;
ALTER TABLE certificates ADD COLUMN deleted_at DATETIME DEFAULT NULL;
ALTER TABLE exam_attempts ADD COLUMN deleted_at DATETIME DEFAULT NULL;