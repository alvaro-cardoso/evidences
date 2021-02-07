<?php
require_once "checkin.php";
if (isset($_GET['logout'])) {
    unset($_SESSION['login']);
    session_destroy();
    header('location:login.php');
}
?>

<?php 
    include 'Files_Logic.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel='shortcut icon' href='Imagens/GG_Management_Solutions_Icon.png'>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File List</title>
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
                    if (isset($_GET['evidence_id'])) {
                        $evidence_id = trim($_GET['evidence_id']);
                        echo "File List from Evidence " . $evidence_id;
                         
                    } else {
                        echo "File List Empty";
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
                if (isset($_GET['evidence_id'])) {
                    $sql = "SELECT * FROM arquivos WHERE evidence_id=? LIMIT 10 OFFSET $offset";
                    $stmt = mysqli_prepare($connection,  $sql);
                    mysqli_stmt_bind_param($stmt, "i", $param_evidence_id);
                    $param_evidence_id = $evidence_id;
                    if (mysqli_stmt_execute($stmt)) {
                        $result = mysqli_stmt_get_result($stmt);
                        $numTotal   = mysqli_num_rows($result);
                    } else
                        echo "Oops! Something went wrong. Please try again later.";
                } else {
                    
                    $sql = "SELECT * FROM arquivos LIMIT 10 OFFSET $offset";
                    $result = mysqli_query($connection, $sql);
                    $numTotal   = mysqli_num_rows($result);
                    if (!$result)
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($connection);
                }

                if (mysqli_num_rows($result) > 0) {
                ?>
                    <table id="arquivos" class="table">
                        <thead class="bg-primary table-dark border border-light">
                            <tr>
                                <th class="text-center">Details</th>
                                <th>ID</th>
                                <th>Evidence</th>
                                <th>Name</th>
                                <th>Size (In mb)</th>
                                <th>Action</th>
                                <th>Download</th>
    
                            </tr>
                        </thead>
                        <tbody class="border border-light bg-light">
                            <?php

                            while ($row = mysqli_fetch_array($result)) {
                            ?>
                                <tr>
                                    <td class="text-center"><span class="material-icons" data-bs-toggle="tooltip" data-bs-placement="bottom" title=<?= $row['description']; ?>>info</span></td>
                                    <td><?= $row['id']; ?></td>
                                    <td>Evidence <?= $row['evidence_id'] ?></td>
                                    <td><?= $row['name']; ?></td>
                                    <td><?= $row['size']/1000 . "KB"; ?></td>
                                    <td>
                                        <?php
                                        if (isset($_GET['evidence_id'])) {
                                            echo "<a href='#" . $row['id'] . "' title='Update File' data-toggle='tooltip'> <span class='material-icons' aria-hidden='true' style='color:#3ca23c;'>create</span></a>";
                                            echo "<a href='#" . $row['id'] . "' title='Delete File' data-toggle='tooltip'> <span class='material-icons' aria-hidden='true' style='color:crimson;'>delete_sweep</span></a>";
                                        } else {

                                            echo "<a href='#" . $row['id'] . "' title='Update File' data-toggle='tooltip'> <span class='material-icons' aria-hidden='true' style='color:#3ca23c;'>create</span></a>";
                                            echo "<a href='#" . $row['id'] . "' title='Delete File' data-toggle='tooltip'> <span class='material-icons' aria-hidden='true' style='color:crimson;'>delete_sweep</span></a>";

                                            }
                                        ?>
                                    </td>
                                    <td>
                                            
                                            <a href="evidence_files/<?php echo $row['name'];?>" download="<?php echo $row['name'];?>"> Download </a>   
                                        
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
                    if (isset($_GET['evidence_id']))
                        echo "<a href=?evidence_id=" . $evidence_id . "&page=" . $prev . "><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>chevron_left</span></span></a> <span class='badge bg-success align-top fs-5'>$page</span> <a href=?evidence_id=" . $evidence_id . "&page=" . $next . "><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>chevron_right</span></span></a>";
                    else
                        echo "<a href=\"?page=$prev\"><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>chevron_left</span></span></a> <span class='badge bg-success align-top fs-5'>$page</span> <a href=\"?page=$next\"><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>chevron_right</span></span></a>";
                    echo "</div>";
                    // Free result set
                    mysqli_free_result($result);
                } 
                else {
                    echo "<p class='lead'><em>No Files were found.</em></p>";
                }
                
                // Close connection
                mysqli_close($connection);
                ?>
                
                <div class="container">
                    <div class="row">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <h3>Add Files </h3>
                            <input type="file" name="arquivo"> <br>
                            <button type="submit" name="enviar_arquivo"> Upload </button>
                        </form>
                    </div>
                </div>  
 
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