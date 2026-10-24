<?php
require_once __DIR__ . "/auth.php";
include 'db_connect.php';

if (isset($_POST['add_usertype'])) {
    $usertype = trim($_POST['usertype']);
    if ($usertype !== '') {
        $stmt = mysqli_prepare($conn, "INSERT INTO tbl_usertype (Usertype) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $usertype);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: usertype.php"); exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM tbl_usertype WHERE ID=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: usertype.php"); exit();
}

$result = mysqli_query($conn, "SELECT * FROM tbl_usertype ORDER BY ID ASC");
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Type | Academic Information System</title>
<link rel="stylesheet" href="app.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head><body>
<?php include __DIR__ . "/session_banner.php"; ?><div class="wrapper">
<aside class="sidebar"><div class="logo"><h2>MSTIP Academic</h2><p>Enrollment Management System</p></div>
<nav class="menu">
<a href="dashboard.php"><i class="fa-solid fa-chart-column"></i> Dashboard</a>
<a href="course.php"><i class="fa-solid fa-graduation-cap"></i> Courses</a>
<a href="schoolyear.php"><i class="fa-solid fa-calendar"></i> School Year</a>
<a href="section.php"><i class="fa-solid fa-users"></i> Section</a>
<a href="term.php"><i class="fa-solid fa-clock"></i> Term</a>
<a href="yearlevel.php"><i class="fa-solid fa-hourglass-half"></i> Year Level</a>
<a href="subjects.php"><i class="fa-solid fa-book"></i> Subjects</a>
<a href="usertype.php" class="active"><i class="fa-solid fa-user-shield"></i> User Type</a>
<a href="users.php"><i class="fa-solid fa-user"></i> Users</a>
<a href="instructors.php"><i class="fa-solid fa-chalkboard-teacher"></i> Instructors</a>
</nav>
<a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</aside>
<main class="main-content">
<div class="page-header"><div><h1>User Types</h1><p>Manage system user types.</p></div><div class="record-count"><?= mysqli_num_rows($result); ?> Records</div></div>
<div class="content-grid">
<div class="card"><div class="card-header">Add New User Type</div><div class="card-body"><form method="POST">
<div class="form-group"><label>User Type <span>*</span></label><select name="usertype" required><option value="">-- Select User Type --</option><option>Admin</option><option>Registrar</option><option>Dean</option><option>Instructor</option><option>Student</option></select></div>
<button type="submit" name="add_usertype" class="add-btn"><i class="fa-solid fa-plus"></i> Add User Type</button>
</form></div></div>
<div class="card table-card"><div class="card-header">All User Types</div>
<div class="search-bar"><form onsubmit="return filterTable(event);" class="search-form"><input type="text" id="tableSearch" placeholder="Search user type..." autocomplete="off"><button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button><button type="button" class="clear-btn" onclick="clearSearch()"><i class="fa-solid fa-rotate-left"></i> Clear</button></form></div>
<div class="table-container"><table><thead><tr><th>#</th><th>ID</th><th>USERTYPE</th><th>ACTIONS</th></tr></thead><tbody>
<?php while($row=mysqli_fetch_assoc($result)): ?><tr><td><?= htmlspecialchars($row['ID']); ?></td><td><?= htmlspecialchars($row['ID']); ?></td><td><?= htmlspecialchars($row['Usertype']); ?></td><td class="actions"><a href="usertype_edit.php?id=<?= (int)$row['ID']; ?>"><button type="button" class="edit-btn"><i class="fa-solid fa-pen"></i> Edit</button></a><a href="usertype.php?delete=<?= (int)$row['ID']; ?>" onclick="return confirm('Are you sure you want to delete this user type?');"><button type="button" class="delete-btn"><i class="fa-solid fa-trash"></i> Delete</button></a></td></tr><?php endwhile; ?>
</tbody></table></div></div></div></main></div>
<script>function filterTable(e){if(e)e.preventDefault();const t=document.getElementById('tableSearch').value.toLowerCase().trim();document.querySelectorAll('.table-container tbody tr').forEach(r=>r.style.display=r.innerText.toLowerCase().includes(t)?'':'none');return false;}function clearSearch(){document.getElementById('tableSearch').value='';filterTable();document.getElementById('tableSearch').focus();}</script>
</body></html>
