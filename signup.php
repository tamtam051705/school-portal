<?php
session_start();
require_once __DIR__ . '/db_connect.php';
if (!empty($_SESSION['user_id'])) { $r = strtolower(trim((string)($_SESSION['user_role'] ?? ''))); header('Location: ' . ($r === 'admin' ? 'dashboard.php' : 'subjects.php')); exit; }
$error = '';
$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$usertypeId = (int)($_POST['usertype_id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($fullname === '' || $username === '' || $password === '' || $confirm === '' || $usertypeId <= 0) {
        $error = 'Please complete all fields.';
    } elseif (mb_strlen($fullname) > 255 || mb_strlen($username) > 255) {
        $error = 'Name and username must be 255 characters or fewer.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $roleStmt = mysqli_prepare($conn, 'SELECT ID, Usertype FROM tbl_usertype WHERE ID = ? LIMIT 1');
        $type = null;
        if ($roleStmt) {
            mysqli_stmt_bind_param($roleStmt, 'i', $usertypeId);
            mysqli_stmt_execute($roleStmt);
            $roleResult = mysqli_stmt_get_result($roleStmt);
            $type = mysqli_fetch_assoc($roleResult);
            mysqli_stmt_close($roleStmt);
        }

        if (!$type) {
            $error = 'Please select a valid user type.';
        } else {
            $check = mysqli_prepare($conn, 'SELECT ID FROM tbl_users WHERE username = ? LIMIT 1');
            mysqli_stmt_bind_param($check, 's', $username);
            mysqli_stmt_execute($check);
            mysqli_stmt_store_result($check);
            $exists = mysqli_stmt_num_rows($check) > 0;
            mysqli_stmt_close($check);
            if ($exists) {
                $error = 'This username is already registered.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $typeId = (int)$type['ID'];
                $stmt = mysqli_prepare($conn, 'INSERT INTO tbl_users (username, password, Fullname, UsertypeID) VALUES (?, ?, ?, ?)');
                mysqli_stmt_bind_param($stmt, 'sssi', $username, $hash, $fullname, $typeId);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header('Location: login.php?registered=1');
                    exit;
                }
                $error = mysqli_stmt_errno($stmt) === 1062 ? 'This username is already registered.' : 'Registration failed. Please try again.';
                mysqli_stmt_close($stmt);
            }
        }
    }
}
$userTypes = [];
$typeResult = mysqli_query($conn, "SELECT ID, Usertype FROM tbl_usertype ORDER BY ID ASC");
if ($typeResult) {
    while ($row = mysqli_fetch_assoc($typeResult)) {
        $userTypes[] = $row;
    }
}
function h($value) { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up | School Portal</title>
<link rel="stylesheet" href="app.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="login-container"><div class="login-box">
<div class="login-icon"><i class="fa-solid fa-user-plus"></i></div>
<h1>CREATE AN ACCOUNT</h1><p>Register for the School Portal.</p>
<?php if ($error !== ''): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>
<form method="POST" action="signup.php">
<div class="form-group"><label for="fullname">Full Name</label><div class="input-box"><i class="fa-solid fa-id-card"></i><input id="fullname" name="fullname" type="text" maxlength="255" value="<?= h($fullname) ?>" required></div></div>
<div class="form-group"><label for="username">Username</label><div class="input-box"><i class="fa-solid fa-user"></i><input id="username" name="username" type="text" maxlength="255" value="<?= h($username) ?>" required></div></div>
<div class="form-group">
<label for="usertype_id">User Type</label>
<div class="input-box">
<i class="fa-solid fa-user-shield"></i>
<select id="usertype_id" name="usertype_id" required style="width:100%;border:0;outline:0;background:transparent;font:inherit;">
<option value="">-- Select User Type --</option>
<?php foreach ($userTypes as $typeOption): ?>
<option value="<?= (int)$typeOption['ID'] ?>" <?= $usertypeId === (int)$typeOption['ID'] ? 'selected' : '' ?>><?= h($typeOption['Usertype']) ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
<div class="form-group"><label for="password">Password (at least 8 characters)</label><div class="input-box"><i class="fa-solid fa-lock"></i><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required></div></div>
<div class="form-group"><label for="confirm_password">Confirm Password</label><div class="input-box"><i class="fa-solid fa-lock"></i><input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required></div></div>
<button type="submit"><i class="fa-solid fa-user-plus"></i> Sign Up</button>
</form><p style="margin-top:18px;">Already have an account? <a href="login.php">Log in</a></p>
</div></div>
</body></html>
