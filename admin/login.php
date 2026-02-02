<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if(isset($_POST['login']))
  {
    $adminuser=$_POST['username'];
    $password=md5($_POST['password']);
    $query=mysqli_query($con,"select ID, Role from tbladmin where  UserName='$adminuser' && Password='$password' ");
    $ret=mysqli_fetch_array($query);
    if($ret>0){
      $_SESSION['imsaid']=$ret['ID'];
      $_SESSION['role']=$ret['Role'];
      if($ret['Role'] == 'ceo'){
        header('location:ceo-dashboard.php');
      } else {
        header('location:dashboard.php');
      }
    }
    else{
    
    echo '<script>alert("Invalid Details.")</script>';

    }
  }
  if(isset($_POST['submit']))
  {
    $contactno=$_POST['contactno'];
    $email=$_POST['email'];
$password=md5($_POST['newpassword']);
        $query=mysqli_query($con,"select ID from tbladmin where  Email='$email' and MobileNumber='$contactno' ");
        
    $ret=mysqli_num_rows($query);
    if($ret>0){
      $_SESSION['contactno']=$contactno;
      $_SESSION['email']=$email;
      $query1=mysqli_query($con,"update tbladmin set Password='$password'  where  Email='$email' && MobileNumber='$contactno'");
       if($query1)
   {
echo "<script>alert('Password successfully changed');</script>";

   }
     
    }
    else{
    
      echo "<script>alert('Invalid Details. Please try again.');</script>";
    }
  }
  ?>
<!DOCTYPE html>
<html lang="en">
    
<head>
        <title>Inventory Management System || Login Page</title><meta charset="UTF-8" />
        
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="css/matrix-login.css" />
        <link href="font-awesome/css/font-awesome.css" rel="stylesheet" />
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
<script type="text/javascript">
function checkpass()
{
if(document.changepassword.newpassword.value!=document.changepassword.confirmpassword.value)
{
alert('New Password and Confirm Password field does not match');
document.changepassword.confirmpassword.focus();
return false;
}
return true;
} 

</script>
    </head>
    <body>
        <div id="loginbox">            
            <form id="loginform" class="form-vertical" method="post">
				 <div class="control-group normal_text"> <h3>Admin Inventory</strong> <strong style="color: orange">System</strong></h3></div>
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lg"><i class="icon-user"> </i></span><input type="text" placeholder="Username" name="username" required="true" />
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_ly"><i class="icon-lock"></i></span><input type="password" placeholder="Password" name="password" required="true"/>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <span class="pull-left"><a href="#" class="flip-link btn btn-info" id="to-recover">Lost password?</a></span>
                    <span class="pull-right"><input type="submit" class="btn btn-success" name="login" value="Sign In"></span>
                </div>
            </form>
            <div>
                <a style="margin-left: 20px;" href="../index.php" class="flip-link btn btn-info" id="to-recover"><i class="icon-home"></i>&nbsp;Back to Home</a>
                <a style="margin-left: 142px;" href="..\loginsystem/signup.php" class="flip-link btn btn-success"><i class="icon-user"></i>&nbsp;Signup New Staff</a>
            </div>

                <br />

            <form id="recoverform" class="form-vertical" method="post" name="changepassword" onsubmit="return checkpass();">
				<p class="normal_text">Enter your e-mail address below and we will send you instructions on how to recover your password.</p>
				
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lo"><i class="icon-envelope"></i></span><input type="text" placeholder="E-mail address" name="email" required="true" />
                        </div>
                    </div>
                    <br />
               <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lo"><i class="icon-phone-sign"></i></span><input type="text" placeholder="Contact Number" name="contactno" required="true" />
                        </div>
                    </div>
                    <br />
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lo"><i class="icon-lock"></i></span><input type="password" name="newpassword" placeholder="New Password" required="true" />
                        </div>
                    </div>
                    <br />
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lo"><i class="icon-lock"></i></span><input type="password" name="confirmpassword" placeholder="Confirm Password" required="true" />
                        </div>
                    </div>
                <div class="form-actions">
                    <span class="pull-left"><a href="#" class="flip-link btn btn-success" id="to-login">&laquo; Back to login</a></span>
                    <span class="pull-right"><input type="submit" class="btn btn-success" name="submit" value="Reset"></span>

                </div>
            </form>
        </div>
        
        <script src="js/jquery.min.js"></script>  
        <script src="js/matrix.login.js"></script> 
    </body>

</html>
