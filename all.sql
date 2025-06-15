-- 1. roles – Role management

CREATE TABLE roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(30) UNIQUE NOT NULL
);
-- 2. users

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    username VARCHAR(50) UNIQUE,
    password_hash VARCHAR(255),
    role_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);
-- 3. tasks

CREATE TABLE tasks (
    task_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    description TEXT,
    deadline DATETIME,
    status ENUM('not_started', 'ongoing', 'completed', 'verified', 'not_accepted') DEFAULT 'not_started',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);
-- 4. task_assignments

CREATE TABLE task_assignments (
    assignment_id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT,
    user_id INT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);


-- 7. task_comments – For users/managers to add comments

CREATE TABLE task_comments (
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT,
    user_id INT,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 5. task_submissions

CREATE TABLE task_submissions (
    submission_id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT,
    user_id INT,
    text_content TEXT,
    media_url VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
-- 8. task_status_logs – Track all status changes

CREATE TABLE task_status_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT,
    old_status ENUM('not_started', 'ongoing', 'completed', 'verified', 'not_accepted'),
    new_status ENUM('not_started', 'ongoing', 'completed', 'verified', 'not_accepted'),
    changed_by INT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (changed_by) REFERENCES users(user_id)
);

-- 6. task_verifications

CREATE TABLE task_verifications (
    verification_id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT,
    manager_id INT,
    is_verified BOOLEAN,
    instructions TEXT,
    verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id),
    FOREIGN KEY (manager_id) REFERENCES users(user_id)
);
-- 9. action_logs – Admin/system-level audits

CREATE TABLE action_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action_type VARCHAR(100),
    action_details TEXT,
    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);


















-- 🧩 1. message_threads
-- Represents a conversation (either one-on-one or group)


CREATE TABLE message_threads (
    thread_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255), -- optional, useful for group threads
    is_group BOOLEAN DEFAULT FALSE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);
-- 👥 2. thread_participants
-- Users participating in a thread


CREATE TABLE thread_participants (
    thread_id INT,
    user_id INT,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (thread_id, user_id),
    FOREIGN KEY (thread_id) REFERENCES message_threads(thread_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
-- 💬 3. thread_messages
-- Actual messages within a thread


CREATE TABLE thread_messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    thread_id INT,
    sender_id INT,
    message_text TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (thread_id) REFERENCES message_threads(thread_id),
    FOREIGN KEY (sender_id) REFERENCES users(user_id)
);
-- 📎 4. message_attachments
-- Files/images/audio/etc. sent with a message


CREATE TABLE message_attachments (
    attachment_id INT PRIMARY KEY AUTO_INCREMENT,
    message_id INT,
    file_url VARCHAR(255),
    file_type VARCHAR(50), -- e.g. image/png, application/pdf
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES thread_messages(message_id)
);
-- 👁️ 5. message_reads
-- Tracks who has read a message


CREATE TABLE message_reads (
    message_id INT,
    user_id INT,
    read_at TIMESTAMP,
    PRIMARY KEY (message_id, user_id),
    FOREIGN KEY (message_id) REFERENCES thread_messages(message_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
-- 📝 Optional Enhancements
-- ✅ message_reactions – likes, emojis, etc.

CREATE TABLE message_reactions (
    reaction_id INT PRIMARY KEY AUTO_INCREMENT,
    message_id INT,
    user_id INT,
    reaction_type VARCHAR(50), -- e.g. "like", "thumbs_up", "heart"
    reacted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES thread_messages(message_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);