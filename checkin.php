<?php
// Check if the user is logged
session_start();
if((!isset($_SESSION['login']))){
  unset($_SESSION['login']);
  header('location:login.php');
}
?>