<?php
require_once __DIR__ . "/auth.php";

include 'db_connect.php';

function countTable($conn, $table) {
    $allowed = ['tbl_course','tbl_schoolyear','tbl_section','tbl_yearlevel','tbl_term','tbl_subjects','tbl_usertype','tbl_users'];
    if (!in_array($table, $allowed, true)) return 0;
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$table`");
    if (!$result) return 0;
    $row = mysqli_fetch_assoc($result);
    return (int)$row['total'];
}

$total_courses = countTable($conn, 'tbl_course');
$total_schoolyears = countTable($conn, 'tbl_schoolyear');
$total_sections = countTable($conn, 'tbl_section');
$total_yearlevels = countTable($conn, 'tbl_yearlevel');
$total_terms = countTable($conn, 'tbl_term');
$total_subjects = countTable($conn, 'tbl_subjects');
$total_usertypes = countTable($conn, 'tbl_usertype');
$total_users = countTable($conn, 'tbl_users');
$total_instructors = countTable($conn, 'tbl_users'); // Assuming instructors are 


$display_name = 'Admin';
if (!empty($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "SELECT Fullname, username FROM tbl_users WHERE ID = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) {
            $display_name = $row['Fullname'] !== '' ? $row['Fullname'] : $row['username'];
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | School Portal</title>
    <link rel="stylesheet" href="app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include __DIR__ . "/session_banner.php"; ?>
<div class="portal-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark"><i class="fa-solid fa-school"></i></div>
            <div><div class="brand-title">School Student</div><div class="brand-subtitle">Portal</div></div>
        </div>
        <nav class="menu">
            <a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i><span>Dashboard</span></a>
            <a href="course.php"><i class="fa-solid fa-graduation-cap"></i><span>Courses</span></a>
            <a href="schoolyear.php"><i class="fa-solid fa-calendar-days"></i><span>School Year</span></a>
            <a href="section.php"><i class="fa-solid fa-users"></i><span>Section</span></a>
            <a href="term.php"><i class="fa-regular fa-clock"></i><span>Term</span></a>
            <a href="yearlevel.php"><i class="fa-solid fa-layer-group"></i><span>Year Level</span></a>
            <a href="subjects.php"><i class="fa-solid fa-book-open"></i><span>Subjects</span></a>
            <a href="usertype.php"><i class="fa-solid fa-user-shield"></i><span>User Type</span></a>
            <a href="users.php"><i class="fa-solid fa-user"></i><span>Users</span></a>
            <a href="instructors.php"><i class="fa-solid fa-chalkboard-teacher"></i><span>Instructors</span></a>
        </nav>
        <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </aside>

    <main class="main-content portal-main">
        <header class="portal-header">
            <div><h1>Good day, <?= htmlspecialchars($display_name); ?>!</h1><p>Here's your school portal overview.</p></div>
            <div class="admin-chip"><div class="admin-avatar" style="overflow:hidden;"><?php if (!empty($_SESSION['user_image']) && is_file(__DIR__ . '/' . $_SESSION['user_image'])): ?><img src="<?= htmlspecialchars($_SESSION['user_image']); ?>" alt="Profile image" style="width:30px;height:30px;object-fit:cover;"><?php else: ?><i class="fa-solid fa-user"></i><?php endif; ?></div><span><?= htmlspecialchars($display_name); ?></span></div>
        </header>

        <section class="stats-grid dashboard-stats-extended">
            <a href="course.php" class="stat-card"><div class="stat-icon"><i class="fa-solid fa-graduation-cap"></i></div><div><strong><?= $total_courses; ?></strong><span>Courses</span></div></a>
            <a href="schoolyear.php" class="stat-card"><div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div><div><strong><?= $total_schoolyears; ?></strong><span>School Years</span></div></a>
            <a href="section.php" class="stat-card"><div class="stat-icon"><i class="fa-solid fa-users"></i></div><div><strong><?= $total_sections; ?></strong><span>Sections</span></div></a>
            <a href="yearlevel.php" class="stat-card"><div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div><div><strong><?= $total_yearlevels; ?></strong><span>Year Levels</span></div></a>
            <a href="usertype.php" class="stat-card"><div class="stat-icon"><i class="fa-solid fa-user-shield"></i></div><div><strong><?= $total_usertypes; ?></strong><span>User Types</span></div></a>
            <a href="users.php" class="stat-card"><div class="stat-icon"><i class="fa-solid fa-user"></i></div><div><strong><?= $total_users; ?></strong><span>Users</span></div></a>
        </section>

        <section class="dashboard-panels">
            <div class="panel quick-panel">
                <div class="panel-title">QUICK ACTIONS</div>
                <div class="quick-actions">
                    <a href="course.php" class="quick-action"><span class="action-plus">+</span><span>Add Course</span><i class="fa-solid fa-chevron-right"></i></a>
                    <a href="subjects.php" class="quick-action"><span class="action-plus">+</span><span>Add Subject</span><i class="fa-solid fa-chevron-right"></i></a>
                    <a href="schoolyear.php" class="quick-action"><span class="action-plus">+</span><span>Add School Year</span><i class="fa-solid fa-chevron-right"></i></a>
                    <a href="users_edit.php" class="quick-action"><span class="action-plus">+</span><span>Add User</span><i class="fa-solid fa-chevron-right"></i></a>
                </div>
            </div>
            <div class="panel system-panel">
                <div class="panel-title">SYSTEM OVERVIEW</div>
                <div class="overview-list">
                    <div><span>Subjects</span><strong><?= $total_subjects; ?></strong></div>
                    <div><span>Terms</span><strong><?= $total_terms; ?></strong></div>
                    <div><span>User Types</span><strong><?= $total_usertypes; ?></strong></div>
                    <div><span>Users</span><strong><?= $total_users; ?></strong></div>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>
