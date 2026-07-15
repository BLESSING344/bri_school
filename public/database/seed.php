<?php

/**
 * BRI International School Management System - Database Seeder
 *
 * Creates the schema (if missing) and populates it with realistic sample
 * data so the app is immediately demonstrable after `docker compose up`.
 * Safe to run multiple times: every insert is guarded by an existence check.
 */

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$pdo = Database::connection();

echo "== BRI School Management System - Database Seeder ==\n";

// -------------------------------------------------------------------
// Schema
// -------------------------------------------------------------------
$schema = file_get_contents(__DIR__ . '/schema.sql');
$pdo->exec($schema);
echo "[ok] Schema ensured\n";

// -------------------------------------------------------------------
// Users (one per role, so every role can be demoed)
// -------------------------------------------------------------------
$users = [
    ['username' => 'admin', 'password' => 'admin123', 'full_name' => 'System Administrator', 'role' => 'admin'],
    ['username' => 'grace.teacher', 'password' => 'teacher123', 'full_name' => 'Grace Namuli', 'role' => 'teacher'],
    ['username' => 'peter.teacher', 'password' => 'teacher123', 'full_name' => 'Peter Okello', 'role' => 'teacher'],
    ['username' => 'sarah.bursar', 'password' => 'bursar123', 'full_name' => 'Sarah Kintu', 'role' => 'bursar'],
];

$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
$insertUser = $pdo->prepare('INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)');

foreach ($users as $user) {
    $stmt->execute([$user['username']]);
    if ((int) $stmt->fetchColumn() === 0) {
        $insertUser->execute([
            $user['username'],
            password_hash($user['password'], PASSWORD_DEFAULT),
            $user['full_name'],
            $user['role'],
        ]);
        echo "[ok] User created: {$user['username']} / {$user['password']} ({$user['role']})\n";
    }
}

// -------------------------------------------------------------------
// Classes
// -------------------------------------------------------------------
$classes = [
    ['class_name' => 'Primary One', 'teacher_in_charge' => 'Grace Namuli'],
    ['class_name' => 'Primary Two', 'teacher_in_charge' => 'Peter Okello'],
    ['class_name' => 'Primary Three', 'teacher_in_charge' => 'Grace Namuli'],
];

$stmt = $pdo->prepare('SELECT COUNT(*) FROM classes WHERE class_name = ?');
$insertClass = $pdo->prepare('INSERT INTO classes (class_name, teacher_in_charge) VALUES (?, ?)');

foreach ($classes as $class) {
    $stmt->execute([$class['class_name']]);
    if ((int) $stmt->fetchColumn() === 0) {
        $insertClass->execute([$class['class_name'], $class['teacher_in_charge']]);
        echo "[ok] Class created: {$class['class_name']}\n";
    }
}

// -------------------------------------------------------------------
// Teachers
// -------------------------------------------------------------------
$teachers = [
    ['full_name' => 'Grace Namuli', 'subject' => 'Mathematics', 'phone' => '0700111222', 'email' => 'grace.namuli@brischool.test', 'class_assigned' => 'Primary One'],
    ['full_name' => 'Peter Okello', 'subject' => 'English', 'phone' => '0700333444', 'email' => 'peter.okello@brischool.test', 'class_assigned' => 'Primary Two'],
    ['full_name' => 'Ruth Achieng', 'subject' => 'Science', 'phone' => '0700555666', 'email' => 'ruth.achieng@brischool.test', 'class_assigned' => 'Primary Three'],
];

$stmt = $pdo->prepare('SELECT COUNT(*) FROM teachers WHERE full_name = ?');
$insertTeacher = $pdo->prepare('INSERT INTO teachers (full_name, subject, phone, email, class_assigned) VALUES (?, ?, ?, ?, ?)');

foreach ($teachers as $teacher) {
    $stmt->execute([$teacher['full_name']]);
    if ((int) $stmt->fetchColumn() === 0) {
        $insertTeacher->execute([
            $teacher['full_name'], $teacher['subject'], $teacher['phone'], $teacher['email'], $teacher['class_assigned'],
        ]);
        echo "[ok] Teacher created: {$teacher['full_name']}\n";
    }
}

// -------------------------------------------------------------------
// Students
// -------------------------------------------------------------------
$students = [
    ['full_name' => 'John Mugisha', 'gender' => 'Male', 'class' => 'Primary One', 'parent_name' => 'Mr. Mugisha', 'parent_contact' => '0700123456'],
    ['full_name' => 'Mary Nakato', 'gender' => 'Female', 'class' => 'Primary One', 'parent_name' => 'Mrs. Nakato', 'parent_contact' => '0788123456'],
    ['full_name' => 'David Ssemwogerere', 'gender' => 'Male', 'class' => 'Primary One', 'parent_name' => 'Mr. Ssemwogerere', 'parent_contact' => '0701234567'],
    ['full_name' => 'Grace Nabirye', 'gender' => 'Female', 'class' => 'Primary Two', 'parent_name' => 'Mrs. Nabirye', 'parent_contact' => '0702345678'],
    ['full_name' => 'Samuel Kato', 'gender' => 'Male', 'class' => 'Primary Two', 'parent_name' => 'Mr. Kato', 'parent_contact' => '0703456789'],
    ['full_name' => 'Esther Namutebi', 'gender' => 'Female', 'class' => 'Primary Two', 'parent_name' => 'Mrs. Namutebi', 'parent_contact' => '0704567890'],
    ['full_name' => 'Brian Tumusiime', 'gender' => 'Male', 'class' => 'Primary Three', 'parent_name' => 'Mr. Tumusiime', 'parent_contact' => '0705678901'],
    ['full_name' => 'Patricia Aweko', 'gender' => 'Female', 'class' => 'Primary Three', 'parent_name' => 'Mrs. Aweko', 'parent_contact' => '0706789012'],
];

$stmt = $pdo->prepare('SELECT id FROM students WHERE full_name = ?');
$insertStudent = $pdo->prepare('INSERT INTO students (full_name, gender, class, parent_name, parent_contact) VALUES (?, ?, ?, ?, ?) RETURNING id');

$studentIds = [];
foreach ($students as $student) {
    $stmt->execute([$student['full_name']]);
    $existingId = $stmt->fetchColumn();

    if ($existingId) {
        $studentIds[] = (int) $existingId;
        continue;
    }

    $insertStudent->execute([
        $student['full_name'], $student['gender'], $student['class'], $student['parent_name'], $student['parent_contact'],
    ]);
    $studentIds[] = (int) $insertStudent->fetchColumn();
    echo "[ok] Student created: {$student['full_name']}\n";
}

// -------------------------------------------------------------------
// Attendance (last 5 weekdays, mostly present)
// -------------------------------------------------------------------
$countStmt = $pdo->query('SELECT COUNT(*) FROM attendance');
if ((int) $countStmt->fetchColumn() === 0) {
    $insertAttendance = $pdo->prepare('INSERT INTO attendance (student_id, date, status, recorded_by) VALUES (?, ?, ?, ?)');

    $daysAdded = 0;
    $cursor = new DateTime('today');
    while ($daysAdded < 5) {
        $cursor->modify('-1 day');
        if ((int) $cursor->format('N') >= 6) {
            continue; // skip weekends
        }
        $date = $cursor->format('Y-m-d');

        foreach ($studentIds as $index => $studentId) {
            // Make one student absent every other day for realism
            $status = ($daysAdded === 1 && $index === 2) ? 'Absent' : 'Present';
            $insertAttendance->execute([$studentId, $date, $status, 'System Seed']);
        }
        $daysAdded++;
    }
    echo "[ok] Attendance seeded for {$daysAdded} school days\n";
}

// -------------------------------------------------------------------
// Exams & Marks
// -------------------------------------------------------------------
$stmt = $pdo->prepare('SELECT id FROM exams WHERE exam_name = ? AND class = ?');
$insertExam = $pdo->prepare('INSERT INTO exams (exam_name, term, year, class) VALUES (?, ?, ?, ?) RETURNING id');
$insertMark = $pdo->prepare('INSERT INTO marks (student_id, exam_id, subject, score) VALUES (?, ?, ?, ?)');
$year = (int) date('Y');

$examsByClass = [
    'Primary One' => 'Term 2 Midterm',
    'Primary Two' => 'Term 2 Midterm',
    'Primary Three' => 'Term 2 Midterm',
];

foreach ($examsByClass as $className => $examName) {
    $stmt->execute([$examName, $className]);
    $examId = $stmt->fetchColumn();

    if (!$examId) {
        $insertExam->execute([$examName, 'Term 2', $year, $className]);
        $examId = $insertExam->fetchColumn();
        echo "[ok] Exam created: {$examName} ({$className})\n";

        foreach ($students as $index => $student) {
            if ($student['class'] !== $className) {
                continue;
            }
            $studentId = $studentIds[$index];
            foreach (['Mathematics', 'English', 'Science'] as $subject) {
                $score = rand(55, 98);
                $insertMark->execute([$studentId, $examId, $subject, $score]);
            }
        }
    }
}

// -------------------------------------------------------------------
// Fees
// -------------------------------------------------------------------
$countStmt = $pdo->query('SELECT COUNT(*) FROM fees');
if ((int) $countStmt->fetchColumn() === 0) {
    $insertFee = $pdo->prepare(
        'INSERT INTO fees (student_id, term, amount_paid, balance, recorded_by) VALUES (?, ?, ?, ?, ?)'
    );

    foreach ($studentIds as $index => $studentId) {
        $termFee = 500000; // UGX
        $paid = [500000, 350000, 200000][$index % 3];
        $balance = $termFee - $paid;
        $insertFee->execute([$studentId, 'Term 2', $paid, $balance, 'Sarah Kintu']);
    }
    echo '[ok] Fees seeded for ' . count($studentIds) . " students\n";
}

echo "== Seeding complete ==\n";
echo "Login with: admin / admin123 (or grace.teacher / teacher123, sarah.bursar / bursar123)\n";
