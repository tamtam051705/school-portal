<?php
require_once __DIR__ . "/auth.php";
include 'db_connect.php';


if (!isset($_GET['code'])) {

    header("Location: subjects.php");
    exit();

}

$code = $_GET['code'];


$sql = "SELECT * FROM tbl_subjects WHERE CODE='$code'";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    echo "Subject not found.";
    exit();

}

$row = mysqli_fetch_assoc($result);


if (isset($_POST['update_subject'])) {

    $new_code = $_POST['code'];
    $title = $_POST['title'];
    $lecture = $_POST['lecture'];
    $laboratory = $_POST['laboratory'];
    $credit = $_POST['credit'];
    $pre_requisite = $_POST['pre_requisite'];


    $sql = "UPDATE tbl_subject

            SET CODE='$new_code',
                TITLE='$title',
                LECTURE='$lecture',
                LABORATORY='$laboratory',
                CREDIT='$credit',
                PRE_REQUISITE='$pre_requisite'

            WHERE CODE='$code'";


    if (mysqli_query($conn, $sql)) {

        header("Location: subjects.php");
        exit();

    } else {

        echo "Error updating subject: " . mysqli_error($conn);

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Edit Subject</title>

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

            <a href="term.php">
                <i class="fa-solid fa-clock"></i> Term
            </a>

            <a href="yearlevel.php">
                <i class="fa-solid fa-hourglass-half"></i> Year Level
            </a>

            <a href="subjects.php" class="active">
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

                <h1>Edit Subject</h1>

                <p>Update subject information.</p>

            </div>

        </div>



        <div class="card">


            <div class="card-header">

                Edit Subject

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
                            value="<?= htmlspecialchars($row['CODE']); ?>"
                            required>

                    </div>



                    <div class="form-group">

                        <label>
                            Title <span>*</span>
                        </label>

                        <input
                            type="text"
                            name="title"
                            value="<?= htmlspecialchars($row['TITLE']); ?>"
                            required>

                    </div>



                    <div class="form-group">

                        <label>
                            Lecture
                        </label>

                        <input
                            type="number"
                            name="lecture"
                            value="<?= htmlspecialchars($row['LECTURE']); ?>"
                            min="0">

                    </div>



                    <div class="form-group">

                        <label>
                            Laboratory
                        </label>

                        <input
                            type="number"
                            name="laboratory"
                            value="<?= htmlspecialchars($row['LABORATORY']); ?>"
                            min="0">

                    </div>



                    <div class="form-group">

                        <label>
                            Credit
                        </label>

                        <input
                            type="number"
                            name="credit"
                            value="<?= htmlspecialchars($row['CREDIT']); ?>"
                            min="0">

                    </div>



                    <div class="form-group">

                        <label>
                            Pre-Requisite
                        </label>

                        <input
                            type="text"
                            name="pre_requisite"
                            value="<?= htmlspecialchars($row['PRE_REQUISITE']); ?>">

                    </div>



                    <button
                        type="submit"
                        name="update_subject"
                        class="add-btn">

                        Update Subject

                    </button>


                    <a href="subjects.php">

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

