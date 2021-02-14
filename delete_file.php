<?php
// Include checkin, config and authorization file
require_once "checkin.php";
require "auth_admin_dev.php";
include_once 'config.php';
if(!isset($_GET['project_id']))
    require "auth_admin.php";

// Processing form data when form is submitted
if (isset($_POST['id']) && !empty($_POST['id'])) {
    $file_id = $_POST['id'];

    // Prepare a delete statement
    $sql = "DELETE FROM files WHERE id =?";
    if ($stmt = mysqli_prepare($connection,  $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $file_id);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $evidence_id = trim($_POST["evidence_id"]);
            $evidence_page = trim($_POST["evidence_page"]);
            //  File deleted successfully. Redirect to landing page
            if (isset($_POST['project_id'])){
                $project_id = trim($_POST["project_id"]);
                $project_page = trim($_POST["project_page"]);
                header("location: files_evidence.php?evidence_id=" . $evidence_id . "&project_id=" . $project_id . "&project_page=" . $project_page . "&evidence_page=" . $evidence_page);
            }
            else
                header("location: files_evidence.php?evidence_id=" . $evidence_id . "&evidence_page=" . $evidence_page);
            exit();
        } else {
            header("location:error.php?number=8");
            exit();
        }
    }
    // close statement
    mysqli_stmt_close($stmt);

    // close connection
    mysqli_close($connection);
} else {
    // Check existence of id parameter
    if (empty(trim($_GET['id'])) || !isset($_GET['id'])) {
        // URL doesn't contain id parameter. Redirect to error page
        header("location:error.php?number=6");
        exit();
    }
    else{
        $file_id = $_GET['id'];
    }
}

// Check existence of parameters in URL
if (isset($_GET["project_id"])) {
    if (!empty(trim($_GET["project_id"])) && !empty(trim($_GET["project_page"])) && isset($_GET["project_page"])) {
        $project_id = trim($_GET['project_id']);
        $project_page = trim($_GET['project_page']);
    } else {
        // URL doesn't contain valid id. Redirect to error page
        header("location: error.php?number=6");
        exit();
    }
}
if (isset($_GET["project_page"])){
    if(!isset($_GET["project_id"])){
        // URL doesn't contain valid id. Redirect to error page
        header("location: error.php?number=6");
        exit();
    }
}
if (isset($_GET["evidence_page"]) && !empty($_GET["evidence_page"]))
    $evidence_page = $_GET["evidence_page"];
else{
    // URL doesn't contain valid id. Redirect to error page
    header("location: error.php?number=6");
    exit();
}

if (isset($_GET["evidence_id"]) && !empty($_GET["evidence_id"]))
    $evidence_id = $_GET["evidence_id"];
else {
    // URL doesn't contain valid id. Redirect to error page
    header("location: error.php?number=6");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delete Evidence</title>
    <!-- library css -->
    <link rel='shortcut icon' href='GG_Management_Solutions_Icon.png'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <style>
        a {
            text-decoration: none !important;
            border: none !important;
        }

        .dropdown-menu>li>a:hover {
            background-color: rgba(0, 0, 0, 0.8) !important;
        }

        #exit:hover {
            background-color: crimson !important;
            opacity: 0.8;
        }

        .dropdown>a:hover {
            background-color: rgba(0, 0, 0, 0.8) !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <div class="row flex-grow-1">
            <div class="col bg-dark text-light" style="border-right:solid #24242c; border-right-width:10px">
                <br>
                <div class="dropdown">
                    <a class="btn bg-dark text-light dropdown" role="button" id="dropdownList" text-center style="font-size: 22px" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class='material-icons float-start' style='font-size: 30px' aria-hidden='true'>account_circle</span>&nbsp;<?php echo $_SESSION['login'] ?></a>
                    <ul class="dropdown-menu bg-dark text-light" style="border:none !important; box-shadow: none;" aria-labelledby="dropdownList">
                        <li><a class="dropdown-item bg-dark text-light" href="index.php"><span class='material-icons float-start' aria-hidden='true'>assignment</span>&nbsp;Projects</a></a></li>
                        <?php if($_SESSION['user_profile'] == 1){ ?>
                            <li><a class="dropdown-item bg-dark text-light" href="evidences.php"><span class='material-icons float-start' aria-hidden='true'>folder</span>&nbsp;Evidences</a></a></li>
                            <li><a class="dropdown-item bg-dark text-light" href="users.php"><span class='material-icons float-start' aria-hidden='true'>folder_shared</span>&nbsp;Users</a></li>
                        <?php } ?>
                        <li><a id ="exit" class="dropdown-item bg-dark text-light" href="index.php?logout=1"><span class='material-icons float-start' aria-hidden='true'>power_settings_new</span>&nbsp;Logout</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-10 bg-light">
                <br>
                <h3 class="titulo-tabla">
                    Delete File <?= $_GET['id']; ?>
                </h3>
                <hr class="bg-dark">
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="id" value="<?= $file_id ?>">
                <input type="hidden" name="evidence_id" value="<?= $evidence_id; ?>" />
                <input type="hidden" name="evidence_page" value="<?= $evidence_page; ?>" />
                <?php
                    if(isset($_GET['project_id'])){
                        echo "<input type='hidden' name='project_id' value=" . $project_id . " />";
                        echo "<input type='hidden' name='project_page' value=" . $project_page . " />";
                    }
                ?>

                <p>Are you sure you want to delete this file?</p>
                <p>
                    <input type="submit" value="Yes" class="btn btn-danger">
                    <?php
                    if (isset($_GET['project_id']))
                        echo "<a href='files_evidence.php?evidence_id=" . $evidence_id . "&project_id=" . $project_id . "&project_page=" . $project_page . "&evidence_page=" . $evidence_page . "' class='btn btn-success'>No</a>";
                    else
                        echo "<a href='files_evidence.php?evidence_id=" . $evidence_id . "&evidence_page=" . $evidence_page . "' class='btn btn-success'>No</a>";
                    ?>
                </p>
                </form>
            </div>
        </div>
    </div>

    <!-- library js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.colVis.min.js"></script>


    <!-- internal script -->
    <script src="javascript.js"></script>

</body>

</html>