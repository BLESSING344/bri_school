<?php
/**
 * Authentication Helper Functions
 * Provides role-based access control
 */

if (!isset($_SESSION)) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

/**
 * Check if user is logged in, redirect if not
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit();
    }
}

/**
 * Check if user has required role, redirect if not
 */
function requireRole($required_roles) {
    requireLogin();
    
    if (!isset($_SESSION['role'])) {
        header('Location: ../login.php');
        exit();
    }
    
    if (is_array($required_roles)) {
        if (!in_array($_SESSION['role'], $required_roles)) {
            header('Location: index.php?error=access_denied');
            exit();
        }
    } else {
        if ($_SESSION['role'] !== $required_roles) {
            header('Location: index.php?error=access_denied');
            exit();
        }
    }
}

/**
 * Get current user data
 */
function getCurrentUser($pdo) {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Check if user has permission
 */
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return hasRole('admin');
}

/**
 * Check if user is teacher
 */
function isTeacher() {
    return hasRole('teacher');
}

/**
 * Check if user is bursar
 */
function isBursar() {
    return hasRole('bursar');
}
?>
