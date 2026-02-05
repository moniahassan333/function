-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS php_mvc_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE php_mvc_app;

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    profile_image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول المنشورات
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول التصنيفات
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول العلاقة بين المنشورات والتصنيفات (Many-to-Many)
CREATE TABLE IF NOT EXISTS post_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    category_id INT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_post_category (post_id, category_id),
    INDEX idx_post_id (post_id),
    INDEX idx_category_id (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إدراج بيانات تجريبية

-- مستخدمين تجريبيين (كلمة المرور: password123)
INSERT INTO users (username, email, password, role) VALUES
('علي', 'ali@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
(' منيا', 'monia@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(' ساره', 'sara@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- تصنيفات


-- التصنيفات ()
INSERT INTO categories (name, description) VALUES
('فانتازيا', 'روايات خيالية مليئة بالسحر والعوالم الغامضة'),
('رعب', 'قصص مرعبة وأحداث مظلمة ومخيفة'),
('خيال علمي', 'عالم مستقبلي وتقنيات وأحداث غير واقعية'),
('غموض', 'أسرار، جرائم، وأحداث غير متوقعة'),
('مغامرات', 'رحلات مشوقة وأحداث مليئة بالإثارة');

-- المنشورات (نفس user_id)
INSERT INTO posts (user_id, title, content) VALUES
(1, 'مملكة الظلال', 'في عالم تحكمه السحر واللعنات، تكتشف فتاة أنها الوريثة الأخيرة لعرشٍ منسي.'),
(1, 'البيت الذي لا ينام', 'منزل مهجور تظهر فيه همسات مرعبة كل ليلة، ولا أحد يجرؤ على البقاء حتى الفجر.'),
(2, 'ما بعد المجرة', 'بعد اكتشاف بوابة فضائية، يجد البشر أنفسهم أمام حضارات لا ترحم.'),
(2, 'سر الدم القديم', 'تحقيق غامض يقود إلى أسطورة قديمة عن دم يمنح الخلود بثمنٍ مرعب.'),
(3, 'رحلة إلى أرض التيه', 'مجموعة مغامرين يدخلون أرضًا لا تخضع للزمان ولا يعود منها الجميع.');

-- الربط (نفس post_id و category_id)
INSERT INTO post_categories (post_id, category_id) VALUES
(1, 1), (1, 4),
(2, 2), (2, 4),
(3, 3), (3, 1),
(4, 2), (4, 4),
(5, 5), (5, 1);


