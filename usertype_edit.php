<?php
require_once __DIR__ . "/auth.php";
include 'db_connect.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_usertype WHERE ID=?");
mysqli_stmt_bind_param($stmt, "i", $id); mysqli_stmt_execute($stmt);
$result=mysqli_stmt_get_result($stmt); $row=mysqli_fetch_assoc($result); mysqli_stmt_close($stmt);
if (!$row) { header("Location: usertype.php"); exit(); }
if (isset($_POST['update_usertype'])) {
    $usertype=trim($_POST['usertype']);
    $stmt=mysqli_prepare($conn,"UPDATE tbl_usertype SET Usertype=? WHERE ID=?");
    mysqli_stmt_bind_param($stmt,"si",$usertype,$id); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    header("Location: usertype.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Edit User Type</title><link rel="stylesheet" href="app.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></head><body>
<?php include __DIR__ . "/session_banner.php"; ?><div class="wrapper"><aside class="sidebar"><div class="logo"><h2>MSTIP Academic</h2><p>Enrollment Management System</p></div><nav class="menu"><a href="dashboard.php"><i class="fa-solid fa-chart-column"></i> Dashboard</a><a href="course.php"><i class="fa-solid fa-graduation-cap"></i> Courses</a><a href="schoolyear.php"><i class="fa-solid fa-calendar"></i> School Year</a><a href="section.php"><i class="fa-solid fa-users"></i> Section</a><a href="term.php"><i class="fa-solid fa-clock"></i> Term</a><a href="yearlevel.php"><i class="fa-solid fa-hourglass-half"></i> Year Level</a><a href="subjects.php"><i class="fa-solid fa-book"></i> Subjects</a><a href="usertype.php" class="active"><i class="fa-solid fa-user-shield"></i> User Type</a>
            <a href="users.php"><i class="fa-solid fa-user"></i> Users</a></nav><div class="sidebar-footer">BSIS 4A</div></aside><main class="main-content"><div class="page-header"><div><h1>Edit User Type</h1><p>Update the selected user type.</p></div></div><div class="content-grid"><div class="card"><div class="card-header">Edit User Type</div><div class="card-body"><form method="POST"><div class="form-group"><label>User Type <span>*</span></label><select name="usertype" required><?php foreach(['Admin','Registrar','Dean','Instructor','Student'] as $type): ?><option value="<?= htmlspecialchars($type); ?>" <?= $row['Usertype']===$type?'selected':''; ?>><?= htmlspecialchars($type); ?></option><?php endforeach; ?></select></div><button type="submit" name="update_usertype" class="add-btn"><i class="fa-solid fa-save"></i> Update User Type</button><a href="usertype.php"><button type="button" class="delete-btn">Cancel</button></a></form></div></div></div></main></div></body></html>
