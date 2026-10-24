<?php
// Include at the very beginning of every protected page.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (empty($_SESSION['user_id']) || empty($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

/*
 * Role-based page permissions.
 * Admin: all pages.
 * Registrar: Subjects, Term, Instructor, School Year.
 * Dean: Subjects, Section, Instructor, Year Level.
 * Instructor: Subjects, Section.
 * Student: Subjects only.
 */
$PAGE_PERMISSIONS = [
    'admin' => ['*'],
    'registrar' => ['subjects.php', 'subjects_edit.php', 'term.php', 'term_edit.php', 'instructor.php', 'instructors.php', 'schoolyear.php', 'schoolyear_edit.php'],
    'dean' => ['subjects.php', 'subjects_edit.php', 'section.php', 'section_edit.php','yearlevel.php', 'yearlevel_edit.php', 'instructor.php', 'instructors.php'],
    'instructor' => ['subjects.php', 'subjects_edit.php', 'section.php', 'section_edit.php'],
    'student' => ['subjects.php'],
];

$ROLE_ALIASES = [
    'admin' => 'admin',
    'registrar' => 'registrar',
    'dean' => 'dean',
    'program head' => 'dean',
    'instructor' => 'instructor',
    'professor' => 'instructor',
    'student' => 'student',
];

function current_user_role() {
    global $conn;
    if (!empty($_SESSION['user_role'])) {
        return strtolower(trim($_SESSION['user_role']));
    }

    // Fallback for sessions created before role was stored.
    if (!empty($_SESSION['usertype_id'])) {
        include_once __DIR__ . '/db_connect.php';
        $id = (int)$_SESSION['usertype_id'];
        $stmt = mysqli_prepare($conn, "SELECT Usertype FROM tbl_usertype WHERE ID = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                $role = strtolower(trim($row['Usertype']));
                $_SESSION['user_role'] = $role;
                mysqli_stmt_close($stmt);
                return $role;
            }
            mysqli_stmt_close($stmt);
        }
    }
    return '';
}

function normalize_role($role) {
    global $ROLE_ALIASES;
    $key = strtolower(trim((string)$role));
    return $ROLE_ALIASES[$key] ?? $key;
}

function can_access_page($page) {
    global $PAGE_PERMISSIONS;
    $role = normalize_role(current_user_role());
    if ($role === 'admin') return true;
    $allowed = $PAGE_PERMISSIONS[$role] ?? [];
    return in_array(strtolower($page), array_map('strtolower', $allowed), true);
}

function require_page_access($page = null) {
    if ($page === null) {
        $page = basename($_SERVER['PHP_SELF']);
    }
    if (!can_access_page($page)) {
        $role = normalize_role(current_user_role());
        $fallbacks = [
            'registrar' => 'subjects.php',
            'dean' => 'subjects.php',
            'instructor' => 'subjects.php',
            'student' => 'subjects.php',
        ];
        $fallback = $fallbacks[$role] ?? 'login.php';
        header('Location: ' . $fallback . '?access=denied');
        exit();
    }
}

require_page_access();

$session_display_name = trim((string)($_SESSION['fullname'] ?? ''));
if ($session_display_name === '') {
    $session_display_name = (string)$_SESSION['username'];
}
$session_role = normalize_role(current_user_role());
if (!isset($_SESSION['user_image'])) {
    include_once __DIR__ . '/db_connect.php';
    $currentId = (int)$_SESSION['user_id'];
    $imageStmt = mysqli_prepare($conn, "SELECT ImagePath FROM tbl_users WHERE ID = ? LIMIT 1");
    if ($imageStmt) {
        mysqli_stmt_bind_param($imageStmt, 'i', $currentId);
        mysqli_stmt_execute($imageStmt);
        $imageRes = mysqli_stmt_get_result($imageStmt);
        if ($imageRow = mysqli_fetch_assoc($imageRes)) $_SESSION['user_image'] = $imageRow['ImagePath'] ?? '';
        mysqli_stmt_close($imageStmt);
    }
}
