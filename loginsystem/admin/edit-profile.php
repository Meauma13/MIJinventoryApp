<?php session_start();
include_once('../includes/config.php');
if (strlen($_SESSION['adminid']==0)) {
  header('location:logout.php');
  } else{
//Code for Updation 
if(isset($_POST['update']))
{
    $fname=$_POST['fname'];
    $lname=$_POST['lname'];
    $contact=$_POST['contact'];
$userid=$_GET['uid'];
    $msg=mysqli_query($con,"update users set fname='$fname',lname='$lname',contactno='$contact' where id='$userid'");

if($msg)
{
    echo "<script>alert('Profile updated successfully');</script>";
       echo "<script type='text/javascript'> document.location = 'manage-users.php'; </script>";
}
}


    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
    $fname=mysqli_real_escape_string($con, $_POST['fname']);
    $lname=mysqli_real_escape_string($con, $_POST['lname']);
    $contact=mysqli_real_escape_string($con, $_POST['contact']);
    $username=mysqli_real_escape_string($con, trim($_POST['username']));
    $userid=$_GET['uid'];

    // check username uniqueness (exclude current user)
    $chk = mysqli_query($con, "SELECT id FROM users WHERE username='$username' AND id<>$userid LIMIT 1");
    if($chk && mysqli_num_rows($chk) > 0){
        echo "<script>alert('Username already in use by another user. Choose another.');</script>";
    } else {
        $msg=mysqli_query($con,"update users set fname='$fname',lname='$lname',contactno='$contact', username='$username' where id='$userid'");

        if($msg){
            // also sync tbladmin username for matching email (if exists)
            $res = mysqli_query($con, "SELECT email FROM users WHERE id='$userid' LIMIT 1");
            if($res && $r = mysqli_fetch_assoc($res)){
                $uemail = mysqli_real_escape_string($con, $r['email']);
                mysqli_query($con, "UPDATE tbladmin SET UserName='$username' WHERE Email='$uemail'");
            }
            echo "<script>alert('Profile updated successfully');</script>";
            echo "<script type='text/javascript'> document.location = 'manage-users.php'; </script>";
        }
    }
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Edit Profile | Registration and Login System</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
      <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
          <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        
<?php 
$userid=$_GET['uid'];
$query=mysqli_query($con,"select * from users where id='$userid'");
while($result=mysqli_fetch_array($query))
{?>
                        <h1 class="mt-4"><?php echo $result['fname'];?>'s Profile</h1>
                        <div class="card mb-4">
                     <form method="post">
                            <div class="card-body">
                                <table class="table table-bordered">
                                   <tr>
                                    <th>First Name</th>
                                       <td><input class="form-control" id="fname" name="fname" type="text" value="<?php echo $result['fname'];?>" required /></td>
                                   </tr>
                                   <tr>
                                       <th>Last Name</th>
                                       <td><input class="form-control" id="lname" name="lname" type="text" value="<?php echo $result['lname'];?>"  required /></td>
                                   </tr>
                                      <tr>
                                       <th>Username</th>
                                       <td><?php echo isset($result['username']) ? $result['username'] : '';?></td>
                                   </tr>
                                         <tr>
                                       <th>Contact No.</th>
                                       <td colspan="3"><input class="form-control" id="contact" name="contact" type="text" value="<?php echo $result['contactno'];?>"  pattern="[0-9]{10}" title="10 numeric characters only"  maxlength="10" required /></td>
                                   </tr>
                                   <tr>
                                       <th>Email</th>
                                       <td colspan="3"><?php echo $result['email'];?></td>
                                   </tr>
                               
                                     
                                        <tr>
                                       <th>Reg. Date</th>
                                       <td colspan="3"><?php echo $result['posting_date'];?></td>
                                   </tr>
                                   <tr>
                                       <td colspan="4" style="text-align:center ;"><button type="submit" class="btn btn-primary btn-block" name="update">Update</button></td>

                                   </tr>
                                    </tbody>
                                </table>
                            </div>
                            </form>
                        </div>
<?php } ?>

                    </div>
                </main>
          <?php include('../includes/footer.php');?>
            </div>
                                                               <tr>
                                                                   <th>Username</th>
                                                                   <td colspan="3"><input class="form-control" id="username" name="username" type="text" value="<?php echo isset($result['username'])? $result['username'] : ''; ?>" required /></td>
                                                               </tr>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
        <script src="../js/datatables-simple-demo.js"></script>
    </body>
</html>
<?php } ?>
