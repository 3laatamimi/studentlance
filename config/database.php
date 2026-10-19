<?php
class Database {
    private $pdo;
    private static $instance = null;
    
    private function __construct() {
        $dbDir = __DIR__ . '/../database';
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0777, true);
        }
        
        $dbPath = $dbDir . '/studentlance.sqlite';
        $this->pdo = new PDO("sqlite:" . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec("PRAGMA foreign_keys = ON");
        
        $this->createTables();
        $this->insertSampleData();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    private function createTables() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT CHECK(role IN ('student', 'client')) NOT NULL,
                full_name TEXT NOT NULL,
                bio TEXT DEFAULT '',
                skills TEXT DEFAULT '',
                hourly_rate REAL DEFAULT 0,
                avatar TEXT DEFAULT 'default.jpg',
                rating REAL DEFAULT 0,
                total_earnings REAL DEFAULT 0,
                completed_projects INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                category TEXT NOT NULL,
                budget_min REAL NOT NULL,
                budget_max REAL NOT NULL,
                skills_required TEXT DEFAULT '',
                status TEXT DEFAULT 'open',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (client_id) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS bids (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                project_id INTEGER NOT NULL,
                student_id INTEGER NOT NULL,
                amount REAL NOT NULL,
                proposal TEXT NOT NULL,
                status TEXT DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id),
                FOREIGN KEY (student_id) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS reviews (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                project_id INTEGER NOT NULL,
                reviewer_id INTEGER NOT NULL,
                reviewee_id INTEGER NOT NULL,
                rating INTEGER CHECK(rating BETWEEN 1 AND 5),
                comment TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id),
                FOREIGN KEY (reviewer_id) REFERENCES users(id),
                FOREIGN KEY (reviewee_id) REFERENCES users(id)
            );
        ");
    }
    
    private function insertSampleData() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() > 0) return;
        
        // مستخدمون تجريبيون – الأدوار حسب المفهوم الجديد:
        // 'student' = طالب يبحث عن خدمة (ينشر مشاريع)
        // 'client' = عميل يقدم خدمة (مستقل، يربح)
        $users = [
            // مستقلون (يقدمون خدمات) – دورهم client
            ['ahmed_dev', 'ahmed@example.com', password_hash('12345678', PASSWORD_DEFAULT), 'client', 'أحمد محمد', 'مطور ويب طالب علوم حاسب', 'PHP,JavaScript,HTML,CSS', 25.00, 4.8, 12500, 47],
            ['sara_design', 'sara@example.com', password_hash('12345678', PASSWORD_DEFAULT), 'client', 'سارة علي', 'مصممة جرافيك متخصصة', 'Figma,Photoshop,UI/UX', 20.00, 4.5, 8900, 32],
            // طلاب (يبحثون عن خدمات) – دورهم student
            ['mohamed_client', 'mohamed@example.com', password_hash('12345678', PASSWORD_DEFAULT), 'student', 'محمد خالد', 'طالب يحتاج خدمات تقنية', '', 0, 0, 0, 0],
            ['nora_client', 'nora@example.com', password_hash('12345678', PASSWORD_DEFAULT), 'student', 'نورة أحمد', 'طالبة تبحث عن تصميم', '', 0, 0, 0, 0],
        ];
        
        $insertUser = $this->pdo->prepare("INSERT INTO users (username, email, password, role, full_name, bio, skills, hourly_rate, rating, total_earnings, completed_projects) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        foreach ($users as $user) {
            $insertUser->execute($user);
        }
        
        // مشاريع – أصحابها هم الطلاب (دور student) الذين يبحثون عن خدمات
        // client_id هنا يشير إلى المستخدمين 3 و 4 (محمد ونورة)
        $projects = [
            [3, 'تطوير موقع إلكتروني', 'مطلوب مطور لبناء موقع متجر إلكتروني متكامل', 'Web Development', 500, 1000, 'PHP,MySQL,JavaScript'],
            [4, 'تصميم شعار', 'تصميم شعار احترافي لشركة ناشئة', 'Graphic Design', 100, 300, 'Photoshop,Illustrator'],
            [3, 'تطبيق جوال', 'تطوير تطبيق جوال للايفون والاندرويد', 'Mobile Apps', 800, 1500, 'React Native,Flutter'],
        ];
        
        $insertProject = $this->pdo->prepare("INSERT INTO projects (client_id, title, description, category, budget_min, budget_max, skills_required) VALUES (?,?,?,?,?,?,?)");
        foreach ($projects as $project) {
            $insertProject->execute($project);
        }
    }
}
?>