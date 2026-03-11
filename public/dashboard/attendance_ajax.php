<?php
// Disable error display and ensure clean output for JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start(); // Start output buffering to catch any unexpected output

// For AJAX endpoints, we need to handle sessions and auth without redirects
if (!isset($_SESSION)) {
    session_start();
}

// Set JSON header early
header('Content-Type: application/json; charset=utf-8');

// Check authentication (but don't redirect for AJAX)
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Check role (admin or teacher can access attendance)
$user_role = $_SESSION['role'] ?? '';
if ($user_role !== 'admin' && $user_role !== 'teacher') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Load database connection directly (avoid die() in database.php)
try {
    $host = 'localhost';
    $dbname = 'bri_school';
    $username = 'root';
    $password = 'Blessing1';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Handle the AJAX request
if (isset($_GET['action']) && $_GET['action'] == 'get_students') {
    $class = $_GET['class'] ?? '';
    
    if (empty($class)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Class not specified']);
        exit();
    }
    
    try {
        $sql = "SELECT id, full_name FROM students WHERE class = ? ORDER BY full_name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$class]);
        $students = $stmt->fetchAll();
        
        ob_end_clean();
        echo json_encode(['success' => true, 'students' => $students], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
} else {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
