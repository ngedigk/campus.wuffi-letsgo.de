CREATE TABLE users (
    id CHAR(36) NOT NULL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email_verified TINYINT(1) NOT NULL DEFAULT 0,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE email_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    token CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    token CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE courses (
    id CHAR(36) NOT NULL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    prerequisite_course_id CHAR(36) NULL,
    FOREIGN KEY (prerequisite_course_id) REFERENCES courses(id) ON DELETE SET NULL
);

CREATE TABLE access_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(100) NOT NULL UNIQUE,
    course_id CHAR(36) NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE user_courses (
    user_id CHAR(36) NOT NULL,
    course_id CHAR(36) NOT NULL,
    access_code_id INT NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_completed TINYINT(1) NOT NULL DEFAULT 0,
    completed_at DATETIME NULL,
    PRIMARY KEY (user_id, course_id),
    UNIQUE KEY access_code_id (access_code_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (access_code_id) REFERENCES access_codes(id) ON DELETE SET NULL
);

CREATE TABLE course_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id CHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE module_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    title VARCHAR(255) NULL,
    html_content TEXT,
    audio_url VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_quiz TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (module_id) REFERENCES course_modules(id) ON DELETE CASCADE
);

CREATE TABLE user_module_completions (
    user_id CHAR(36) NOT NULL,
    module_id INT NOT NULL,
    completed_at DATETIME NOT NULL,
    PRIMARY KEY (user_id, module_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (module_id) REFERENCES course_modules(id) ON DELETE CASCADE
);

CREATE TABLE user_slide_views (
    user_id CHAR(36) NOT NULL,
    slide_id INT NOT NULL,
    viewed_at DATETIME NOT NULL,
    PRIMARY KEY (user_id, slide_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (slide_id) REFERENCES module_slides(id) ON DELETE CASCADE
);

CREATE TABLE quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slide_id INT NOT NULL,
    question_text TEXT NOT NULL,
    FOREIGN KEY (slide_id) REFERENCES module_slides(id) ON DELETE CASCADE
);

CREATE TABLE question_choices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    choice_text VARCHAR(255) NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
);

CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45),
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_login_ip_time ON login_attempts (ip, attempted_at);