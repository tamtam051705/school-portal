<?php
require_once __DIR__ . "/auth.php";
include 'db_connect.php';



if (!isset($_GET['course'])) {

    header("Location: course.php");
    exit();

}

$course = $_GET['course'];


$sql = "SELECT * FROM tbl_course
        WHERE COURSE='$course'";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    echo "Course record not found.";
    exit();

}


$row = mysqli_fetch_assoc($result);


if (isset($_POST['update_course'])) {

    $new_course = $_POST['course'];

    $description = $_POST['description'];


    $sql = "UPDATE tbl_course

            SET COURSE='$new_course',
                DESCRIPTION='$description'

            WHERE COURSE='$course'";


    if (mysqli_query($conn, $sql)) {

        header("Location: course.php");
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

    <title>Edit Course</title>

    <link rel="stylesheet" href="app.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>


<body>
<?php include __DIR__ . "/session_banner.php"; ?>


<div class="wrapper">


<aside class="sidebar">


    <div class="logo">

        <h2>Course Portal</h2>

        <p>Academic Information System</p>

    </div>


    <nav class="menu">


        <a href="dashboard.php">

            <i class="fa-solid fa-chart-column"></i>

            Dashboard

        </a>


        <a href="course.php" class="active">

            <i class="fa-solid fa-graduation-cap"></i>

            Courses

        </a>


        <a href="schoolyear.php">

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


    </nav>


    <div class="sidebar-footer">

        BSIS 4A

    </div>


</aside>



<main class="main-content">


    <div class="page-header">

        <div>

            <h1>Edit Course</h1>

            <p>
                Update the selected course information.
            </p>

        </div>

    </div>



    <div class="content-grid">


        <div class="card">


            <div class="card-header">

                Edit Course

            </div>


            <div class="card-body">


                <form method="POST">



                    <div class="form-group">

                        <label>

                            Course Code <span>*</span>

                        </label>


                        <input

                            type="text"

                            name="course"

                            value="<?= htmlspecialchars($row['COURSE']); ?>"

                            required>

                    </div>



                    

                    <div class="form-group">

                        <label>

                            Description <span>*</span>

                        </label>


                        <textarea

                            name="description"

                            placeholder="Course description..."

                            required><?= htmlspecialchars($row['DESCRIPTION']); ?></textarea>

                    </div>



                   

                    <button

                        type="submit"

                        name="update_course"

                        class="add-btn">


                        <i class="fa-solid fa-pen-to-square"></i>

                        Update Course


                    </button>



                    

                    <a href="course.php">

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
