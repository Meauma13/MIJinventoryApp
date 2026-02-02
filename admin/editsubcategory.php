<?php
session_start();
//error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid']==0)) {
  header('location:logout.php');
  } else{

  // Handle delete confirmation from GET redirect
  if (isset($_GET['choice']) && $_GET['choice'] == 'yes') {
    $sid = $_GET['scid'];

    // Check for existing products
    $prod_check = mysqli_query($con, "SELECT * FROM tblproducts WHERE SubcatID = '$sid'");
    if (mysqli_num_rows($prod_check) > 0) {
      echo '<script>alert("Cannot delete subcategory: Products exist under this subcategory.")</script>';
      echo "<script>window.location.href='manage-subcategory.php'</script>";
      exit;
    }

    $query = mysqli_query($con, "DELETE FROM tblsubcategory WHERE ID = '$sid'");
    if ($query) {
      mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, TableName, RecordID) VALUES ('".$_SESSION['imsaid']."', 'DELETE', 'tblsubcategory', '$sid')");
      echo '<script>alert("Sub Category has been deleted.")</script>';
      header('location:manage-subcategory.php');
      exit;
    } else {
      echo '<script>alert("Something Went Wrong. Please try again.")</script>';
    }
  }
  else if ((isset($_GET['choice'])) && $_GET['choice'] == 'no') {
      header('location:manage-subcategory.php');
      exit;
  }

if(isset($_POST['update']))
  {
    $sid=$_GET['scid'];
    $catid=$_POST['category'];
    $subcat=$_POST['subcat'];
    $status=$_POST['status'];
     
    $query=mysqli_query($con, "update tblsubcategory set CatID  ='$catid', SubCategoryname='$subcat',Status='$status' where ID='$sid'");
    
    if ($query) {
    mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, TableName, RecordID) VALUES ('".$_SESSION['imsaid']."', 'UPDATE', 'tblsubcategory', '$sid')");
    echo '<script>alert("Sub Category has been updated.")</script>';
    header('location:manage-subcategory.php');
  }
  else
    {
       echo '<script>alert("Something Went Wrong. Please try again")</script>';
    }

  
}
else if(isset($_POST['delete']))
{
$message = "Deleting this subcategory will delete all products contained within. Do you want to proceed?";

// Use echo to trigger the JavaScript confirm box
echo "<script>
    var choice = confirm('$message');
    if (choice) {
        window.location.href = '?scid=" . $_GET['scid'] . "&choice=yes';
    } else {
        window.location.href = '?scid=" . $_GET['scid'] . "&choice=no';
    }
</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Inventory Management System || Update Sub Category</title>
<?php include_once('includes/cs.php');?>
</head>
<body>

<!--Header-part-->
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>


<div id="content">
<div id="content-header">
  <div id="breadcrumb"> <a href="dashboard.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <strong>Update Sub Category</strong></div>
  <h1>Update Sub Category</h1>
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
                     $sid=$_GET['scid'];
$ret=mysqli_query($con,"select tblcategory.CategoryName as catname,tblcategory.ID as cid,tblsubcategory.SubCategoryname as subcat,tblsubcategory.Status from tblsubcategory inner join tblcategory on tblcategory.ID=tblsubcategory.CatID where tblsubcategory.ID='$sid'");

while ($row=mysqli_fetch_array($ret)) {

?>
            <div class="control-group">
              <label class="control-label">Category Name :</label>
              <div class="controls">
                <select name="category" class="span11" required="true">
                    <option value="<?php echo $row['cid'];?>"><?php echo $row['catname'];?></option>
              <?php $query=mysqli_query($con,"select * from tblcategory");
              while($result=mysqli_fetch_array($query))
              {
              ?>      
                  <option value="<?php echo $result['ID'];?>"><?php echo $result['CategoryName'];?></option>
                  <?php } ?>
                  </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Sub Category Name :</label>
              <div class="controls">
                <input type="text" class="span11" name="subcat" id="subcat" value="<?php  echo $row['subcat'];?>" required='true' />
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
              <button type="submit" class="btn btn-success" name="update">Update</button>
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