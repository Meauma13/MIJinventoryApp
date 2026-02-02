<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid']==0)) {
  header('location:logout.php');
  } else{

  // Handle delete confirmation from GET redirect
  if (isset($_GET['choice']) && $_GET['choice'] == 'yes') {
    $eid = $_GET['editid'];

    // Check for existing products
    $brand_name_query = mysqli_query($con, "SELECT BrandName FROM tblbrand WHERE ID = '$eid'");
    $brand_row = mysqli_fetch_array($brand_name_query);
    $brand_name = $brand_row['BrandName'];
    $prod_check = mysqli_query($con, "SELECT * FROM tblproducts WHERE BrandName = '$brand_name'");
    if (mysqli_num_rows($prod_check) > 0) {
      echo '<script>alert("Cannot delete brand: Products exist under this brand.")</script>';
      echo "<script>window.location.href='manage-brand.php'</script>";
      exit;
    }

    $query = mysqli_query($con, "DELETE FROM tblbrand WHERE ID = '$eid'");
    if ($query) {
      mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, TableName, RecordID) VALUES ('".$_SESSION['imsaid']."', 'DELETE', 'tblbrand', '$eid')");
      echo '<script>alert("Brand has been deleted.")</script>';
      header('location:manage-brand.php');
      exit;
    } else {
      echo '<script>alert("Something Went Wrong. Please try again.")</script>';
    }
  }
  else if ((isset($_GET['choice'])) && $_GET['choice'] == 'no') {
      header('location:manage-brand.php');
      exit;
    }

    if(isset($_POST['submit']))
  {
    $eid=$_GET['editid'];
    $brandname=$_POST['brandname'];
    $status=$_POST['status'];
     
    $query=mysqli_query($con, "update tblbrand set BrandName='$brandname',Status='$status' where ID=$eid");
    if ($query) {
    mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, TableName, RecordID) VALUES ('".$_SESSION['imsaid']."', 'UPDATE', 'tblbrand', '$eid')");
    echo '<script>alert("Brand name has been updated.")</script>';
    header('location:manage-brand.php');
  }
  else
    {
     echo '<script>alert("Something Went Wrong. Please try again")</script>';
    }

  
}
else if(isset($_POST['delete']))
{
$message = "Deleting this brand will affect all products associated with it. Do you want to proceed?";

// Use echo to trigger the JavaScript confirm box
echo "<script>
    var choice = confirm('$message');
    if (choice) {
        window.location.href = '?editid=" . $_GET['editid'] . "&choice=yes';
    } else {
        window.location.href = '?editid=" . $_GET['editid'] . "&choice=no';
    }
</script>";
}
  ?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Inventory Management System || Update Brand</title>
<?php include_once('includes/cs.php');?>
</head>
<body>

<!--Header-part-->
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>


<div id="content">
<div id="content-header">
  <div id="breadcrumb"> <a href="dashboard.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <strong>Update Brand</strong></div>
  <h1>Update Brand</h1>
</div>
<div class="container-fluid">
  <hr>
  <div class="row-fluid">
    <div class="span12">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
          <h5>Update Brand</h5>
        </div>
        <div class="widget-content nopadding">
          <form method="post" class="form-horizontal">
           <?php
 $eid=$_GET['editid'];
$ret=mysqli_query($con,"select * from tblbrand where ID='$eid'");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {

?>
            <div class="control-group">
              <label class="control-label">Brand Name :</label>
              <div class="controls">
                <input type="text" class="span11" name="brandname" id="brandname" value="<?php  echo $row['BrandName'];?>" required='true' />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Status :</label>
              <div class="controls">
                <?php  if($row['Status']=="1"){ ?>
                <input type="checkbox"  name="status" id="status" value="1"  checked="true"/>
                <?php } else { ?>
                  <input type="checkbox" value='1' name="status" id="status" />
                  <?php } ?>
              </div>
            </div>
            
           <?php } ?>
            <div class="form-actions">
              <button type="submit" class="btn btn-success" name="submit">Update</button>
              <button type="submit" class="btn btn-danger" name="delete" style="margin-left: 400px">Delete</button>
            </div>
          </form>
        </div>
      </div>
    
    </div>
  </div>
 </div>
</div>
<?php include_once('includes/footer.php');?>
<?php include_once('includes/js.php');?>
</body>
</html>
<?php } ?>