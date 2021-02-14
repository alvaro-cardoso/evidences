<?php
    if($_SESSION['user_profile'] != 1 && $_SESSION['user_profile'] != 2) // Check if the user is a admin or a developer
        header('location: error.php?number=1');
?>