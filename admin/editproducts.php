<?php
session_start();
error_reporting(E_ALL);
include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid']==0)) {
  header('location:logout.php');
  } else{

  // Handle delete confirmation from GET redirect
  if (isset($_GET['choice']) && $_GET['choice'] == 'yes') {
    $eid = $_GET['editid'];
    $query = mysqli_query($con, "DELETE FROM tblproducts WHERE ID = '$eid'");
    if ($query) {
      mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, Details, RecordID) VALUES ('".$_SESSION['imsaid']."', 'DELETE', 'tblproducts', '$eid')");
      echo '<script>alert("Product has been deleted.")</script>';
      echo "<script>window.location.href='manage-product.php'</script>";
      exit;
    } else {
      echo '<script>alert("Something Went Wrong. Please try again.")</script>';
    }
  }

    if(isset($_POST['submit']))
  {
    $eid=$_GET['editid'];
    $pname=$_POST['pname'];
    $category=$_POST['category'];
    $stock=$_POST['stock'];
    $costprice=$_POST['costprice'];
    $sellingprice=$_POST['sellingprice'];
    $status=$_POST['status'];

     
    $query=mysqli_query($con, "update tblproducts set ProductName='$pname',CategoryID='$category',Stock='$stock',CostPrice='$costprice',SellingPrice='$sellingprice',Status='$status' where ID='$eid'");
    if ($query) {
    mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, Details, RecordID) VALUES ('".$_SESSION['imsaid']."', 'UPDATE', 'tblproducts', '$eid')");
   
    echo '<script>alert("Product has been updated.")</script>';
    echo "<script>window.location.href='manage-product.php'</script>";
  }
  else
    {
     echo '<script>alert("Something Went Wrong. Please try again")</script>';
    }

  
}
else if(isset($_POST['delete']))
{
$message = "Deleting this product will remove it from inventory. Do you want to proceed?";

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
<title>Inventory Management System || Update Products</title>
<?php include_once('includes/cs.php');?>
<script>
/* function getSubCat(val) {
  $.ajax({
type:"POST",
url:"get-subcat.php",
data:'catid='+val,
success:function(data){
$("#subcategory").html(data);
}
  });
} */
  </script>
</head>
<body>

<!--Header-part-->
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>


<div id="content">
<div id="content-header">
  <div id="breadcrumb"> <a href="dashboard.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <strong>Update Product</strong></div>
  <h1>Update Product</h1>
</div>
<div class="container-fluid">
  <hr>
  <div class="row-fluid">
    <div class="span12">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
          <h5>Update Product</h5>
        </div>
        <div class="widget-content nopadding">
          <form method="post" class="form-horizontal">
            <?php
            $eid=$_GET['editid'];
$ret=mysqli_query($con,"select tblcategory.CategoryName as catname,tblcategory.ID as catid,tblproducts.ID as pid,tblproducts.ProductName,tblproducts.Status,tblproducts.CostPrice,tblproducts.SellingPrice,tblproducts.Stock from tblproducts inner join tblcategory on tblcategory.ID=tblproducts.CategoryID where tblproducts.ID='$eid'");

$cnt=1;
while ($row=mysqli_fetch_array($ret)) {

?>
           <div class="control-group">
              <label class="control-label">Product Name :</label>
              <div class="controls">
                <input type="text" class="span11" name="pname" id="pname" value="<?php echo $row['ProductName'];?>" required='true'/>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Category :</label>
              <div class="controls">
                <select type="text" class="span11" name="category" id="category" onChange="getSubCat(this.value)" value="" required='true' />
                   <option value="<?php echo $row['catid'];?>"><?php echo $row['catname'];?></option>
                    <?php $query=mysqli_query($con,"select * from tblcategory where CategoryCode='1'");
              while($rw=mysqli_fetch_array($query))
              {
              ?>      
                  <option value="<?php echo $rw['ID'];?>"><?php echo $rw['CategoryName'];?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <!-- <div class="control-group">
              <label class="control-label">Sub Category Name: :</label>
              <div class="controls">
                <select type="text" class="span11" name="subcategory" id="subcategory" value="" required='true' />
                  <option value="<?php // echo $row['scatid'];?>"><?php // echo $row['subcat'];?></option>
                </select>
              </div>
            </div> -->
          
            <div class="control-group">
              <label class="control-label">Stock :</label>
              <div class="controls">
                <input type="text" class="span11"  name="stock" id="stock" value="<?php echo $row['Stock'];?>" required="true"/>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Cost Price :</label>
              <div class="controls">
                <input type="text" class="span11" name="costprice" id="price" value="<?php echo $row['CostPrice'];?>" required="true"/>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Selling Price :</label>
              <div class="controls">
                <input type="text" class="span11" name="sellingprice" id="price" value="<?php echo $row['SellingPrice'];?>" required="true"/>
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
              <button type="submit" class="btn btn-success span3" name="submit">Update</button>
              <a href="manage-product.php" id="cancel" name="cancel" class="btn btn-info span3">Cancel</a>
              <button type="submit" class="btn btn-danger span3" name="delete">Delete</button>
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