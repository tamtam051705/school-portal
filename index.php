<?php
session_start();

if (empty($_SESSION['user_id']) || empty($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$role = strtolower(trim((string)($_SESSION['user_role'] ?? '')));
header('Location: ' . ($role === 'admin' ? 'dashboard.php' : 'subjects.php'));
exit();
