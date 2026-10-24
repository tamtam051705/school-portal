<?php
session_start();
include 'db_connect.php';

if (!empty($_SESSION['user_id']) && !empty($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        header('Location: login.php?error=empty');
        exit();
    }

    $stmt = mysqli_prepare($conn, "SELECT ID, username, password, Fullname, UsertypeID, ImagePath FROM tbl_users WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $storedHash = (string)$user['password'];

        // Passwords are stored as one-way hashes. Never decrypt or compare
        // the plain-text password directly with the database value.
        if (!password_verify($password, $storedHash)) {
            mysqli_stmt_close($stmt);
            header('Location: login.php?error=invalid');
            exit();
        }

        // Rehash automatically if PHP's recommended hashing algorithm/cost changes.
        if (password_needs_rehash($storedHash, PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $upgrade = mysqli_prepare($conn, 'UPDATE tbl_users SET password = ? WHERE ID = ?');
            mysqli_stmt_bind_param($upgrade, 'si', $newHash, $user['ID']);
            mysqli_stmt_execute($upgrade);
            mysqli_stmt_close($upgrade);
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['ID'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['fullname'] = $user['Fullname'];
        $_SESSION['user_image'] = $user['ImagePath'] ?? '';
        $_SESSION['usertype_id'] = (int)$user['UsertypeID'];

        // Store the user type in the session for role-based authorization.
        $roleStmt = mysqli_prepare($conn, "SELECT Usertype FROM tbl_usertype WHERE ID = ? LIMIT 1");
        $roleName = '';
        if ($roleStmt) {
            mysqli_stmt_bind_param($roleStmt, 'i', $user['UsertypeID']);
            mysqli_stmt_execute($roleStmt);
            $roleResult = mysqli_stmt_get_result($roleStmt);
            if ($roleRow = mysqli_fetch_assoc($roleResult)) {
                $roleName = strtolower(trim($roleRow['Usertype']));
            }
            mysqli_stmt_close($roleStmt);
        }
        $_SESSION['user_role'] = $roleName;

        mysqli_stmt_close($stmt);

        $roleRedirects = [
            'admin' => 'dashboard.php',
            'registrar' => 'subjects.php',
            'dean' => 'subjects.php',
            'program head' => 'subjects.php',
            'instructor' => 'subjects.php',
            'professor' => 'subjects.php',
            'student' => 'subjects.php'
        ];
        $redirect = $roleRedirects[$roleName] ?? 'subjects.php';
        header("Location: " . $redirect);
        exit();
    }

    mysqli_stmt_close($stmt);
    header('Location: login.php?error=invalid');
    exit();
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Welcome to School Portal</title>
</head>
<body>
<div class="login-container">
    <div class="login-box">
        <div class="login-icon"><i class="fa-solid fa-school"></i></div>
        <h1>WELCOME TO SCHOOL PORTAL</h1>
        <p>Please log in to access your account.</p>
        <?php if ($error === 'invalid'): ?><div class="alert">Invalid username or password.</div><?php elseif ($error === 'empty'): ?><div class="alert">Please enter username and password.</div><?php endif; ?>
        <?php if (isset($_GET['registered'])): ?><div style="padding:12px;background:#dcfce7;color:#166534;border-radius:8px;margin-bottom:12px;">Account created. Please log in.</div><?php endif; ?>
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <div class="form-group"><label for="username">Username</label><div class="input-box"><i class="fa-solid fa-user"></i><input type="text" id="username" name="username" placeholder="Enter your username" required></div></div>
            <div class="form-group"><label for="password">Password</label><div class="input-box"><i class="fa-solid fa-lock"></i><input type="password" id="password" name="password" placeholder="Enter your password" required></div></div>
            <button type="submit"><i class="fa-solid fa-right-to-bracket"></i> Login</button>
        </form>
        <p style="margin-top:18px;">No account yet? <a href="signup.php">Sign up</a></p>
    </div>
</div>
</body>
</html>
