<?php
require_once __DIR__ . "/auth.php";
include 'db_connect.php';

/* =========================
   DELETE USER
========================= */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    if ($id > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_users WHERE ID = ?");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: users.php");
    exit();
}


/* =========================
   GET USERS + USER TYPE
========================= */
$sql = "SELECT
            u.ID,
            u.username,
            u.password,
            u.Fullname,
            u.UsertypeID,
            u.ImagePath,
            t.Usertype
        FROM tbl_users AS u
        LEFT JOIN tbl_usertype AS t
            ON u.UsertypeID = t.ID
        ORDER BY u.ID ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | Academic Information System</title>
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
            <a href="instructors.php"><i class="fa-solid fa-chalkboard-teacher"></i> Instructors</a>
        </nav>
        <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <div><h1>Users</h1><p>Manage system users and their user types.</p></div>
            <div style="display:flex;align-items:center;gap:10px;">
                <a href="users_edit.php" class="add-user-btn"><i class="fa-solid fa-plus"></i> Add User</a>
                <div class="record-count"><?= mysqli_num_rows($result); ?> Records</div>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-header">All Users</div>
            <div class="search-bar">
                <form onsubmit="return filterTable(event);" class="search-form">
                    <input type="text" id="tableSearch" placeholder="Search ID, username, fullname or user type..." autocomplete="off">
                    <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                    <button type="button" class="clear-btn" onclick="clearSearch()"><i class="fa-solid fa-rotate-left"></i> Clear</button>
                </form>
            </div>
            <div class="table-container">
                <table>
                    <thead><tr><th>#</th><th>ID</th><th>PHOTO</th><th>USERNAME</th><th>PASSWORD</th><th>FULLNAME</th><th>USER TYPE</th><th>ACTIONS</th></tr></thead>
                    <tbody>
                    <?php if (mysqli_num_rows($result) === 0): ?>
                        <tr><td colspan="7" class="empty-state">No users found. Click <strong>Add User</strong> to create one.</td></tr>
                    <?php else: $count = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $count++; ?></td>
                            <td class="user-id"><?= (int)$row['ID']; ?></td>
                            <td><?php if (!empty($row['ImagePath']) && is_file(__DIR__ . '/' . $row['ImagePath'])): ?><img src="<?= htmlspecialchars($row['ImagePath']); ?>" alt="Profile" style="width:42px;height:42px;object-fit:cover;border-radius:50%;"><?php else: ?>—<?php endif; ?></td>
                            <td class="username"><?= htmlspecialchars($row['username']); ?></td>
                            <td class="user-password">••••••••</td>
                            <td class="fullname"><?= htmlspecialchars($row['Fullname']); ?></td>
                            <td><?= htmlspecialchars($row['Usertype'] ?? 'No User Type'); ?></td>
                            <td class="actions">
                                <a href="users_edit.php?id=<?= (int)$row['ID']; ?>" class="edit-btn"><i class="fa-solid fa-pen"></i> Edit</a>
                                <a href="users.php?delete=<?= (int)$row['ID']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa-solid fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script>
function filterTable(event) {
    if (event) event.preventDefault();
    const term = document.getElementById('tableSearch').value.toLowerCase().trim();
    document.querySelectorAll('.table-container tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
    });
    return false;
}
function clearSearch() {
    document.getElementById('tableSearch').value = '';
    filterTable();
    document.getElementById('tableSearch').focus();
}
</script>
</body>
</html>
