SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS __uuid_user_map (
    old_id INT PRIMARY KEY,
    new_id CHAR(36) NOT NULL
);

CREATE TABLE IF NOT EXISTS __uuid_course_map (
    old_id INT PRIMARY KEY,
    new_id CHAR(36) NOT NULL
);

TRUNCATE TABLE __uuid_user_map;
TRUNCATE TABLE __uuid_course_map;

INSERT INTO __uuid_user_map (old_id, new_id)
SELECT id, UUID() FROM users;

INSERT INTO __uuid_course_map (old_id, new_id)
SELECT id, UUID() FROM courses;

ALTER TABLE users DROP PRIMARY KEY;
ALTER TABLE users MODIFY id CHAR(36) NOT NULL;
UPDATE users u
JOIN __uuid_user_map m ON m.old_id = CAST(u.id AS UNSIGNED)
SET u.id = m.new_id;
ALTER TABLE users ADD PRIMARY KEY (id);

ALTER TABLE courses DROP PRIMARY KEY;
ALTER TABLE courses MODIFY id CHAR(36) NOT NULL;
UPDATE courses c
JOIN __uuid_course_map m ON m.old_id = CAST(c.id AS UNSIGNED)
SET c.id = m.new_id;
ALTER TABLE courses ADD PRIMARY KEY (id);

ALTER TABLE email_verifications MODIFY user_id CHAR(36) NOT NULL;
UPDATE email_verifications ev
JOIN __uuid_user_map m ON m.old_id = CAST(ev.user_id AS UNSIGNED)
SET ev.user_id = m.new_id;

ALTER TABLE password_resets MODIFY user_id CHAR(36) NOT NULL;
UPDATE password_resets pr
JOIN __uuid_user_map m ON m.old_id = CAST(pr.user_id AS UNSIGNED)
SET pr.user_id = m.new_id;

ALTER TABLE access_codes MODIFY course_id CHAR(36) NOT NULL;
UPDATE access_codes ac
JOIN __uuid_course_map m ON m.old_id = CAST(ac.course_id AS UNSIGNED)
SET ac.course_id = m.new_id;

ALTER TABLE user_courses MODIFY user_id CHAR(36) NOT NULL, MODIFY course_id CHAR(36) NOT NULL;
UPDATE user_courses uc
JOIN __uuid_user_map m ON m.old_id = CAST(uc.user_id AS UNSIGNED)
SET uc.user_id = m.new_id;

UPDATE user_courses uc
JOIN __uuid_course_map m ON m.old_id = CAST(uc.course_id AS UNSIGNED)
SET uc.course_id = m.new_id;

SET FOREIGN_KEY_CHECKS = 1;

DROP TABLE IF EXISTS __uuid_user_map;
DROP TABLE IF EXISTS __uuid_course_map;
