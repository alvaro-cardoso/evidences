<?php
// Include checkin, config and authorization file
require_once "checkin.php";
require "auth_admin.php";
require_once "config.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users List</title>
    <!-- library css -->
    <link rel='shortcut icon' href='GG_Management_Solutions_Icon.png'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

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
                        <?php if($_SESSION['user_profile'] == 1){ // Options only available to admins ?>
                            <li><a class="dropdown-item bg-dark text-light" href="evidences.php"><span class='material-icons float-start' aria-hidden='true'>folder</span>&nbsp;Evidences</a></a></li>
                            <li><a class="dropdown-item bg-dark text-light" href="users.php"><span class='material-icons float-start' aria-hidden='true'>folder_shared</span>&nbsp;Users</a></li>
                        <?php } ?>
                        <li><a id ="exit" class="dropdown-item bg-dark text-light" href="index.php?logout=1"><span class='material-icons float-start' aria-hidden='true'>power_settings_new</span>&nbsp;Logout</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-10 bg-light">
                <br>
                <h3 class="titulo-tabla">Users List <a href="create_user.php" class="btn btn-success pull-right">Add New User</a></h3>
                <hr class="bg-dark">
                <?php
                // Attempt select query execution
                $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
                // Number of items displayed in a page
                $offset = ($page - 1) * 10;
                $sql = "SELECT * FROM users LIMIT 10 OFFSET $offset";
                ?>
                <?php
                if ($result = mysqli_query($connection, $sql)) {
                    if (mysqli_num_rows($result) > 0) {
                ?>
                        <table id="users" class="table">
                            <thead class="bg-primary table-dark border border-light">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Login</th>
                                    <th>Username</th>
                                    <th>Profile</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="border border-light bg-light">
                                <?php
                                while ($row = mysqli_fetch_array($result)) {
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $row['id']; ?></td>
                                        <td><?= $row['login']; ?></td>
                                        <td><?= $row['username']; ?></td>
                                        <td><?php switch ($row['profile']) {
                                                case "1":
                                                    echo "Admin";
                                                    break;
                                                case "2":
                                                    echo "Developer";
                                                    break;
                                                case "3":
                                                    echo "Certifier";
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<a href='update_user.php?id=" . $row['id'] . "&users_page=" . $page . "' title='Update User' data-toggle='tooltip'> <span class='material-icons' aria-hidden='true' style='color:#3ca23c;'>create</span></a>";
                                            echo "<a href='delete_user.php?id=" . $row['id'] . "&users_page=" . $page . "' title='Delete User' data-toggle='tooltip'> <span class='material-icons' aria-hidden='true' style='color:crimson;'>delete_sweep</span></a>";
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                <?php
                        $sqlTotal   = "SELECT id FROM users"; // Total users
                        $qrTotal    = mysqli_query($connection, $sqlTotal); 
                        $numTotal   = mysqli_num_rows($qrTotal); // All table registers
                        $totalPage = ceil($numTotal / 10); // Page total
                        $prev  = (($page - 1) == 0) ? 1 : $page - 1; // Previous page
                        $next = (($page + 1) >= $totalPage) ? $totalPage : $page + 1; // Next page
                        echo "<div class='text-center'>";
                        // Pagination Conditions
                        if ($page > 1) {
                            echo "<a href=\"?page=1\"><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>first_page</span></span></a>
                        <a href=\"?page=$prev\"><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>chevron_left</span></span>&nbsp</a>";
                            if (($page != $totalPage)) {
                                echo "<span class='badge bg-success align-top fs-5'>1</span>";
                                echo "&nbsp<span class='badge bg-secondary'><span class='material-icons' aria-hidden='true'>more_horiz</span></span>&nbsp";
                            }
                        }
                        echo "<span class='badge bg-dark align-top fs-5'>$page</span>";
                        if ($page < $totalPage) {
                            if (($page != 1)) {
                                echo "&nbsp<span class='badge bg-secondary'><span class='material-icons' aria-hidden='true'>more_horiz</span></span>&nbsp";
                                echo "<span class='badge bg-success align-top fs-5'>$totalPage</span>";
                            }
                            echo "&nbsp<a href=\"?page=$next\"><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>chevron_right</span></span></a>
                        <a href=\"?page=$totalPage\"><span class='badge bg-dark'><span class='material-icons' aria-hidden='true'>last_page</span></span></a>";
                        }
                        echo "</div>";
                        // Free result set
                        mysqli_free_result($result);
                    } else {
                        echo "<p class='lead'><em>No users were found.</em></p>";
                    }
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

    <!-- internal script -->
    <script src="javascript.js"></script>
</body>

</html>