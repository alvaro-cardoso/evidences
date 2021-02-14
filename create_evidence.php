<?php
// Include config, checkin and authorization file
require_once "checkin.php";
require "auth_admin_dev.php";
if (!isset($_GET['project_id']))
    require "auth_admin.php";
require_once "config.php";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);

    $description = trim($_POST["description"]);

    $type = trim($_POST["type"]);

    $status = trim($_POST["status"]);

    if (isset($_GET['project_id']))
        $project_id = trim($_GET["project_id"]);
    else
        $project_id = trim($_POST["project_id"]);

    $log_date = date("Y-m-d H:i:s");

    // Prepare an insert statement
    $sql = "INSERT INTO evidences (project_id, name, description, type, status, log_date, log_login) VALUES (?,?,?,?,?,?,?)";

    if ($stmt = mysqli_prepare($connection, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "issiiss", $project_id, $name, $description, $type, $status, $log_date, $log_login);

        // Set parameters
        $project_id = $project_id;
        $name         = $name;
        $description   = $description;
        $type           = $type;
        $status        = $status;
        $log_date      = $log_date;
        $log_login         = $_SESSION['login'];
        echo $stmt->sqlstate;
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $evidence_id =  mysqli_insert_id($connection); // Get insert ID
            $i = 0;
            foreach ($_FILES['file']['name'] as $filename) { // Insert the attach files in the BD
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $file = $_FILES['file']['tmp_name'][$i];
                $size = $_FILES['file']['size'][$i];

                $permitted = array("pdf", "xlsx", "docx");

                $i++;

                if (!in_array($extension, $permitted)) { // Check the extension file
                    header("location: error.php?number=2");
                    exit();
                } elseif ($size > 1024**3) { // Check the file size
                    header("location: error.php?number=3");
                    exit();
                } else {
                    // Read the file
                    $fp = fopen($file, "r");
                    $content = fread($fp, $size);
                    fclose($fp);
                    // Prepare to insert
                    $sql = "INSERT INTO files (evidence_id,name,content,size) VALUES (?,?,?,?)";
                    $stmt = mysqli_prepare($connection, $sql);
                    // Bind the parameters
                    $null = NULL;
                    mysqli_stmt_bind_param($stmt, "isbi", $evidence_id, $filename, $null, $size);
                    // Bind blob, to insert the content file
                    $stmt->send_long_data(2, $content);
                    if (!mysqli_stmt_execute($stmt)) {
                        header("location: error.php?number=4");
                        exit();
                    }
                }
            }
            //Evidence created successfully. Redirect to landing page
            if (isset($_GET['project_id']))
                header("location: evidences.php?project_id=" . $project_id . "&project_page=" . $_GET['project_page']);
            else
                header("location: evidences.php");
            exit();
        } else {
            header("location: error.php?number=4");
            exit();
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta title="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel='shortcut icon' href='GG_Management_Solutions_Icon.png'>
    <title>Create Evidence</title>
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
                    <?php
                    // Verify url content
                    if (isset($_GET['project_id'])) {
                        if (!empty(trim($_GET["project_id"])) && !empty(trim($_GET["project_page"])) && isset($_GET["project_page"])) {
                            $project_page = ($_GET['project_page']);
                            $project_id = ($_GET['project_id']);

                            // Check project status
                            $sql = "SELECT * FROM projects WHERE id = ?";
                            $stmt = mysqli_prepare($connection, $sql);
                            mysqli_stmt_bind_param($stmt, "i", $project_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $row = mysqli_fetch_array($result);
                            if ($row['status'] == 3) { // Evidence cannot be created since project is closed
                                header("location: error.php?number=5");
                                exit();
                            }
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

                    if (isset($_GET['project_id']))
                        echo "Register Evidence for Project " . $project_id;
                    else
                        echo "Register Evidence";
                    ?>
                </h3>
                <hr class="bg-dark">
                <?php
                if (isset($_GET['project_id']))
                    echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "?project_id=" . $project_id . "&project_page=" . $project_page . "' method='post' enctype='multipart/form-data'>";
                else
                    echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='post' enctype='multipart/form-data'>";
                ?>
                <div class="form-group">
                    <?php
                    if (!isset($_GET['project_id'])) {
                        echo "<label>Project</label><select id='project_id' name='project_id' class='form-control'>";
                        // Show the projects that are not closed
                        $query = "SELECT * FROM projects";
                        $result = mysqli_query($connection, $query);
                        while ($row = mysqli_fetch_array($result)) :
                            if ($row['status'] != 3)
                                echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
                        endwhile;
                        echo "</select>";
                    }
                    // Close connection
                    mysqli_close($connection);
                    ?>
                </div>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control"  required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control"  required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select id="type" name="type" class="form-control" >
                        <option value="1">Safety Management Plan</option>
                        <option value="2">Development Plan</option>
                        <option value="3">Configuration Management Plan</option>
                        <option value="4">V&V Plan</option>
                        <option value="5">System Testing Results</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status<div class="text-danger">(Note: If Cancelled or Validated is selected, the evidence cannot be editted)</div></label>
                    <select id="status" name="status" class="form-control" value="<?= $status; ?>">
                        <option value="1">Pending analisys</option>
                        <option value="2">Cancelled</option>
                        <option value="3">Validated</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Upload evidence file (.pdf, .xlsx, .docx)</label>
                    <input class="form-control" type="file" name="file[]" accept=".docx,.pdf,.xlsx" multiple>
                </div>
                <br>
                <input type="submit" class="btn btn-success" value="Submit">
                <?php
                if (isset($_GET['project_id']))
                    echo "<a href='evidences.php?project_id=" . $project_id . "&project_page=" . $project_page . "' class='btn btn-default' style='color:crimson'>Cancel</a>";
                else
                    echo "<a href='evidences.php' class='btn btn-default' style='color:crimson'>Cancel</a>";
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