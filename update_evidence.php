<?php
// Include config, checkin and authorization file
require_once "checkin.php";
require "auth_admin_dev.php";
if (!isset($_GET['project_id']))
    require "auth_admin.php";
require_once "config.php";

// Define variables and initialize with empty values
$name     = $description     = $status     = $type      = $project_id       = "";

// Processing form data when form is submitted
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $id = $_POST["id"];

    $log_date = date("Y-m-d H:i:s");

    // New Values

    $name = trim($_POST["name"]);

    $description = trim($_POST["description"]);

    $status = trim($_POST["status"]);

    $type = trim($_POST["type"]);


    // Old values

    $oldname = trim($_POST["oldname"]);

    $olddescription = trim($_POST["olddescription"]);

    $oldtype = trim($_POST["oldtype"]);

    $oldstatus = trim($_POST["oldstatus"]);

    // Prepare an update statement
    $sql = "UPDATE evidences SET type=?, name=?, description=?, status=?, log_date=?, log_login=? WHERE id=?";

    if ($stmt = mysqli_prepare($connection, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ississi", $type, $name, $description, $status, $log_date, $log_login, $param_id);

        // Set parameters
        $log_login     = $_SESSION['login'];
        $param_id = $id;
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $sql = "INSERT INTO logs (evidence_id,date,attribute,updates,login) VALUES (?,?,?,?,?)";
            if ($stmt = mysqli_prepare($connection, $sql)) {
                // Check name update
                if ($name != $oldname) {
                    $atribute = 'Name';
                    $updates = "From \"{$oldname}\" to \"{$name}\"";
                    mysqli_stmt_bind_param($stmt, "issss", $param_id, $log_date, $atribute, $updates, $log_login);
                    if (!mysqli_stmt_execute($stmt))
                        echo "Something went wrong. Please try again later.";  //tem que ver isso aqui
                }
                // Check description update
                if ($description != $olddescription) {
                    $atribute = 'Description';
                    $updates = "From \"{$olddescription}\" to \"{$description}\"";
                    mysqli_stmt_bind_param($stmt, "issss", $param_id, $log_date, $atribute, $updates, $log_login);
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "Something went wrong. Please try again later.";  //tem que ver isso aqui
                    }
                }
                // Check type update
                if ($type != $oldtype) {
                    $atribute = 'Type';
                    switch ($oldtype) {
                        case 1:
                            $oldtype = 'Safety Management Plan';
                            break;
                        case 2:
                            $oldtype = 'Development Plan';
                            break;
                        case 3:
                            $oldtype = 'Configuration Management Plan';
                            break;
                        case 4:
                            $oldtype = 'V&V Plan';
                            break;
                        case 5:
                            $oldtype = 'System Testing Results';
                            break;
                    }
                    switch ($type) {
                        case 1:
                            $type = 'Safety Management Plan';
                            break;
                        case 2:
                            $type = 'Development Plan';
                            break;
                        case 3:
                            $type = 'Configuration Management Plan';
                            break;
                        case 4:
                            $type = 'V&V Plan';
                            break;
                        case 5:
                            $type = 'System Testing Results';
                            break;
                    }
                    //Update query
                    $updates = "From \"{$oldtype}\" to \"{$type}\"";
                    mysqli_stmt_bind_param($stmt, "issss", $param_id, $log_date, $atribute, $updates, $log_login);
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "Something went wrong. Please try again later.";  //tem que ver isso aqui
                    }
                }
                // Check status update
                if ($status != $oldstatus) {
                    $atribute = 'Status';
                    switch ($oldstatus) {
                        case 1:
                            $oldstatus = 'Pending Analysis';
                            break;
                        case 2:
                            $oldstatus = 'Cancelled';
                            break;
                        case 3:
                            $oldstatus = 'Validated';
                            break;
                    }
                    switch ($status) {
                        case 1:
                            $status = 'In progress';
                            break;
                        case 2:
                            $status = 'Cancelled';
                            break;
                        case 3:
                            $status = 'Validated';
                            break;
                    }
                    //Update query
                    $updates = "From \"{$oldstatus}\" to \"{$status}\"";
                    mysqli_stmt_bind_param($stmt, "issss", $param_id, $log_date, $atribute, $updates, $log_login);
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "Something went wrong. Please try again later.";  //tem que ver isso aqui
                    }
                }
                $evidence_page = trim($_POST["evidence_page"]);
                // Evidence updated and log archived successfully. Redirect to landing page
                if (isset($_POST['project_id'])) {
                    $project_id = trim($_POST["project_id"]);
                    $project_page = trim($_POST["project_page"]);
                    header("location: evidences.php?project_id=" . $project_id . "&project_page=" . $project_page . "&page=" . $evidence_page);
                    exit();
                } else {
                    header("location: evidences.php?page=" . $evidence_page);
                    exit();
                }
            }
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($connection);
} else {
    // Check existence of id parameter before processing further
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Get URL parameter
        $id =  trim($_GET["id"]);

        if (!isset($_GET['evidence_page']) && empty($_GET['evidence_page'])) {
            header("location: error.php?number=6");
            exit();
        }

        // Prepare a select statement
        $sql = "SELECT * FROM evidences WHERE id = ?";
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
                    $row = mysqli_fetch_array($result);

                    // Retrieve individual field value
                    $oldname       = $row["name"];
                    $olddescription   = $row["description"];
                    $oldtype        = $row["type"];
                    $oldstatus     = $row["status"];
                    $log_date       = $row["log_date"];
                    $login = $row["log_login"];

                    if ($oldstatus != 1) {
                        header("location: error.php?number=9");
                        exit();
                    }

                    // Check project status
                    $sql = "SELECT status FROM projects WHERE id = ?";
                    $stmt = mysqli_prepare($connection, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $row['project_id']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $row = mysqli_fetch_assoc($result);
                    if ($row['status'] == 3){
                        header("location: error.php?number=5");
                        exit();
                    }
                } else {
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php?number=6");
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php?number=6");
        exit();
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
if (isset($_GET["project_page"])) {
    if (!isset($_GET["project_id"])) {
        // URL doesn't contain valid id. Redirect to error page
        header("location: error.phpnumber=6");
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Update Evidence</title>
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
                    <?php
                    if (isset($_GET['project_id']))
                        echo "Update Evidence " . $_GET['id'] . " from Project " . $_GET['project_id'];
                    else
                        echo "Update Evidence " . $_GET['id'];
                    ?>
                </h3>
                <hr class="bg-dark">
                <p>Please fill this form and submit to update the evidence on the database.</p>
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="<?= $oldname; ?>" required>
                    </div>
                    <input type="hidden" name="oldname" value="<?= $oldname; ?>" />
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" value="<?= $olddescription; ?>" required>
                    </div>
                    <input type="hidden" name="olddescription" value="<?= $olddescription; ?>" />
                    <div class="form-group">
                        <label>Type</label>
                        <select id="type" name="type" class="form-control">
                            <option value="1" <?php if ($oldtype == 1) echo "selected"; ?>>Safety Management Plan</option>
                            <option value="2" <?php if ($oldtype == 2) echo "selected"; ?>>Development Plan</option>
                            <option value="3" <?php if ($oldtype == 3) echo "selected"; ?>>Configuration Management Plan</option>
                            <option value="4" <?php if ($oldtype == 4) echo "selected"; ?>>V&V Plan</option>
                            <option value="5" <?php if ($oldtype == 5) echo "selected"; ?>>System Testing Results</option>
                        </select>
                    </div>
                    <input type="hidden" name="oldtype" value="<?= $oldtype; ?>" />
                    <div class="form-group">
                        <label>Status<div class="text-danger">(Note: If Cancelled or Validated is selected, the evidence cannot be editted)</div></label>
                        <select id="status" name="status" class="form-control">
                            <option value="1" <?php if ($oldstatus == 1) echo "selected"; ?>>Pending analysis</option>
                            <option value="2" <?php if ($oldstatus == 2) echo "selected"; ?>>Cancelled</option>
                            <option value="3" <?php if ($oldstatus == 3) echo "selected"; ?>>Validated</option>
                        </select>
                    </div>
                    <input type="hidden" name="oldstatus" value="<?= $oldstatus; ?>" />
                    <input type="hidden" name="id" value="<?= $id; ?>" />
                    <input type="hidden" name="evidence_page" value="<?= $evidence_page; ?>" />
                    <?php
                    // Useful parameters to the mysql query
                    if (isset($_GET['project_id'])) {
                        echo "<input type='hidden' name='project_id' value=" . $project_id . " />";
                        echo "<input type='hidden' name='project_page' value=" . $project_page . " />";
                    }
                    ?>
                    <br>
                    <input type="submit" class="btn btn-success" value="Submit">
                    <?php
                    if (isset($_GET['project_id']))
                        echo "<a href='evidences.php?project_id=" . $project_id . "&project_page=" . $project_page . "&page=" . $evidence_page . "' class='btn btn-danger'>Cancel</a>";
                    else
                        echo "<a href='evidences.php?page=" . $evidence_page . "' class='btn btn-danger'>Cancel</a>";
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