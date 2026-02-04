<?php
session_start();
error_reporting(E_ALL);
include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid']==0)) {
  header('location:logout.php');
  } else{
    if(isset($_POST['submit']))
  {
    $pname=$_POST['pname'];
    $category=$_POST['category'];
    $subcategory=$_POST['subcategory'];
    $stock=$_POST['stock'];
    $price=$_POST['price'];
    $status=$_POST['status'];
    $barcode=$_POST['barcode'];
     
    $query=mysqli_query($con, "insert into tblproducts(ProductName,CategoryID,SubcategoryID,Stock,SellingPrice,Status,Barcode) value('$pname','$category','$subcategory','$stock','$price','$status','$barcode')");
    if ($query) {
    $last_id = mysqli_insert_id($con);
    mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, Details, RecordID) VALUES ('".$_SESSION['imsaid']."', 'CREATE', 'tblproducts', '$last_id')");
   
    echo '<script>alert("Product has been created.")</script>';
  }
  else
    {
     echo '<script>alert("Something Went Wrong. Please try again")</script>';
    } 
}
  ?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Inventory Management System || Add Products</title>
<?php include_once('includes/cs.php');?>
<script>
  function getSubCat(val) {
  $.ajax({
    type: "POST",
    url: "get-subcat.php",
    data: { catid: val }, // Using object syntax is safer
    success: function(data) {
      $("#subcategory").html(data);
    },
    error: function() {
      alert("Error loading subcategories.");
    }
  });
}
</script>
</head>
<body>

<!--Header-part-->
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>


<div id="content">
<div id="content-header">
  <div id="breadcrumb"> <a href="dashboard.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="add-product.php" class="tip-bottom">Add Product</a></div>
  <h1>Add Product</h1>
</div>
<div class="container-fluid">
  <hr>
  <div class="row-fluid">
    <div class="span12">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
          <h5>Add Product</h5>
        </div>
        <div class="widget-content nopadding">
          <form method="post" class="form-horizontal">
           <div class="control-group">
              <label class="control-label">Product Name :</label>
              <div class="controls">
                <input type="text" class="span11" name="pname" id="pname" value="" required='true' placeholder="Enter Product Name" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Category :</label>
              <div class="controls">


  <select class="span11" name="category" id="category" onChange="getSubCat(this.value)" required="true">
    <option value="">Select Category</option>
    <?php 
    $query = mysqli_query($con, "select * from tblcategory where CategoryCode='1'");
    while($row = mysqli_fetch_array($query)) { ?>      
      <option value="<?php echo $row['ID'];?>"><?php echo $row['CategoryName'];?></option>
    <?php } ?>
  </select>
</div>

<div class="control-group">
  <label class="control-label">Sub Category Name:</label>
  <div class="controls">
    <select class="span11" name="subcategory" id="subcategory" required="true">
      <option value="">Select Sub Category</option>
    </select>
  </div>
</div>

            <div class="control-group">
              <label class="control-label">Stock(units) :</label>
              <div class="controls">
                <input type="text" class="span11"  name="stock" id="stock" value="" required="true" placeholder="Enter Stock" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Price(perunits) :</label>
              <div class="controls">
                <input type="text" class="span11" name="price" id="price" value="" required="true" placeholder="Enter Price" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Barcode :</label>
              <div class="controls">
                <input type="text" class="span11" name="barcode" id="barcode" value="" placeholder="Enter Barcode (optional)" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Status :</label>
              <div class="controls">
                <input type="checkbox"  name="status" id="status" value="1" required="true" />
              </div>
            </div>          
           
            <div class="form-actions">
              <button type="submit" class="btn btn-success" name="submit">Add</button>
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