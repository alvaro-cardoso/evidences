<?php
// Include config, checkin and authorization file
require_once "checkin.php";
require "auth_admin.php";
require_once "config.php";
?>

<?php
// Define variables and initialize with empty values
$title     = $description     = $status     = "";

// Processing form data when form is submitted
if (isset($_POST["id"]) && !empty($_POST["id"]) && isset($_POST["page"]) && !empty($_POST["page"])) {
    // Get hidden input value
    $id = $_POST["id"];

    $log_date = date("Y-m-d H:i:s");

    // New values

    $title = trim($_POST["title"]);

    $description = trim($_POST["description"]);

    $status = trim($_POST["status"]);

    // Old values

    $oldtitle = trim($_POST["oldtitle"]);

    $olddescription = trim($_POST["olddescription"]);

    $oldstatus = trim($_POST["oldstatus"]);

    // Prepare an update statement
    $sql = "UPDATE projects SET title=?, description=?, status=?, log_date=?, log_login=? WHERE id=?";

    if ($stmt = mysqli_prepare($connection, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ssissi", $title, $description, $status, $log_date, $log_login, $param_id);

        // Set parameters
        $log_login     = $_SESSION['login'];
        $param_id = $id;
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $sql = "INSERT INTO logs (project_id,date,attribute,updates,login) VALUES (?,?,?,?,?)";
            if ($stmt = mysqli_prepare($connection, $sql)) {
                // Check title update
                if ($title != $oldtitle) {
                    $atribute = 'Name';
                    $updates = "From \"{$oldtitle}\" to \"{$title}\"";
                    mysqli_stmt_bind_param($stmt, "issss", $param_id, $log_date, $atribute, $updates, $log_login);
                    if (!mysqli_stmt_execute($stmt))
                        echo "Something went wrong. Please try again later.";
                }
                // Check description update
                if ($description != $olddescription) {
                    $atribute = 'Description';
                    $updates = "From \"{$olddescription}\" to \"{$description}\"";
                    mysqli_stmt_bind_param($stmt, "issss", $param_id, $log_date, $atribute, $updates, $log_login);
                    if (!mysqli_stmt_execute($stmt))
                        echo "Something went wrong. Please try again later.";
                }
                // Check status update
                if ($status != $oldstatus) {
                    $atribute = 'Status';
                    switch ($oldstatus) {
                        case 1:
                            $oldstatus = 'In progress';
                            break;
                        case 2:
                            $oldstatus = 'In validation';
                            break;
                        case 3:
                            $oldstatus = 'Closed';
                            break;
                    }
                    switch ($status) {
                        case 1:
                            $status = 'In progress';
                            break;
                        case 2:
                            $status = 'In validation';
                            break;
                        case 3:
                            $status = 'Closed';
                            break;
                    }
                    //Update query
                    $updates = "From \"{$oldstatus}\" to \"{$status}\"";
                    mysqli_stmt_bind_param($stmt, "issss", $param_id, $log_date, $atribute, $updates, $log_login);
                    if (!mysqli_stmt_execute($stmt))
                        echo "Something went wrong. Please try again later.";
                }
            }
            // Project updated and log archived successfully. Redirect to landing page
            header("location: index.php?page=" . $_POST['page']);
            exit();
        } else
            echo "Something went wrong. Please try again later.";
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($connection);
} else {
    // Check existence of id parameter before processing further
    if (isset($_GET["id"]) && !empty(trim($_GET["id"])) && isset($_GET["project_page"]) && !empty(trim($_GET["project_page"]))) {
        // Get URL parameter
        $id =  trim($_GET["id"]);
        $page = trim($_GET["project_page"]);

        // Prepare a select statement
        $sql = "SELECT * FROM projects WHERE id = ?";
        if ($stmt = mysqli_prepare($connection, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);

            // Set parameters
            $param_id = $id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    // Retrieve individual field value
                    $oldtitle       = $row["title"];
                    $olddescription   = $row["description"];
                    $oldstatus     = $row["status"];
                    $log_date       = $row["log_date"];
                    $login = $row["log_login"];
                } 
            }
            else {
                header("location: error.php?number=10");
                exit();
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);

        // Close connection
        mysqli_close($connection);
    } else {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php?number=6");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Update Project</title>
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
                        <?php if ($_SESSION['user_profile'] == 1) { // Options only available to admins ?>
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
                    Update Project
                </h3>
                <hr class="bg-dark">
                <p>Please fill this form and submit to update the project on the database.</p>
                <form action="<?= htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="<?= $oldtitle; ?>" required>
                    </div>
                    <input type="hidden" name="oldtitle" value="<?= $oldtitle; ?>" />
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" value="<?= $olddescription; ?>" required>
                    </div>
                    <input type="hidden" name="olddescription" value="<?= $olddescription; ?>" />
                    <div class="form-group">
                        <label>Status<div class="text-danger">(Note: If Closed is selected, no more evidences can be created or updated in this project)</div></label>
                        <select id="status" name="status" class="form-control">
                            <option value="1" <?php if ($oldstatus == 1) echo "selected"; ?>>In progress</option>
                            <option value="2" <?php if ($oldstatus == 2) echo "selected"; ?>>In validation</option>
                            <option value="3" <?php if ($oldstatus == 3) echo "selected"; ?>>Closed</option>
                        </select>
                    </div>
                    <input type="hidden" name="oldstatus" value="<?= $oldstatus; ?>" />
                    <input type="hidden" name="id" value="<?= $id; ?>" />
                    <input type="hidden" name="page" value="<?= $page; ?>" />
                    <br>
                    <input type="submit" class="btn btn-success" value="Submit">
                    <a href="index.php?page=<?= $page ?>" class="btn btn-danger">Cancel</a>
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