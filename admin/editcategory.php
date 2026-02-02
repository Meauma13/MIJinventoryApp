<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid']==0)) {
  header('location:logout.php');
  } 
  
  // Handle delete confirmation from GET redirect
    else {
   if (isset($_GET['choice']) && $_GET['choice'] == 'yes') {
      $eid = $_GET['editid'];

      // Check for existing subcategories
      $subcat_check = mysqli_query($con, "SELECT * FROM tblsubcategory WHERE CatID = '$eid'");
      if (mysqli_num_rows($subcat_check) > 0) {
        echo '<script>alert("Cannot delete category: Subcategories exist under this category.")</script>';
        echo "<script>window.location.href='manage-category.php'</script>";
        exit;
      }

      // Check for existing products
      $prod_check = mysqli_query($con, "SELECT * FROM tblproducts WHERE CatID = '$eid'");
      if (mysqli_num_rows($prod_check) > 0) {
        echo '<script>alert("Cannot delete category: Products exist under this category.")</script>';
        echo "<script>window.location.href='manage-category.php'</script>";
        exit;
      }

      $query = mysqli_query($con, "DELETE FROM tblcategory WHERE ID = '$eid'");
      if ($query) {
        mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, TableName, RecordID) VALUES ('".$_SESSION['imsaid']."', 'DELETE', 'tblcategory', '$eid')");
        header('location:manage-category.php');
        exit;
      } else {
        echo '<script>alert("Something went wrong. Please try again.")</script>';
      }
    }
    else if ((isset($_GET['choice'])) && $_GET['choice'] == 'no') {
      header('location:manage-category.php');
      exit;
    }

    // Handle form submissions
  
    if (isset($_POST['submit'])) {
      // Existing update logic (unchanged)
      $eid = $_GET['editid'];
      $category = $_POST['category'];
      $status = $_POST['status'];
      $query = mysqli_query($con, "UPDATE tblcategory SET CategoryName='$category', Status='$status' WHERE ID='$eid'");
      if ($query) {
        mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, TableName, RecordID) VALUES ('".$_SESSION['imsaid']."', 'UPDATE', 'tblcategory', '$eid')");
        echo '<script>alert("Category has been updated.")</script>';
        header('location:manage-category.php');
      } else {
        echo '<script>alert("Something Went Wrong. Please try again.")</script>';
      }
    } 
    
    else if (isset($_POST['delete'])) {
      $message = "Deleting this category will delete all products contained within. Do you want to proceed?";
      // Use echo to trigger the JavaScript confirm box
      echo "<script>
        var choice = confirm('$message');
        if (choice) {
          window.location.href = '?editid=" . $_GET['editid'] . "&choice=yes';
        } else {
          window.location.href = '?editid=" . $_GET['editid'] . "&choice=no';
        }
      </script>";
      // No server-side handling here; it happens on redirect
    }
  ?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Inventory Management System || Update Category</title>
<?php include_once('includes/cs.php');?>
</head>
<body>

<!--Header-part-->
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>


<div id="content">
<div id="content-header">
  <div id="breadcrumb"> <a href="dashboard.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <strong>Update Category</strong></div>
  <h1>Update Category</h1>
</div>
<div class="container-fluid">
  <hr>
  <div class="row-fluid">
    <div class="span12">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
          <h5>Update Category</h5>
        </div>
        <div class="widget-content nopadding">
          <form method="post" class="form-horizontal">
           
<?php
 $eid=$_GET['editid'];
$ret=mysqli_query($con,"select * from tblcategory where ID='$eid'");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {

?>
            <div class="control-group">
              <label class="control-label">Category Name :</label>
              <div class="controls">
                <input type="text" class="span11" name="category" id="category" value="<?php  echo $row['CategoryName'];?>" required='true' />
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
              <button type="submit" class="btn btn-success" name="submit" style = "margin-left: 100px">Update</button>
              <button type="submit" class="btn btn-danger" name="delete" style = "margin-left: 400px">Delete</button>
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