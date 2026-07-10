ALTER TABLE user_courses
    ADD COLUMN is_completed TINYINT(1) NOT NULL DEFAULT 0 AFTER granted_at,
    ADD COLUMN completed_at DATETIME NULL AFTER is_completed;

CREATE TABLE IF NOT EXISTS course_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id CHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_module_completions (
    user_id CHAR(36) NOT NULL,
    module_id INT NOT NULL,
    completed_at DATETIME NOT NULL,
    PRIMARY KEY (user_id, module_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (module_id) REFERENCES course_modules(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS module_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    title VARCHAR(255) NULL,
    html_content TEXT,
    audio_url VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_quiz TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (module_id) REFERENCES course_modules(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slide_id INT NOT NULL,
    question_text TEXT NOT NULL,
    FOREIGN KEY (slide_id) REFERENCES module_slides(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS question_choices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    choice_text VARCHAR(255) NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
);
