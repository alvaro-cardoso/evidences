<?php
    // Require config db file
    require "config.php";

    // Check existence of id parameter
    if(!isset($_GET['id']) || empty($_GET['id'])){
        header("location: error.php?number=6");
        exit();
    }

    // Prerpare the select statement
    $sql = "SELECT content, name FROM files WHERE id=?";
    $stmt = mysqli_prepare($connection,$sql);
    mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result);

    // Get file info
    $content = $row['content'];
    $filename = $row['name'];
    
    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($connection);

    // Prepare to download file
    header("Content-Length: " . strlen($content) );
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=" . $filename);
    header("Content-Transfer-Encoding: binary\n");
    echo $content;
?>