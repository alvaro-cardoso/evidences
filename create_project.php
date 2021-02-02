<?php
require_once "checkin.php";
if(isset($_GET['logout'])){
    unset($_SESSION['login']);
    session_destroy();
    header('location:login.php');
}
?>

<?php

// Include config and checkin file
require_once "config.php";
 
// Define variables and initialize with empty values
$title     = $description     = $status     = "";
$title_err = $description_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Validate title
    $input_title = trim($_POST["title"]);
    if(empty($input_title))
    {
        $title_err = "Please enter a title.";
    }
    elseif(!filter_var($input_title, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/"))))
    {
        $title_err = "Please enter a valid title.";
    }
    else
    {
        $title = $input_title;
    }

    // Validate description
    $input_description = trim($_POST["description"]);
    if(empty($input_description))
    {
        $description_err = "Please enter a description.";
    }
    elseif(!filter_var($input_description, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/"))))
    {
        $description_err = "Please enter a valid description.";
    }
    else
    {
        $description = $input_description;
    }

    $status = trim($_POST["status"]);

    $log_date = date("Y-m-d H:i:s");;
    
    // Check input errors before inserting in database
    if(empty($title_err) && empty($description_err))
    {
        // Prepare an insert statement
        $sql = "INSERT INTO projects (title, description, status, log_date, log_login) VALUES (?,?,?,?,?)";
         
        if($stmt = mysqli_prepare($connection, $sql))
        {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssiss", $title, $description, $status, $log_date, $log_login);
            
            // Set parameters
            $title         = $title;
            $description   = $description;
            $status        = $status;
            $log_date      = $log_date;
            $log_login         = $_SESSION['login'];
            echo $stmt->sqlstate;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Projects created successfully. Redirect to landing pstatus
                header("location: index.php");
                exit();
            }
            else
            {
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta title="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Create Project</title>
    <!-- library css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">

</head>

<body>
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <div class="row text-light" style="background: #1a6e50">
            <div class="col-12">
                <br>
                <span class="badge bg-dark fs-5 pull-right">Logged as: <?php echo $_SESSION['login']?> <a href="index.php?logout=1" class="btn btn-danger"> Logout</a></span><br><br><br>
            </div>
        </div>
        <div class="row flex-grow-1">
            <div class="col bg-dark text-light" style="border-right:solid #1a6e50; border-right-width:10px">
            <br>
                <div class="dropdown">
                    <a class="btn text-light dropdown-toggle" style="background:#1a6e50" href="#" role="button" id="dropdownList" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class='material-icons float-start' aria-hidden='true'>view_list</span> Lists
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownList">
                        <li><a class="dropdown-item" href="index.php"><span class='material-icons float-start' aria-hidden='true'>assignment</span>Projects</a></a></li>
                        <li><a class="dropdown-item" href="evidences.php"><span class='material-icons float-start' aria-hidden='true'>folder</span>Evidences</a></a></li>
                        <li><a class="dropdown-item" href="users.php"><span class='material-icons float-start' aria-hidden='true'>account_circle</span>Users</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-10 bg-light">
                <br>
                <h3 class="titulo-tabla">
                    Register Project
                </h3>
                <hr class="bg-dark">
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group <?= (!empty($title_err)) ? 'has-error' : ''; ?>">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="<?= $title; ?>">
                        <span class="help-block"><?= $title_err;?></span>
                    </div>
                    <div class="form-group <?= (!empty($description_err)) ? 'has-error' : ''; ?>">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" value="<?= $description; ?>">
                        <span class="help-block"><?= $description_err;?></span>
                    </div>
                    <div class="form-group <?= (!empty($status_err)) ? 'has-error' : ''; ?>">
                        <label>Status</label>
                            <select id="status" name="status" class="form-control" value="<?= $status; ?>">
                            <option value="1">In progress</option>
                            <option value="2">In validation</option>
                            <option value="3">Closed</option>
                            </select>
                    </div>
                    <br>
                    <input type="submit" class="btn btn-success" value="Submit">
                    <a href="index.php" class="btn btn-default" style="color:crimson">Cancel</a>
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
    <script src="js/export.js"></script>
</body>
</html>
