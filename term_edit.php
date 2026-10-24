<?php
require_once __DIR__ . "/auth.php";
include 'db_connect.php';


if (!isset($_GET['term'])) {

    header("Location: term.php");
    exit();

}

$term = $_GET['term'];


$sql = "SELECT * FROM tbl_term WHERE TERM='$term'";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    echo "Term not found.";
    exit();

}

$row = mysqli_fetch_assoc($result);

if (isset($_POST['update_term'])) {

    $new_term = $_POST['term'];
    $description = $_POST['description'];


    $sql = "UPDATE tbl_term

            SET TERM='$new_term',
                DESCRIPTION='$description'

            WHERE TERM='$term'";


    if (mysqli_query($conn, $sql)) {

        header("Location: term.php");
        exit();

    } else {

        echo "Error updating term: " . mysqli_error($conn);

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Edit Term</title>

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

            <p>Academic Information System</p>

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

            <a href="term.php" class="active">
                <i class="fa-solid fa-clock"></i> Term
            </a>

            <a href="yearlevel.php">
                <i class="fa-solid fa-hourglass-half"></i> Year Level
            </a>

            <a href="subjects.php">
                <i class="fa-solid fa-book"></i> Subjects
            </a>

        </nav>


        <div class="sidebar-footer">
            BSIS 4A
        </div>

    </aside>


    <main class="main-content">


        <div class="page-header">

            <div>

                <h1>Edit Term</h1>

                <p>Update term information.</p>

            </div>

        </div>



        <div class="card">


            <div class="card-header">

                Edit Term

            </div>


            <div class="card-body">


                <form method="POST">


                    <div class="form-group">

                        <label>
                            Term <span>*</span>
                        </label>

                        <input
                            type="text"
                            name="term"
                            value="<?= htmlspecialchars($row['TERM']); ?>"
                            required>

                    </div>



                    <div class="form-group">

                        <label>
                            Description
                        </label>

                        <input
                            type="text"
                            name="description"
                            value="<?= htmlspecialchars($row['DESCRIPTION']); ?>">

                    </div>



                    <button
                        type="submit"
                        name="update_term"
                        class="add-btn">

                        Update Term

                    </button>



                    <a href="term.php">

                        <button
                            type="button"
                            class="delete-btn">

                            Cancel

                        </button>

                    </a>


                </form>


            </div>

        </div>


    </main>

</div>

</body>

</html>

