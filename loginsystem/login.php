<?php session_start(); 
include_once('includes/config.php');
// Code for login 
if(isset($_POST['login']))
{
    // Secure input
    $useremail = mysqli_real_escape_string($con, trim($_POST['uidentity']));
    $password = $_POST['password'];

    // First, try to authenticate against tbladmin (admin/ceo/staff)
    $md5pass = md5($password);
    $admin_q = mysqli_query($con, "SELECT ID, AdminName, role FROM tbladmin WHERE (Email='".$useremail."' OR UserName='".$useremail."') AND (Password='".$md5pass."' OR Password='".$password."') LIMIT 1");
    if($admin_q && mysqli_num_rows($admin_q) > 0){
        $admin = mysqli_fetch_assoc($admin_q);
        // set admin session keys used by admin area
        session_regenerate_id(true);
        $_SESSION['imsaid'] = $admin['ID'];
        $_SESSION['role'] = $admin['role'];
        if(strtolower($admin['role']) == 'ceo'){
            header('location:../admin/ceo-dashboard.php');
            exit;
        } else {
            header('location:../admin/dashboard.php');
            exit;
        }
    }

    // Fallback: authenticate against legacy `users` table (site users)
    // allow login by username (preferred) or email; check both plain and md5-stored passwords
    $safe_pw = mysqli_real_escape_string($con, $password);
    $md5_pw = md5($password);
    $ret = mysqli_query($con, "SELECT id,fname FROM users WHERE (username='".$useremail."' OR email='".$useremail."') AND (password='".$safe_pw."' OR password='".$md5_pw."') LIMIT 1");
    if($ret && mysqli_num_rows($ret) > 0){
        $num = mysqli_fetch_assoc($ret);
        session_regenerate_id(true);
        $_SESSION['id'] = $num['id'];
        $_SESSION['name'] = $num['fname'];
        header("location:welcome.php");
        exit;
    }

    // No match
    echo "<script>alert('Invalid username or password');</script>";

}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>User Login | Registration and Login System</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">

<div class="card-header">
    <h3 class="text-center font-weight-light my-4">User Login</h3></div>
                                    <div class="card-body">
                                        
                                        <form method="post">
                                            
<div class="form-floating mb-3">
</div>
<div class="form-floating mb-3">
<input class="form-control" name="uidentity" type="text" placeholder="Username" required/>
<label for="inputEmail">Username</label>
</div>
                                            

<div class="form-floating mb-3">
<input class="form-control" name="password" type="password" placeholder="Password" required />
<label for="inputPassword">Password</label>
</div>


<div class="d-flex align-items-center justify-content-between mt-4 mb-0">
<a class="small" href="password-recovery.php">Forgot Password?</a>
<button class="btn btn-primary" name="login" type="submit">Login</button>
</div>
</form>
</div>
                                    <div class="card-footer text-center py-3">
                                        <!-- <div class="small"><a href="signup.php">Need an account? Sign up!</a></div> -->
                                          <div class="small"><a href="index.php">Back to Home</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
<?php include('includes/footer.php');?>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
