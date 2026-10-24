<?php
require_once __DIR__ . "/auth.php";
include 'db_connect.php';
$isStudent = ($session_role === 'student');

if (!$isStudent && isset($_POST['add_subject'])) {

    $code = $_POST['code'];
    $title = $_POST['title'];
    $lecture = $_POST['lecture'];
    $laboratory = $_POST['laboratory'];
    $credit = $_POST['credit'];
    $pre_requisite = $_POST['pre_requisite'];

    $sql = "INSERT INTO tbl_subjects
            (CODE, TITLE, LECTURE, LABORATORY, CREDIT, PRE_REQUISITE)
            VALUES
            ('$code', '$title', '$lecture', '$laboratory', '$credit', '$pre_requisite')";

    mysqli_query($conn, $sql);

    header("Location: subjects.php");
    exit();
}

if (!$isStudent && isset($_GET['delete'])) {

    $code = $_GET['delete'];

    $sql = "DELETE FROM tbl_subject WHERE CODE='$code'";

    mysqli_query($conn, $sql);

    header("Location: subjects.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM tbl_subjects");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Subjects</title>

    <link rel="stylesheet" href="app.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
<?php include __DIR__ . "/session_banner.php"; ?>

<div class="wrapper">

    <aside class="sidebar">

        <div class="logo">
            <h2>Subject Portal</h2>
            <p>Enrollment Management System</p>
        </div>

        <nav class="menu">

            <a href="dashboard.php">
                <i class="fa-solid fa-chart-column"></i> Dashboard
            </a>

            <a href="course.php">
                <i class="fa-solid fa-graduation-cap"></i> Courses
            </a>

            <a href="schoolyear.php">
                <i class="fa-solid fa-calendar"></i> School Year
            </a>

            <a href="section.php">
                <i class="fa-solid fa-users"></i> Section
            </a>

            <a href="term.php">
                <i class="fa-solid fa-clock"></i> Term
            </a>

            <a href="yearlevel.php">
                <i class="fa-solid fa-hourglass-half"></i> Year Level
            </a>

            <a href="subjects.php" class="active">
                <i class="fa-solid fa-book"></i> Subjects
            </a>

            <a href="usertype.php">
                <i class="fa-solid fa-user-shield"></i> User Type
            </a>
            <a href="users.php"><i class="fa-solid fa-user"></i> Users</a>
            <a href="instructors.php">
                <i class="fa-solid fa-chalkboard-teacher"></i> Instructors
            </a>    

        </nav>

        <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>


    </aside>

    <main class="main-content">

        <div class="page-header">

            <div>
                <h1>Subjects</h1>
                <p>Manage all academic subjects.</p>
            </div>

        </div>


        <div class="subjects-layout">

            <?php if (!$isStudent): ?>
            <div class="card">

                <div class="card-header">
                    Add New Subject
                </div>

                <div class="card-body">

                    <form method="POST">

                        <div class="form-group">

                            <label>
                                Code <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="code"
                                placeholder="Subject Code"
                                required>

                        </div>


                        <div class="form-group">

                            <label>
                                Title <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="title"
                                placeholder="Subject Title"
                                required>

                        </div>


                        <div class="form-group">

                            <label>Lecture</label>

                            <input
                                type="number"
                                name="lecture"
                                placeholder="Lecture Units"
                                min="0">

                        </div>


                        <div class="form-group">

                            <label>Laboratory</label>

                            <input
                                type="number"
                                name="laboratory"
                                placeholder="Laboratory Units"
                                min="0">

                        </div>


                        <div class="form-group">

                            <label>Credit</label>

                            <input
                                type="number"
                                name="credit"
                                placeholder="Credit Units"
                                min="0">

                        </div>


                        <div class="form-group">

                            <label>Pre-Requisite</label>

                            <input
                                type="text"
                                name="pre_requisite"
                                placeholder="Pre-Requisite">

                        </div>


                        <button
                            type="submit"
                            name="add_subject"
                            class="add-btn">

                            Add Subject

                        </button>

                    </form>

                </div>

            </div>
            <?php endif; ?>

            <div class="card table-card">

                <div class="card-header">
                    All Subjects
                </div>

                
        <div class="search-bar">
            <form onsubmit="return filterTable(event);" class="search-form">
                <input type="text" id="tableSearch" placeholder="Search records..." autocomplete="off">
                <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                <button type="button" class="clear-btn" onclick="clearSearch()"><i class="fa-solid fa-rotate-left"></i> Clear</button>
            </form>
        </div>
<div class="table-container">

                    <table>

                        <thead>

                            <tr>

                                <th>#</th>
                                <th>CODE</th>
                                <th>TITLE</th>
                                <th>LECTURE</th>
                                <th>LAB</th>
                                <th>CREDIT</th>
                                <th>PRE-REQUISITE</th>
                                <th>ACTIONS</th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php

                        $count = 1;

                        while ($row = mysqli_fetch_assoc($result)) {

                        ?>

                            <tr>

                                <td>
                                    <?= $count++; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['CODE']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['TITLE']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['LECTURE']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['LABORATORY']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['CREDIT']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['PRE_REQUISITE']); ?>
                                </td>

                                <?php if (!$isStudent): ?>
                                <td class="actions">

                                    <a href="subjects_edit.php?code=<?= urlencode($row['CODE']); ?>">

                                        <button type="button" class="edit-btn">Edit</button>

                                    </a>

                                    <a href="subjects.php?delete=<?= urlencode($row['CODE']); ?>"
                                       onclick="return confirm('Are you sure you want to delete this subject?');">

                                        <button type="button" class="delete-btn">Del</button>

                                    </a>

                                </td>
                                <?php else: ?>
                                <td><span class="no-photo">View only</span></td>
                                <?php endif; ?>

                            </tr>

                        <?php

                        }

                        ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </main>

</div>


<script>
function filterTable(event) {
    if (event) event.preventDefault();
    const term = document.getElementById('tableSearch').value.toLowerCase().trim();
    const table = document.querySelector('.table-container table');
    if (!table) return false;
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
    });
    return false;
}
function clearSearch() {
    const input = document.getElementById('tableSearch');
    input.value = '';
    filterTable();
    input.focus();
}
</script>

</body>
</html>

