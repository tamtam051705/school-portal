<?php
require_once __DIR__ . "/auth.php";
include 'db_connect.php';

if (isset($_POST['add_section'])) {

    $section = $_POST['section'];
    $description = $_POST['description'];

    $sql = "INSERT INTO tbl_section (SECTION, DESCRIPTION)
            VALUES ('$section', '$description')";

    mysqli_query($conn, $sql);

    header("Location: section.php");
    exit();
}

if (isset($_GET['delete'])) {

    $section = $_GET['delete'];

    $sql = "DELETE FROM tbl_section
            WHERE SECTION='$section'";

    mysqli_query($conn, $sql);

    header("Location: section.php");
    exit();
}


/* GET SECTIONS */
$result = mysqli_query(
    $conn,
    "SELECT * FROM tbl_section"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Section</title>

    <link rel="stylesheet" href="app.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>


<body>
<?php include __DIR__ . "/session_banner.php"; ?>

<div class="wrapper">


<aside class="sidebar">

    <div class="logo">

        <h2>Section Portal</h2>

        <p>Enrollment Management System</p>

    </div>


    <nav class="menu">

        <a href="dashboard.php">
            <i class="fa-solid fa-chart-column"></i>
            Dashboard
        </a>

        <a href="course.php">
            <i class="fa-solid fa-graduation-cap"></i>
            Courses
        </a>

        <a href="schoolyear.php">
            <i class="fa-solid fa-calendar"></i>
            School Year
        </a>

        <a href="section.php" class="active">
            <i class="fa-solid fa-users"></i>
            Section
        </a>

        <a href="term.php">
            <i class="fa-solid fa-clock"></i>
            Term
        </a>

        <a href="yearlevel.php">
            <i class="fa-solid fa-hourglass-half"></i>
            Year Level
        </a>

        <a href="subjects.php">
            <i class="fa-solid fa-book"></i>
            Subjects
        </a>

        <a href="usertype.php">
            <i class="fa-solid fa-user-shield"></i>
            User Type
        </a>
            <a href="users.php"><i class="fa-solid fa-user"></i> Users</a>

        <a href="instructors.php">
            <i class="fa-solid fa-chalkboard-teacher"></i>
            Instructors
        </a>

    </nav>

    <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>


</aside>

<main class="main-content">


<div class="page-header">

    <div>

        <h1>Sections</h1>

        <p>
            Manage available sections.
        </p>

    </div>


    <div class="record-count">

        <?= mysqli_num_rows($result); ?> Records

    </div>

</div>



<div class="content-grid">

<div class="card">


    <div class="card-header">

        Add New Section

    </div>


    <div class="card-body">


        <form method="POST">


            <div class="form-group">

                <label>

                    Section <span>*</span>

                </label>


                <input

                    type="text"

                    name="section"

                    placeholder="Example: A"

                    required>

            </div>

            <div class="form-group">

                <label>

                    Description <span>*</span>

                </label>


                <textarea

                    name="description"

                    placeholder="Section description..."

                    required></textarea>

            </div>


            <button

                type="submit"

                name="add_section"

                class="add-btn">

                Add Section

            </button>


        </form>


    </div>

</div>


<div class="card table-card">


    <div class="card-header">

        All Sections

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

                    <th>SECTION</th>

                    <th>DESCRIPTION</th>

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

                        <?= $count++ ?>

                    </td>


                    <td>

                        <span class="year-badge">

                            <?= htmlspecialchars($row['SECTION']); ?>

                        </span>

                    </td>


                    <td>

                        <?= htmlspecialchars($row['DESCRIPTION']); ?>

                    </td>


                    <td class="actions">


                        <a href="section_edit.php?section=<?= urlencode($row['SECTION']); ?>">

                            <button

                                type="button"

                                class="edit-btn">

                                Edit

                            </button>

                        </a>

                        <a

                            href="section.php?delete=<?= urlencode($row['SECTION']); ?>"

                            onclick="return confirm('Are you sure you want to delete this section?');">


                            <button

                                type="button"

                                class="delete-btn">

                                Del

                            </button>


                        </a>


                    </td>


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

