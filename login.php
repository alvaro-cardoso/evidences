<?php
session_start();
require_once "config.php";

//Assign post values to the variables
if(isset($_POST["login"])){
  $login = trim($_POST["login"]);
  $pass = trim($_POST["pass"]);
}
//Check login/pass
if(isset($login) and isset($pass)){
  //Password encryption
  $password = md5($pass);
  //sql query
  $sql = "SELECT * FROM users WHERE login=? AND password=?";
         
        if($stmt = mysqli_prepare($connection, $sql))
        {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $login, $password);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
              $stmt->store_result();
              // Check if the query has valid results
               if($stmt->num_rows > 0){
                
                $stmt->bind_result($user_id, $user_login, $user_pass, $user_name, $user_profile);
                $stmt->fetch();
                // Set parameters
                $_SESSION['login'] = $login;
                $_SESSION['user_profile'] = $user_profile;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $user_name;
                $stmt->free_result();
                header('location:index.php');
               }
               else{
                header('location:login.php?error=dataerror');
               }
            }
            else
            {
                echo "Something went wrong. Please try again.";
            }
            
        }
                // Close statement
                mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($connection);
}

else{
  echo "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Login</title>
<link rel='shortcut icon' href='GG_Management_Solutions_Icon.png'>
<link rel='stylesheet' href='https://fonts.googleapis.com/icon?family=Material+Icons'>
<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css'>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css'> 
<style>
form {
  animation: fadeInDown .5s forwards;
  animation-delay: 0.4s;
  opacity: 0;
}

@keyframes fadeInDown {
from {
        opacity: 0;
        transform: translate3d(0, -90px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}
  body {
    color: #fff;
    background: linear-gradient(to right,#1a6e50,#00DA5D);
  }
  .form-control {
    min-height: 41px;
    background: #d9d9d9;
    box-shadow: none !important;
    border: transparent;
  }
  .form-control:focus {
    background: #e6e6e6;
  }
  .form-control, .btn {        
        border-radius: 2px;
    }
  .login-form {
    width: 400px;
    margin: 40px auto;
    text-align: center;
  }

    .login-form form {
    color: #999999;
    border-radius: 3px;
      margin-bottom: 15px;
        background: #1C1C1C;
        box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        padding: 30px;
    }
    .login-form .btn {        
        font-size: 16px;
        font-weight: bold;
    background: success;
    border: none;
        outline: none !important;
    }
  .login-form .btn:hover, .login-form .btn:focus {
    opacity: 0.8;
  }
  .login-form a:hover {
    text-decoration: underline;
  }
  .login-form form a {
    text-decoration: none;
  }
  .material-icons {
    font-size: 120px;
}

</style>
</head>
<body>
<div class='login-form'>
    <form action='login.php' method='post'>
    <div class='ic'>
          <a class='text-center'><span class='material-icons text-light' aria-hidden='true'>account_circle</span></a>
          </div>
        <h2 class='text-center text-light' style = 'margin: 0 0 25px'>Login</h2>   
        <div class='form-group has-error'>
          <input type='text' class='form-control' name='login' placeholder='Enter Login' required> <br>
        </div>
    <div class='form-group'>
            <input type='password' class='form-control' name='pass' placeholder='Enter Password' required> <br>
        </div>        
        <div class='form-group'>
            <button type='submit' class='btn btn-success btn-block btn-lg'>Sign in</button>
        </div>
      <br>
        <div><?php ?></div>
    </form>
</div>
</body>
</html>
  ";
  //Login error
  if(isset($_GET["error"]) && !empty(trim($_GET["error"])))
    {
        echo "<script> alert('Invalid Username or Password ! Please try again.');</script>";
}
}
