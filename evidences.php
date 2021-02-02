<?php
require_once "checkin.php";
if (isset($_GET['logout'])) {
    unset($_SESSION['login']);
    session_destroy();
    header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Evidences List</title>
    <!-- library css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

    <style>
        a {
            text-decoration: none !important;
            border: none !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <div class="row text-light" style="background: #1a6e50">
            <div class="col-12">
                <br>
                <span class="badge bg-dark fs-5 pull-right">Logged as: <?php echo $_SESSION['login'] ?> <a href="index.php?logout=1" class="btn btn-danger"> Logout</a></span><br><br><br>
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
                    <?php
                    if (isset($_GET['project_id'])) {
                        $project_id = trim($_GET['project_id']);
                        echo "Evidence List from Project " . $project_id;
                        echo "<a href='create_evidence.php?project_id=" . $project_id . "' class='btn btn-success pull-right'>Add New Evidence</a>";
                    } else {
                        echo "Evidence List";
                        echo "<a href='create_evidence.php' class='btn btn-success pull-right'>Add New Evidence</a>";
                    }
                    ?>
                </h3>
                <hr class="bg-dark">
                <?php
                // Include config file
                require_once "config.php";
                // Attempt select query execution
                $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * 10;
                if (isset($_GET['project_id'])) {
                    $sql = "SELECT * FROM evidences WHERE project_id=? LIMIT 10 OFFSET $offset";
                    $stmt = mysqli_prepare($connection,  $sql);
                    mysqli_stmt_bind_param($stmt, "i", $param_project_id);
                    $param_project_id = $project_id;
                    if (mysqli_stmt_execute($stmt)) {
                        $result = mysqli_stmt_get_result($stmt);
                        $numTotal   = mysqli_num_rows($result);
                    } else
                        echo "Oops! Something went wrong. Please try again later.";
                } else {
                    $sql = "SELECT * FROM evidences LIMIT 10 OFFSET $offset";
                    $result = mysqli_query($connection, $sql);
                    $numTotal   = mysqli_num_rows($result);
                    if (!$result)
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($connection);
                }
                if (mysqli_num_rows($result) > 0) {
                ?>
                    <table id="evidences" class="table">
                        <thead class="bg-primary table-dark border border-light">
                            <tr>
                                <th class="text-center">Details</th>
                                <th>ID</th>
                                <th>Project</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Log Date</th>
                                <th>Log Login</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="border border-light bg-light">
                            <?php

                            while ($row = mysqli_fetch_array($result)) {
                            ?>
                                <tr>
                                    <td class="text-center"><span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="bottom" title=<?= $row['description']; ?>>info</span></td>
                                    <td><?= $row['id']; ?></td>
                                    <td>Project <?= $row['project_id'] ?></td>
                                    <td><?php switch ($row['type']) {
                                            case 1:
                                                echo "Safety Management Plan";
                                                break;
                                            case 2:
                                                echo "Development Plan";
                                                break;
                                            case 3:
                                                echo "Configuration Management Plan";
                                                break;
                                            case 4:
                                                echo "V&V Plan";
                                                break;
                                            case 5:
                                                echo "System Testing Results";
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td><?= $row['name']; ?></td>
                                    <td><?php switch ($row['status']) {
                                            case 1:
                                                echo "<span class='badge bg-warning'>Pending analysis</span>";
                                                break;
                                            case 2:
                                                echo "<span class='badge bg-danger'>Cancelled</span>";
                                                break;
                                            case 3:
                                                echo "<span class='badge bg-success'>Validated</span>";
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td><?= $row['log_date']; ?></td>
                                    <td><?= $row['log_login']; ?></td>
                                    <td>
                                        <?php
                                        if (isset($_GET['project_id'])) {
                                            echo "<a href='#" . $row['id'] . "' title='Download Evidence' data-toggle='tooltip'><span class='material-icons text-primary' aria-hidden='true'>save_alt</span></a>";
                                            echo "<a href='update_evidence.php?id=" . $row['id'] . "&project_id=" . $project_id . "' title='Update Evidence' data-toggle='tooltip'> <span class='material-icons' aria-hidden='true' style='color:#3ca23c;'>create</span></a>";
                                            echo "<a href='delete_evidence.php?id=" . $row['id'] . "&project_id=" . $project_id . "' title='Delete Evidence' data-toggle='tooltip'> <span class='material-icons' aria-hidden='true' style='color:crimson;'>delete_sweep</span></a>";
                                        } else {
                                            echo "<a href='#" . $row['id'] . "' title='Download Evidence' data-toggle='tooltip'><span class='material-icons text-primary' aria-hidden='true'>save_alt</span></a>";
                                            echo "<a href='update_evidence.php?id=" . $row['id'] . "' title='Update Evidence' data-toggle='tooltip'> <span class='material-icons' aria-hidden='true' style='color:#3ca23c;'>create</span></a>";
                                            echo "<a href='delete_evidence.php?id=" . $row['id'] . "' title='Delete Evidence' data-toggle='tooltip'> <span class='material-icons' aria-hidden='true' style='color:crimson;'>delete_sweep</span></a>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                <?php
                    $totalPage = ceil($numTotal / 10); //O calculo do Total de página ser exibido
                    $prev  = (($page - 1) == 0) ? 1 : $page - 1;
                    $next = (($page + 1) >= $totalPage) ? $totalPage : $page + 1;
                    echo "<div class='text-center'>";
                    if (isset($_GET['project_id']))
                        echo "<a href=?project_id=" . $project_id . "&page=" . $prev . "><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>chevron_left</span></span></a> <span class='badge bg-success align-top fs-5'>$page</span> <a href=?project_id=" . $project_id . "&page=" . $next . "><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>chevron_right</span></span></a>";
                    else
                        echo "<a href=\"?page=$prev\"><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>chevron_left</span></span></a> <span class='badge bg-success align-top fs-5'>$page</span> <a href=\"?page=$next\"><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>chevron_right</span></span></a>";
                    echo "</div>";
                    // Free result set
                    mysqli_free_result($result);
                } 
                else {
                    echo "<p class='lead'><em>No evidences were found.</em></p>";
                }

                // Close connection
                mysqli_close($connection);
                ?>
            </div>
        </div>
    </div>

    <!-- library js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
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
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>

    <!-- internal script -->
    <script src="js/export.js"></script>
</body>

</html>