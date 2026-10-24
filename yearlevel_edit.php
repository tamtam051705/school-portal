<?php
require_once __DIR__ . "/auth.php";
include 'db_connect.php';

if (!isset($_GET['yearlevel'])) {
    header("Location: yearlevel.php");
    exit();
}

$yearlevel = $_GET['yearlevel'];

$sql = "SELECT * FROM tbl_yearlevel WHERE YEARLEVEL='$yearlevel'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Year level record not found.";
    exit();
}

$row = mysqli_fetch_assoc($result);

if (isset($_POST['update_yearlevel'])) {

    $new_yearlevel = $_POST['yearlevel'];
    $description = $_POST['description'];

    $sql = "UPDATE tbl_yearlevel
            SET YEARLEVEL='$new_yearlevel',
                DESCRIPTION='$description'
            WHERE YEARLEVEL='$yearlevel'";

    if (mysqli_query($conn, $sql)) {

        header("Location: yearlevel.php");
        exit();

    } else {

        echo "Error updating record: " . mysqli_error($conn);

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Edit Year Level</title>

    <link rel="stylesheet" href="app.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
<?php include __DIR__ . "/session_banner.php"; ?>

<div class="wrapper">

    <aside class="sidebar">

        <div class="logo">

            <h2>YearLevel Portal</h2>

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

            <a href="section.php">
                <i class="fa-solid fa-users"></i>
                Section
            </a>

            <a href="term.php">
                <i class="fa-solid fa-clock"></i>
                Term
            </a>

            <a href="yearlevel.php" class="active">
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

                <h1>Edit Year Level</h1>

                <p>
                    Update the selected year level information.
                </p>

            </div>

        </div>


        <div class="content-grid">


            <div class="card">

                <div class="card-header">

                    Edit Year Level

                </div>


                <div class="card-body">

                    <form method="POST">


                        <div class="form-group">

                            <label>
                                Year Level <span>*</span>
                            </label>

                            <select name="yearlevel" required>

                                <option value="1ST"
                                    <?= ($row['YEARLEVEL'] == '1ST') ? 'selected' : ''; ?>>
                                    1ST
                                </option>

                                <option value="2ND"
                                    <?= ($row['YEARLEVEL'] == '2ND') ? 'selected' : ''; ?>>
                                    2ND
                                </option>

                                <option value="3RD"
                                    <?= ($row['YEARLEVEL'] == '3RD') ? 'selected' : ''; ?>>
                                    3RD
                                </option>

                                <option value="4TH"
                                    <?= ($row['YEARLEVEL'] == '4TH') ? 'selected' : ''; ?>>
                                    4TH
                                </option>

                            </select>

                        </div>


                        <div class="form-group">

                            <label>
                                Description <span>*</span>
                            </label>

                            <textarea
                                name="description"
                                placeholder="Full description..."
                                required><?= htmlspecialchars($row['DESCRIPTION']); ?></textarea>

                        </div>


                        <button
                            type="submit"
                            name="update_yearlevel"
                            class="add-btn">

                            <i class="fa-solid fa-pen-to-square"></i>

                            Update Year Level

                        </button>


                        <a href="yearlevel.php">

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

