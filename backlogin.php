<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Charity Management</title>
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="logo.png" type="image/x-icon"> <!-- Favicon link -->
</head>
<body>

<?php


session_start();

$_SESSION["user"]="";
$_SESSION["usertype"]="";


//import database
include("connection.php");




if($_POST){

    $email=$_POST['useremail'];
    $password=$_POST['userpassword'];
    
    
    $error='<label for="promter" class="form-label"></label>';

    $result= $database->query("select * from users where email='$email'");
    if($result->num_rows==1){
        $usertype=$result->fetch_assoc()['usertype'];
        if ($usertype=='p'){
            $checker = $database->query("select * from patient where pemail='$email' and ppassword='$password'");
            if ($checker->num_rows==1){


                //   Patient dashbord
                $_SESSION['user']=$email;
                $_SESSION['usertype']='d';
                
                header('location: donor_dashboard.html');

            }else{
                $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
            }

        }elseif($usertype=='a'){
            $checker = $database->query("select * from admin where aemail='$email' and apassword='$password'");
            if ($checker->num_rows==1){


                //   Admin dashbord
                $_SESSION['user']=$email;
                $_SESSION['usertype']='a';
                
                header('location: admin_dashboard.html');

            }else{
                $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
            }


        }elseif($usertype=='d'){
            $checker = $database->query("select * from donors where donemail='$email' and donpassword='$password'");
            if ($checker->num_rows==1){


                //   doctor dashbord
                $_SESSION['user']=$email;
                $_SESSION['usertype']='d';
                header('location: donor_dashboard.html');

            }else{
                $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
            }

        }
        
    }else{
        $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">We cant found any acount for this email.</label>';
    }






    
}else{
    $error='<label for="promter" class="form-label">&nbsp;</label>';
}



?>







    
    <header>
        <div class="container header-content">
            <div class="logo-title">
                <img src="https://via.placeholder.com/50" alt="Logo" class="logo">
                <h1>Charity Management</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="login-section">
        <div class="container">
            <div class="login-form">
                <h2>Login</h2>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="useremail" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="userpassword" required>
                    </div>
                    <div class="form-group">
                        <button type="submit">Login</button>
                    </div>
                </form>
                <p class="signup-link">Don't have an account? <a href="signup.html">Sign up here</a></p>
                <p class="forgot-password-link"><a href="forgot_password.html">Forgot Password?</a></p>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            
        <p>2024 Giving Sphere Charity - Where Compassion Meets Action.</p>
 </div>
    </footer>
</body>
</html>
