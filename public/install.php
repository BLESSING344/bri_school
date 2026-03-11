<?php
/**
 * Bri International School Management System - Database Setup
 * 
 * Run this file once to set up your local database.
 * Works perfectly on XAMPP or local PHP server.
 */

$host = 'localhost';
$username = 'root';
$password = 'Blessing1'; // change if your XAMPP MySQL password is different
$database_name = 'bri_school';

echo "<h2>Bri International School Management System - Installation</h2><hr>";

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p>✓ Connected to MySQL server successfully</p>";

    // Create the database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database_name` CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "<p>✓ Database '$database_name' created or already exists</p>";

    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$database_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // -------------------------------
    // Create Users table
    // -------------------------------
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM('admin', 'teacher', 'bursar') DEFAULT 'teacher',
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");
    echo "<p>✓ Users table created</p>";

    // -------------------------------
    // Create Teachers table
    // -------------------------------
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS teachers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            subject VARCHAR(100),
            phone VARCHAR(20),
            email VARCHAR(100),
            class_assigned VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");
    echo "<p>✓ Teachers table created</p>";

    // -------------------------------
    // Create Students table
    // -------------------------------
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            gender ENUM('Male', 'Female') NOT NULL,
            class VARCHAR(50) NOT NULL,
            parent_name VARCHAR(100),
            parent_contact VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");
    echo "<p>✓ Students table created</p>";

    // -------------------------------
    // Create Classes table
    // -------------------------------
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS classes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            class_name VARCHAR(50) NOT NULL UNIQUE,
            teacher_in_charge VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");
    echo "<p>✓ Classes table created</p>";

    // -------------------------------
    // Create Attendance table
    // -------------------------------
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS attendance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            date DATE NOT NULL,
            status ENUM('Present', 'Absent') DEFAULT 'Present',
            recorded_by VARCHAR(100),
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "<p>✓ Attendance table created</p>";

    // -------------------------------
    // Create Exams and Marks tables
    // -------------------------------
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS exams (
            id INT AUTO_INCREMENT PRIMARY KEY,
            exam_name VARCHAR(100),
            term VARCHAR(20),
            year YEAR,
            class VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");
    echo "<p>✓ Exams table created</p>";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS marks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            exam_id INT NOT NULL,
            subject VARCHAR(100),
            score DECIMAL(5,2),
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "<p>✓ Marks table created</p>";

    // -------------------------------
    // Create Fees table
    // -------------------------------
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS fees (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            term VARCHAR(20),
            amount_paid DECIMAL(10,2) DEFAULT 0,
            balance DECIMAL(10,2) DEFAULT 0,
            payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            recorded_by VARCHAR(100),
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    echo "<p>✓ Fees table created</p>";

    // -------------------------------
    // Create default admin
    // -------------------------------
    $stmt = $pdo->query("SELECT COUNT(*) AS count FROM users WHERE username='admin'");
    $exists = $stmt->fetchColumn();

    if ($exists == 0) {
        $password_hashed = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES ('admin', ?, 'System Administrator', 'admin')")
            ->execute([$password_hashed]);
        echo "<p>✓ Default admin user created</p>";

        echo "<div style='background:#d4edda;padding:10px;border-radius:5px;'>";
        echo "<h4>Login Credentials</h4>";
        echo "Username: <strong>admin</strong><br>";
        echo "Password: <strong>admin123</strong><br>";
        echo "</div>";
    } else {
        echo "<p>✓ Admin user already exists</p>";
    }

    // -------------------------------
    // Insert sample data
    // -------------------------------
    $pdo->exec("
        INSERT IGNORE INTO classes (class_name, teacher_in_charge)
        VALUES ('Primary One', 'Teacher Grace'), ('Primary Two', 'Teacher Peter');
    ");

    $pdo->exec("
        INSERT IGNORE INTO students (full_name, gender, class, parent_name, parent_contact)
        VALUES 
        ('John Mugisha', 'Male', 'Primary One', 'Mr. Mugisha', '0700123456'),
        ('Mary Nakato', 'Female', 'Primary Two', 'Mrs. Nakato', '0788123456');
    ");

    echo "<p>✓ Sample classes and students added</p>";

    echo "<hr>";
    echo "<div style='background:#e9f7ef;padding:15px;border-radius:5px;'>";
    echo "<h3>Installation Complete 🎉</h3>";
    echo "<p>Your Bri International School Management System is ready!</p>";
    echo "<ul>
            <li>Go to <a href='login.php'>login.php</a> to sign in</li>
            <li>Use the admin credentials above</li>
            <li>Delete this install.php file after successful setup for security</li>
          </ul>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;'>";
    echo "<h4>Error:</h4> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>
