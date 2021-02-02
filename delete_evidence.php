<?php
require_once "checkin.php";
if(isset($_GET['logout'])){
    unset($_SESSION['login']);
    session_destroy();
    header('location:login.php');
}
?>

<?php
// process delete operation after confirmation
if(isset($_POST['id']) && !empty($_POST['id']))
{
    // include config connection db
    include_once 'config.php';

    // Prepare a delete statement
    $sql = "DELETE FROM evidences WHERE id =?";
    if($stmt = mysqli_prepare($connection,  $sql))
    {
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        // set parameters
        $param_id = trim($_POST['id']);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt))
        {
            //  Records delete successfully. Redirect to landing page
            if(isset($_GET['project_id']))
                header("location: evidences.php?project_id=" . $_GET['project_id']);
            else
                header("location: evidences.php");
            exit();
        }
        else
        {
            echo "Oops! Something went wrong. Please try again leter.";
        }
    }
    // close statement
    mysqli_stmt_close($stmt);

    // close connection
    mysqli_close($connection);
}  
    else
{
      // Check existence of id parameter
        if(empty(trim($_GET['id'])))
        {
            // URL doesn't contain id parameter. Redirect to error page
            header("location:error.php");
            exit();
        }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Evidence</title>
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
                    Delete Evidence <?= $_GET['id']; ?>
                </h3>
                <hr class="bg-dark">
                <?php
                    if(isset($_GET['project_id']))
                        echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "?project_id=" . $_GET['project_id'] . "' method='post'>";
                    else
                        echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='post'>";
                ?>
                        <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>">

                        <p>Are you sure you want to delete this evidence?</p>
                        <p>
                            <input type="submit" value="Yes" class="btn btn-danger">
                            <?php
                                if(isset($_GET['project_id']))
                                    echo "<a href='evidences.php?project_id=" . $_GET['project_id'] . "' class='btn btn-success'>No</a>";
                                else
                                    echo "<a href='evidences.php' class='btn btn-success'>No</a>";
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
    <script src="js/export.js"></script>

</body>
</html>