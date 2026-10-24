<?php
require_once __DIR__ . "/auth.php";
include 'db_connect.php';

if (!isset($_GET['schoolyear'])) {

    header("Location: schoolyear.php");
    exit();

}

$schoolyear = $_GET['schoolyear'];

$sql = "SELECT * FROM tbl_schoolyear
        WHERE SCHOOLYEAR='$schoolyear'";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    echo "School year record not found.";
    exit();

}

$row = mysqli_fetch_assoc($result);

if (isset($_POST['update_schoolyear'])) {

    $new_schoolyear = $_POST['schoolyear'];
    $description = $_POST['description'];


    $sql = "UPDATE tbl_schoolyear

            SET SCHOOLYEAR='$new_schoolyear',
                DESCRIPTION='$description'

            WHERE SCHOOLYEAR='$schoolyear'";


    if (mysqli_query($conn, $sql)) {

        header("Location: schoolyear.php");
        exit();

    } else {

        echo "Error updating record: "
             . mysqli_error($conn);

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Edit School Year</title>

    <link rel="stylesheet" href="app.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>


<body>
<?php include __DIR__ . "/session_banner.php"; ?>

<div class="wrapper">

<aside class="sidebar">

    <div class="logo">

        <h2>School Year Portal</h2>

        <p>Academic Information System</p>

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

        <a href="schoolyear.php" class="active">
            <i class="fa-solid fa-calendar"></i>
            School Year
        </a>

        <a href="section.php">
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

    </nav>


    <div class="sidebar-footer">

        BSIS 4A

    </div>

</aside>


<main class="main-content">


<div class="page-header">

    <div>

        <h1>Edit School Year</h1>

        <p>
            Update the selected school year information.
        </p>

    </div>

</div>



<div class="content-grid">


<div class="card">


    <div class="card-header">

        Edit School Year

    </div>


    <div class="card-body">


        <form method="POST">


            <div class="form-group">

                <label>

                    School Year <span>*</span>

                </label>


                <input

                    type="text"

                    name="schoolyear"

                    value="<?= htmlspecialchars($row['SCHOOLYEAR']); ?>"

                    placeholder="Example: 2025-2026"

                    required>

            </div>


            <div class="form-group">

                <label>

                    Description <span>*</span>

                </label>


                <textarea

                    name="description"

                    placeholder="School year description..."

                    required><?= htmlspecialchars($row['DESCRIPTION']); ?></textarea>

            </div>

            <button

                type="submit"

                name="update_schoolyear"

                class="add-btn">

                <i class="fa-solid fa-pen-to-square"></i>

                Update School Year

            </button>


            <a href="schoolyear.php">

                <button

                    type="button"

                    class="delete-btn">

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancel

                </button>

            </a>


        </form>


    </div>

</div>


</div>

</main>

</div>

</body>

</html>

=======
<?php
include 'db_connect.php';

if (!isset($_GET['schoolyear'])) {

    header("Location: schoolyear.php");
    exit();

}

$schoolyear = $_GET['schoolyear'];

$sql = "SELECT * FROM tbl_schoolyear
        WHERE SCHOOLYEAR='$schoolyear'";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    echo "School year record not found.";
    exit();

}

$row = mysqli_fetch_assoc($result);

if (isset($_POST['update_schoolyear'])) {

    $new_schoolyear = $_POST['schoolyear'];
    $description = $_POST['description'];


    $sql = "UPDATE tbl_schoolyear

            SET SCHOOLYEAR='$new_schoolyear',
                DESCRIPTION='$description'

            WHERE SCHOOLYEAR='$schoolyear'";


    if (mysqli_query($conn, $sql)) {

        header("Location: schoolyear.php");
        exit();

    } else {

        echo "Error updating record: "
             . mysqli_error($conn);

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Edit School Year</title>

    <link rel="stylesheet" href="app.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>


<body>

<div class="wrapper">

<aside class="sidebar">

    <div class="logo">

        <h2>School Year Portal</h2>

        <p>Academic Information System</p>

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

        <a href="schoolyear.php" class="active">
            <i class="fa-solid fa-calendar"></i>
            School Year
        </a>

        <a href="section.php">
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

    </nav>


    <div class="sidebar-footer">

        BSIS 4A

    </div>

</aside>


<main class="main-content">


<div class="page-header">

    <div>

        <h1>Edit School Year</h1>

        <p>
            Update the selected school year information.
        </p>

    </div>

</div>



<div class="content-grid">


<div class="card">


    <div class="card-header">

        Edit School Year

    </div>


    <div class="card-body">


        <form method="POST">


            <div class="form-group">

                <label>

                    School Year <span>*</span>

                </label>


                <input

                    type="text"

                    name="schoolyear"

                    value="<?= htmlspecialchars($row['SCHOOLYEAR']); ?>"

                    placeholder="Example: 2025-2026"

                    required>

            </div>


            <div class="form-group">

                <label>

                    Description <span>*</span>

                </label>


                <textarea

                    name="description"

                    placeholder="School year description..."

                    required><?= htmlspecialchars($row['DESCRIPTION']); ?></textarea>

            </div>

            <button

                type="submit"

                name="update_schoolyear"

                class="add-btn">

                <i class="fa-solid fa-pen-to-square"></i>

                Update School Year

            </button>


            <a href="schoolyear.php">

                <button

                    type="button"

                    class="delete-btn">

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancel

                </button>

            </a>


        </form>


    </div>

</div>


</div>

</main>

</div>

</body>

</html>