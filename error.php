<?php
// Require checkin file
require_once "checkin.php"; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Error</title>
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
                    Error
                </h3>
                <hr class="bg-dark">
                <?php
                if (isset($_GET['number']) && !empty($_GET['number'])) {
                    switch ($_GET['number']) {
                        case 0: // Admin authentication error
                            echo "<p>Sorry, you're not a admin. Please <a href='index.php' class='alert-link'>go back</a>.</p>";
                            break;
                        case 1: // Admin and dev authentication error
                            echo "<p>Sorry, you're not a admin nor a developer. Please <a href='index.php' class='alert-link'>go back</a>.</p>";
                            break;
                        case 2: // File extension not permitted
                            echo "<p>Sorry, you've tried to attach a file that is not permitted. Please <a href='index.php' class='alert-link'>go back</a> and try again.</p>";
                            break;
                        case 3: // File size is too big
                            echo "<p>Sorry, you've tried to attach a file that has a size over the limit. The evidence was created but has no files attached. Please <a href='index.php' class='alert-link'>go back</a> and try again.</p>";
                            break;
                        case 4: // Insert/Update/Delete evidence error
                            echo "<p>Sorry, the evidence cannot be created/updated/deleted. Please <a href='index.php' class='alert-link'>go back</a> and contact an admin.</p>";
                            break;
                        case 5: // Project status is closed and a request to create/update a evidence for this project was made
                            echo "<p>Sorry, you've made an invalid request. The evidence cannot be created/updated since the project is closed. Please <a href='index.php' class='alert-link'>go back</a>.</p>";
                            break;
                        case 6: // URL doesn't contain valid parameters
                            echo "<p>Sorry, you've made an invalid request. Please <a href='index.php' class='alert-link'>go back</a> and try again, if it persists, contact an admin.</p>";
                            break;
                        case 7: // Inputing a empty file
                            echo "<p>Sorry, you've tried to attach a empty file. Please <a href='index.php' class='alert-link'>go back</a> and try again.</p>";
                            break;
                        case 8: // Insert/Delete file error
                            echo "<p>Sorry, the file cannot be uploaded/deleted. Please <a href='index.php' class='alert-link'>go back</a> and contact an admin.</p>";
                            break;
                        case 9: // Evidence status isn't 'pending analysis' and a request to update this evidence was made
                            echo "<p>Sorry, you've made an invalid request. The evidence cannot be updated. Please <a href='index.php' class='alert-link'>go back</a>.</p>";
                            break;
                        case 10: // Insert/Update/Delete project error
                            echo "<p>Sorry, the project cannot be created/updated/deleted. Please <a href='index.php' class='alert-link'>go back</a> and contact an admin.</p>";
                            break;
                        case 11: // Insert/Update/Delete user error
                            echo "<p>Sorry, the user cannot be created/updated/deleted. Please <a href='index.php' class='alert-link'>go back</a> and contact an admin.</p>";
                            break;
                        default: // Default error
                            echo "<p>Sorry, you've made an invalid request. Please <a href='index.php' class='alert-link'>go back</a> and try again.</p>";
                    }
                } else // Default error
                    echo "<p>Sorry, you've made an invalid request. Please <a href='index.php' class='alert-link'>go back</a> and try again.</p>";
                ?>
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