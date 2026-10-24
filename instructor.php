<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/db_connect.php";

$uploadDir = __DIR__ . "/uploads/instructors/";
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

function saveInstructorPhoto($file, $oldPhoto = '') {
    global $uploadDir;
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) return $oldPhoto;
    if ($file['error'] !== UPLOAD_ERR_OK) return $oldPhoto;

    $allowed = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) return $oldPhoto;
    if ($file['size'] > 5 * 1024 * 1024) return $oldPhoto;

    $filename = uniqid('instructor_', true) . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        if ($oldPhoto !== '' && is_file($uploadDir . basename($oldPhoto))) {
            @unlink($uploadDir . basename($oldPhoto));
        }
        return $filename;
    }
    return $oldPhoto;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "SELECT PHOTO FROM tbl_instructors WHERE ID=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $old = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "DELETE FROM tbl_instructors WHERE ID=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($old && !empty($old['PHOTO']) && is_file($uploadDir . basename($old['PHOTO']))) {
        @unlink($uploadDir . basename($old['PHOTO']));
    }
    header("Location: instructor.php");
    exit();
}

if (isset($_POST['add_instructor'])) {
    $salutation = trim($_POST['salutation'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $middlename = trim($_POST['middlename'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $photo = saveInstructorPhoto($_FILES['photo'] ?? null);

    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_instructors (SALUTATION, FIRSTNAME, MIDDLENAME, LASTNAME, PHOTO) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $salutation, $firstname, $middlename, $lastname, $photo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: instructor.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM tbl_instructors ORDER BY ID ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Instructor | Academic Information System</title>
<link rel="stylesheet" href="app.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
.photo-thumb{width:46px;height:46px;object-fit:cover;border-radius:50%;border:1px solid #ddd}
.no-photo{font-size:12px;color:#888}
</style>
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
<a href="users.php"><i class="fa-solid fa-user"></i> Users</a>
<a href="instructor.php" class="active"><i class="fa-solid fa-chalkboard-teacher"></i> Instructors</a>
</nav>
<a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</aside>
<main class="main-content">
<div class="page-header"><div><h1>Instructors</h1><p>Manage instructor records.</p></div><div class="record-count"><?= mysqli_num_rows($result); ?> Records</div></div>
<div class="content-grid">
<div class="card">
<div class="card-header">Add New Instructor</div>
<div class="card-body">
<form method="POST" enctype="multipart/form-data">
<div class="form-group"><label>Salutation <span>*</span></label><select name="salutation" required><option value="">-- Select --</option><option>Mr.</option><option>Ms.</option><option>Mrs.</option><option>Dr.</option><option>Prof.</option></select></div>
<div class="form-group"><label>First Name <span>*</span></label><input type="text" name="firstname" required></div>
<div class="form-group"><label>Middle Name</label><input type="text" name="middlename"></div>
<div class="form-group"><label>Last Name <span>*</span></label><input type="text" name="lastname" required></div>
<div class="form-group"><label>Photo</label><input type="file" name="photo" accept=".jpg,.jpeg,.png,.gif,.webp"></div>
<button type="submit" name="add_instructor" class="add-btn"><i class="fa-solid fa-plus"></i> Add Instructor</button>
</form>
</div></div>
<div class="card table-card">
<div class="card-header">All Instructors</div>
<div class="search-bar"><form onsubmit="return filterTable(event);" class="search-form"><input type="text" id="tableSearch" placeholder="Search instructors..." autocomplete="off"><button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button><button type="button" class="clear-btn" onclick="clearSearch()"><i class="fa-solid fa-rotate-left"></i> Clear</button></form></div>
<div class="table-container"><table><thead><tr><th>#</th><th>ID</th><th>PHOTO</th><th>SALUTATION</th><th>FIRSTNAME</th><th>MIDDLENAME</th><th>LASTNAME</th><th>ACTIONS</th></tr></thead><tbody>
<?php $count=1; while($row=mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $count++; ?></td><td><?= (int)$row['ID']; ?></td>
<td>
<?php if(!empty($row['PHOTO'])): ?>
    <img class="photo-thumb" src="uploads/instructors/<?= htmlspecialchars(basename($row['PHOTO'])); ?>" alt="Instructor photo">
<?php else: ?>
    <div class="photo-thumb" style="display: flex; align-items: center; justify-content: center; background: #f0f2f5; color: #888;">
        <i class="fa-solid fa-user"></i>
    </div>
<?php endif; ?>
</td>
<td><?= htmlspecialchars($row['SALUTATION']); ?></td><td><?= htmlspecialchars($row['FIRSTNAME']); ?></td><td><?= htmlspecialchars($row['MIDDLENAME']); ?></td><td><?= htmlspecialchars($row['LASTNAME']); ?></td>
<td class="actions">
    <a href="instructors.php?id=<?= (int)$row['ID']; ?>"><button type="button" class="edit-btn"><i class="fa-solid fa-pen"></i> Edit</button></a>
    <a href="instructor.php?delete=<?= (int)$row['ID']; ?>" onclick="return confirm('Delete this instructor?');"><button type="button" class="delete-btn"><i class="fa-solid fa-trash"></i> Delete</button></a>
</td>
</tr>
<?php endwhile; ?>
</tbody></table></div></div></div>
</main></div>
<script>
function filterTable(e){if(e)e.preventDefault();const t=document.getElementById('tableSearch').value.toLowerCase().trim();document.querySelectorAll('.table-container tbody tr').forEach(r=>r.style.display=r.innerText.toLowerCase().includes(t)?'':'none');return false;}
function clearSearch(){document.getElementById('tableSearch').value='';filterTable();document.getElementById('tableSearch'].focus();}
</script>
</body></html>