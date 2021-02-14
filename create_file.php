<?php
// Include checkin, config and authorization file
require_once "checkin.php";
require "auth_admin_dev.php";
require_once "config.php";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_FILES['file']['size'] == 0) { // Empty file
        header("location: error.php?number=7");
        exit();
    }

    $evidence_id = $_POST['evidence_id'];

    // Get file info
    $filename = $_FILES['file']['name'];
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $file = $_FILES['file']['tmp_name'];
    $size = $_FILES['file']['size'];

    // Permitted file extensions
    $permitted = array("pdf", "xlsx", "docx");

    if (!in_array($extension, $permitted)) { // Check the extension file
        header("location: error.php?number=2");
        exit();
    } elseif ($size > 1024**3) { // Check the file size
        header("location: error.php?number=3");
        exit();
    } else {
        // Read the file
        $fp = fopen($file,"r");
        $content = fread($fp,$size);
        fclose($fp);
        // Prepare to insert
        $sql = "INSERT INTO files (evidence_id,name,content,size) VALUES (?,?,?,?)";
        $stmt = mysqli_prepare($connection,$sql);
        // Bind the parameters
        $null = NULL;
        mysqli_stmt_bind_param($stmt, "isbi", $evidence_id, $filename, $null, $size);
        // Bind blob, to insert the content file
        $stmt->send_long_data(2,$content);
        if (mysqli_stmt_execute($stmt)) {
            if (isset($_POST['project_id']))
                header("location: files_evidence.php?evidence_id=" . $_POST['evidence_id'] . "&project_id=" . $_POST['project_id'] . "&project_page=" . $_POST['project_page'] . "&evidence_page=" . $_POST['evidence_page']);
            else
                header("location: files_evidence.php?evidence_id=" . $_POST['evidence_id'] . "&evidence_page=" . $_POST['evidence_page']);
            exit();
        } else {
            header("location: error.php?number=8");
            exit();
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($connection);

} else {
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
    if (isset($_GET["project_page"])) {
        if (!isset($_GET["project_id"])) {
            // URL doesn't contain valid id. Redirect to error page
            header("location: error.php?number=6");
            exit();
        }
    }

    if (isset($_GET["evidence_page"]) && !empty($_GET["evidence_page"]))
        $evidence_page = $_GET["evidence_page"];
    else {
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

    // Check evidence status
    $sql = "SELECT * FROM evidences WHERE id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_GET['evidence_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result);
    if ($row['status'] != 1) { // Evidence cannot be edited
        header("location: error.php?number=9");
        exit();
    }

    //Check project status
    $sql = "SELECT status FROM projects WHERE id=?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $row['project_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result);
    $project_status = $row['status'];
    if ($row['status'] == 3) { // Evidence cannot be edited since project status is closed
        header("location: error.php?number=5");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta title="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Upload File</title>
    <link rel='shortcut icon' href='GG_Management_Solutions_Icon.png'>
    <!-- library css -->
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
                        <?php if ($_SESSION['user_profile'] == 1) { ?>
                            <li><a class="dropdown-item bg-dark text-light" href="evidences.php"><span class='material-icons float-start' aria-hidden='true'>folder</span>&nbsp;Evidences</a></a></li>
                            <li><a class="dropdown-item bg-dark text-light" href="users.php"><span class='material-icons float-start' aria-hidden='true'>folder_shared</span>&nbsp;Users</a></li>
                        <?php } ?>
                        <li><a id="exit" class="dropdown-item bg-dark text-light" href="index.php?logout=1"><span class='material-icons float-start' aria-hidden='true'>power_settings_new</span>&nbsp;Logout</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-10 bg-light">
                <br>
                <h3 class="titulo-tabla">
                    Register File for Evidence <?= $evidence_id ?>
                </h3>
                <hr class="bg-dark">
                <form action='<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>' method='post' enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Upload file (.pdf, .xlsx, .docx)</label>
                        <input class="form-control" type="file" name="file" accept=".docx,.pdf,.xlsx">
                    </div>
                    <input type="hidden" name="evidence_id" value="<?= $evidence_id; ?>">
                    <input type="hidden" name="evidence_page" value="<?= $evidence_page; ?>">
                    <?php
                    if (isset($_GET['project_id'])) {
                        echo "<input type='hidden' name='project_id' value=" . $project_id . ">";
                        echo "<input type='hidden' name='project_page' value=" . $project_page . ">";
                    }
                    ?>
                    <br>
                    <input type="submit" class="btn btn-success" value="Submit">
                    <?php
                    if (isset($_GET['project_id']))
                        echo "<a href='files_evidence.php?evidence_id=" . $evidence_id . "&evidence_page=" . $evidence_page . "&project_id=" . $project_id . "&project_page=" . $project_page . "' class='btn btn-default' style='color:crimson'>Cancel</a>";
                    else
                        echo "<a href='files_evidence.php?evidence_id=" . $evidence_id . "&evidence_page=" . $evidence_page . "' class='btn btn-default' style='color:crimson'>Cancel</a>";
                    ?>
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