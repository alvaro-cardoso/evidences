<?php
    if($_SESSION['user_profile'] != 1) // Check if the user is a developer
        header('location: error.php?number=0');
?>