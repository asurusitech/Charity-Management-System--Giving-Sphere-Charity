




if($_POST){

$email=$_POST['useremail'];
$password=$_POST['userpassword'];

$error='<label for="promter" class="form-label"></label>';

$result= $database->query("select * from users where email='$email'");
if($result->num_rows==1){
    $utype=$result->fetch_assoc()['usertype'];
    if($utype=='a'){
        $checker = $database->query("select * from admin where aemail='$email' and apassword='$password'");
        if ($checker->num_rows==1){


            //   Admin dashbord
            $_SESSION['user']=$email;
            $_SESSION['usertype']='a';
            
            header('location: /charity/admin/admin_dashboard.html');

        }else{
            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
        }


    }
 }
}