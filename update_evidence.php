<?php
require_once "checkin.php";
if(isset($_GET['logout'])){
    unset($_SESSION['login']);
    session_destroy();
    header('location:login.php');
}
?>

<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$name     = $description     = $status     = "";
$name_err = $description_err = "";

// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"]))
{
    // Get hidden input value
    $id = $_POST["id"];
    
   // Validate name
   $input_name = trim($_POST["name"]);
   if(empty($input_name))
   {
       $name_err = "Please enter a name.";
   }
   elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/"))))
   {
       $name_err = "Please enter a valid name.";
   }
   else
   {
       $name = $input_name;
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
    
    $project_id = trim($_POST["project_id"]);

    $log_date = date("Y-m-d H:i:s");
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($description_err))
    {
        // Prepare an update statement
        $sql = "UPDATE evidences SET project_id=?, name=?, description=?, status=?, log_date=?, log_login=? WHERE id=?";

        if($stmt = mysqli_prepare($connection, $sql))
        {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssi", $project_id, $name, $description, $status, $log_date, $log_login, $param_id);
            
            // Set parameters
            $project_id = $project_id;
            $name      = $name;
            $description   = $description;
            $status        = $status;
            $log_date   = $log_date;
            $log_login     =$_SESSION['login'];
            $param_id = $id;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt))
            {
                // Projects updated successfully. Redirect to landing page
                header("location: evidences.php");
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
else
{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"])))
    {
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM evidences WHERE id = ?";
        if($stmt = mysqli_prepare($connection, $sql))
        {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt))
            {
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1)
                {
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $project_id = $row["project_id"];
                    $name       = $row["name"];
                    $description   = $row["description"];
                    $status     = $row["status"];
                    $log_date       = $row["log_date"];
                    $login = $row["log_login"];

                }
                else
                {
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
            }
            else
            {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($connection);
    }
    else
    {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Update Evidence</title>
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
                        <li><a class="dropdown-item" href="#"><span class='material-icons float-start' aria-hidden='true'>account_circle</span>Users</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-10 bg-light">
                <br>
                <h3 class="titulo-tabla">
                    Update Evidence
                </h3>
                <hr class="bg-dark">
                <p>Please fill this form and submit to update the evidence on the database.</p>
                    <form action="<?= htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                    <div class="form-group">
                        <label>Project</label>
                            <select id="project_id" name="project_id" class="form-control" value="<?= $project_id; ?>">
                            <?php
                                $query = "SELECT * FROM projects";
                                $result = mysqli_query($connection, $query);
                                while ($row = mysqli_fetch_array($result)):;?>
                                <option value="<?= $row[0] ?>"><?= $row[0] ;?></option>
                            <?php endwhile;?>
                        </select>
                        </div>
                        <div class="form-group <?= (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?= $name; ?>">
                            <span class="help-block"><?= $name_err;?></span>
                        </div>
                        <div class="form-group <?= (!empty($description_err)) ? 'has-error' : ''; ?>">
                            <label>Description</label>
                            <input type="text" name="description" class="form-control" value="<?= $description; ?>">
                            <span class="help-block"><?= $description_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select id="status" name="status" class="form-control" value="<?= $status; ?>">
                                <option value="Pending analysis">Pending analysis</option>
                                <option value="Cancelled">Cancelled</option>
                                <option value="Validated">Validated</option>
                            </select>
                        </div>
                        <input type="hidden" name="id" value="<?= $id; ?>"/>
                        <br>
                        <input type="submit" class="btn btn-success" value="Submit">
                        <a href="evidences.php" class="btn btn-danger">Cancel</a>
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

