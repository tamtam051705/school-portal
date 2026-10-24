<?php
require_once __DIR__ . "/auth.php";
include 'db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = $id > 0;
$error = '';

$user = [
    'ID' => 0,
    'username' => '',
    'password' => '',
    'Fullname' => '',
    'UsertypeID' => '',
    'ImagePath' => ''
];

if ($is_edit) {
    $stmt = mysqli_prepare($conn, "SELECT ID, username, password, Fullname, UsertypeID, ImagePath FROM tbl_users WHERE ID = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $found = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$found) {
        header("Location: users.php");
        exit();
    }
    $user = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $is_edit = $id > 0;
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullname = trim($_POST['Fullname'] ?? '');
    $usertype_id = (int)($_POST['UsertypeID'] ?? 0);
    $imagePath = trim($_POST['existing_image'] ?? '');
    $targetDir = __DIR__ . '/images/users/';
    if (!is_dir($targetDir)) { mkdir($targetDir, 0755, true); }
    $uploadError = '';
    if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['imageUpload']['error'] !== UPLOAD_ERR_OK) { $uploadError = 'Image upload failed. Please try again.'; }
        else {
            $allowed = ['jpg'=>'image/jpeg', 'jpeg'=>'image/jpeg', 'png'=>'image/png', 'gif'=>'image/gif'];
            $ext = strtolower(pathinfo($_FILES['imageUpload']['name'], PATHINFO_EXTENSION));
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['imageUpload']['tmp_name']);
            if (!isset($allowed[$ext]) || $allowed[$ext] !== $mime) { $uploadError = 'Invalid image type. Please upload JPG, JPEG, PNG, or GIF.'; }
            elseif ($_FILES['imageUpload']['size'] > 5 * 1024 * 1024) { $uploadError = 'Image must be 5 MB or smaller.'; }
            else {
                $safeUsername = preg_replace('/[^A-Za-z0-9._-]/', '_', $username);
                $targetFile = $targetDir . $safeUsername . '.JPG';
                if (move_uploaded_file($_FILES['imageUpload']['tmp_name'], $targetFile)) { $imagePath = 'images/users/' . $safeUsername . '.JPG'; }
                else { $uploadError = 'Sorry, there was an error saving the image.'; }
            }
        }
    }


    $user = [
        'ID' => $id,
        'username' => $username,
        'password' => '',
        'Fullname' => $fullname,
        'UsertypeID' => $usertype_id,
        'ImagePath' => $imagePath
    ];

    if ($uploadError !== '') {
        $error = $uploadError;
    } elseif ($username === '' || $fullname === '' || $usertype_id <= 0) {
        $error = 'Please complete all required fields.';
    } elseif (!$is_edit && $password === '') {
        $error = 'Password is required when creating a user.';
    } else {
        // Prevent duplicate usernames, except for the current record while editing.
        if ($is_edit) {
            $check = mysqli_prepare($conn, "SELECT ID FROM tbl_users WHERE username = ? AND ID <> ? LIMIT 1");
            mysqli_stmt_bind_param($check, "si", $username, $id);
        } else {
            $check = mysqli_prepare($conn, "SELECT ID FROM tbl_users WHERE username = ? LIMIT 1");
            mysqli_stmt_bind_param($check, "s", $username);
        }
        mysqli_stmt_execute($check);
        $check_result = mysqli_stmt_get_result($check);
        $duplicate = mysqli_num_rows($check_result) > 0;
        mysqli_stmt_close($check);

        if ($duplicate) {
            $error = 'That username is already in use.';
        } elseif ($is_edit) {
            if ($password !== '') {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "UPDATE tbl_users SET username = ?, password = ?, Fullname = ?, UsertypeID = ?, ImagePath = ? WHERE ID = ?");
                mysqli_stmt_bind_param($stmt, "sssisi", $username, $password_hash, $fullname, $usertype_id, $imagePath, $id);
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE tbl_users SET username = ?, Fullname = ?, UsertypeID = ?, ImagePath = ? WHERE ID = ?");
                mysqli_stmt_bind_param($stmt, "ssisi", $username, $fullname, $usertype_id, $imagePath, $id);
            }

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: users.php");
                exit();
            }
            $error = 'Unable to update the user: ' . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO tbl_users (username, password, Fullname, UsertypeID, ImagePath) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssis", $username, $password_hash, $fullname, $usertype_id, $imagePath);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: users.php");
                exit();
            }
            $error = 'Unable to add the user: ' . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        }
    }
}

$types = mysqli_query($conn, "SELECT ID, Usertype FROM tbl_usertype ORDER BY Usertype ASC");
if (!$types) {
    die('Unable to load user types. Please run users_usertype.sql first.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? 'Edit User' : 'Add User'; ?> | Academic Information System</title>
    <link rel="stylesheet" href="app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include __DIR__ . "/session_banner.php"; ?>
<div class="wrapper">
    <aside class="sidebar">
        <div class="logo"><h2>MSTIP Academic</h2><p>Enrollment Management System</p></div>
        <nav class="menu">
            <a href="dashboard.php"><i class="fa-solid fa-chart-column"></i> Dashboard</a>
            <a href="course.php"><i class="fa-solid fa-graduation-cap"></i> Courses</a>
            <a href="schoolyear.php"><i class="fa-solid fa-calendar"></i> School Year</a>
            <a href="section.php"><i class="fa-solid fa-users"></i> Section</a>
            <a href="term.php"><i class="fa-solid fa-clock"></i> Term</a>
            <a href="yearlevel.php"><i class="fa-solid fa-hourglass-half"></i> Year Level</a>
            <a href="subjects.php"><i class="fa-solid fa-book"></i> Subjects</a>
            <a href="usertype.php"><i class="fa-solid fa-user-shield"></i> User Type</a>
            <a href="users.php" class="active"><i class="fa-solid fa-user"></i> Users</a>
        </nav>
        <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <div><h1><?= $is_edit ? 'Edit User' : 'Add User'; ?></h1><p><?= $is_edit ? 'Update the selected user account.' : 'Create a new system user account.'; ?></p></div>
        </div>

        <?php if ($error !== ''): ?><div class="alert"><?= htmlspecialchars($error); ?></div><?php endif; ?>

        <div class="content-grid" style="grid-template-columns:minmax(0,700px);">
            <div class="card">
                <div class="card-header"><?= $is_edit ? 'User Information' : 'New User'; ?></div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="id" value="<?= (int)$user['ID']; ?>">
                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($user['ImagePath'] ?? ''); ?>">

                        <div class="form-group">
                            <label for="username">Username <span>*</span></label>
                            <input type="text" id="username" name="username" maxlength="255" value="<?= htmlspecialchars($user['username']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password <?= $is_edit ? '<small>(leave blank to keep current password)</small>' : '<span>*</span>'; ?></label>
                            <input type="password" id="password" name="password" <?= $is_edit ? '' : 'required'; ?> autocomplete="new-password">
                        </div>

                        <div class="form-group">
                            <label for="Fullname">Full Name <span>*</span></label>
                            <input type="text" id="Fullname" name="Fullname" maxlength="255" value="<?= htmlspecialchars($user['Fullname']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="imageUpload">Profile Image <?= $is_edit ? '(optional)' : '<span>*</span>'; ?></label>
                            <?php if (!empty($user['ImagePath']) && is_file(__DIR__ . '/' . $user['ImagePath'])): ?>
                                <p><img src="<?= htmlspecialchars($user['ImagePath']); ?>" alt="Current profile image" style="width:80px;height:80px;object-fit:cover;border-radius:50%;"></p>
                            <?php endif; ?>
                            <input type="file" id="imageUpload" name="imageUpload" accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif" <?= $is_edit ? '' : 'required'; ?>>
                            <small>JPG, PNG, or GIF; maximum 5 MB. Saved as username.JPG.</small>
                        </div>

                        <div class="form-group">
                            <label for="UsertypeID">User Type <span>*</span></label>
                            <select id="UsertypeID" name="UsertypeID" required>
                                <option value="">-- Select User Type --</option>
                                <?php while ($type = mysqli_fetch_assoc($types)): ?>
                                    <option value="<?= (int)$type['ID']; ?>" <?= ((int)$user['UsertypeID'] === (int)$type['ID']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($type['Usertype']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div style="display:flex;gap:8px;">
                            <button type="submit" class="add-btn"><i class="fa-solid fa-floppy-disk"></i> <?= $is_edit ? 'Update User' : 'Save User'; ?></button>
                            <a href="users.php" class="clear-btn" style="text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
