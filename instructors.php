<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/db_connect.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_instructors WHERE ID=? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$record = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);
if (!$record) { header("Location: instructor.php"); exit(); }

$uploadDir = __DIR__ . "/uploads/instructors/";
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);

if (isset($_POST['update_instructor'])) {
    $salutation = trim($_POST['salutation'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $middlename = trim($_POST['middlename'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $photo = $record['PHOTO'] ?? '';

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed, true) && $_FILES['photo']['size'] <= 5*1024*1024) {
            $photo = uniqid('instructor_', true) . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $photo)) {
                if (!empty($record['PHOTO']) && is_file($uploadDir . basename($record['PHOTO']))) @unlink($uploadDir . basename($record['PHOTO']));
            } else { $photo = $record['PHOTO']; }
        }
    }

    $stmt = mysqli_prepare($conn, "UPDATE tbl_instructors SET SALUTATION=?, FIRSTNAME=?, MIDDLENAME=?, LASTNAME=?, PHOTO=? WHERE ID=?");
    mysqli_stmt_bind_param($stmt, "sssssi", $salutation, $firstname, $middlename, $lastname, $photo, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: instructor.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Edit Instructor</title><link rel="stylesheet" href="app.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></head>
<body>
<?php include __DIR__ . "/session_banner.php"; ?>
<div class="wrapper"><aside class="sidebar"><div class="logo"><h2>MSTIP Academic</h2><p>Enrollment Management System</p></div><nav class="menu">
<a href="dashboard.php"><i class="fa-solid fa-chart-column"></i> Dashboard</a><a href="course.php"><i class="fa-solid fa-graduation-cap"></i> Courses</a><a href="schoolyear.php"><i class="fa-solid fa-calendar"></i> School Year</a><a href="section.php"><i class="fa-solid fa-users"></i> Section</a><a href="term.php"><i class="fa-solid fa-clock"></i> Term</a><a href="yearlevel.php"><i class="fa-solid fa-hourglass-half"></i> Year Level</a><a href="subjects.php"><i class="fa-solid fa-book"></i> Subjects</a><a href="instructor.php" class="active"><i class="fa-solid fa-chalkboard-user"></i> Instructor</a><a href="usertype.php"><i class="fa-solid fa-user-shield"></i> User Type</a><a href="users.php"><i class="fa-solid fa-user"></i> Users</a>
</nav></aside>
<main class="main-content"><div class="page-header"><div><h1>Edit Instructor</h1><p>Update instructor information.</p></div></div>
<div class="content-grid"><div class="card"><div class="card-header">Edit Instructor</div><div class="card-body"><form method="POST" enctype="multipart/form-data">
<div class="form-group"><label>Salutation <span>*</span></label><select name="salutation" required><?php foreach(['Mr.','Ms.','Mrs.','Dr.','Prof.'] as $v): ?><option <?= $record['SALUTATION']===$v?'selected':''; ?>><?= $v; ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label>First Name <span>*</span></label><input type="text" name="firstname" value="<?= htmlspecialchars($record['FIRSTNAME']); ?>" required></div>
<div class="form-group"><label>Middle Name</label><input type="text" name="middlename" value="<?= htmlspecialchars($record['MIDDLENAME']); ?>"></div>
<div class="form-group"><label>Last Name <span>*</span></label><input type="text" name="lastname" value="<?= htmlspecialchars($record['LASTNAME']); ?>" required></div>
<div class="form-group"><label>Photo</label><input type="file" name="photo" accept=".jpg,.jpeg,.png,.gif,.webp"></div>
<button type="submit" name="update_instructor" class="add-btn"><i class="fa-solid fa-save"></i> Update Instructor</button> <a href="instructor.php"><button type="button" class="delete-btn">Cancel</button></a>
</form></div></div></div></main></div>
</body></html>
