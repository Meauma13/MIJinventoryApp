<?php
session_start();
error_reporting(E_ALL);

include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid'] == 0)) {
  header('location:logout.php');
} else {


  // Handle delete confirmation from GET redirect
  if (isset($_GET['choice']) && $_GET['choice'] == 'yes') {
    $eid = $_GET['editid'];
    $query = mysqli_query($con, "DELETE FROM tblproducts WHERE ID = '$eid'");
    if ($query) {
      mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, Details, RecordID) VALUES ('" . $_SESSION['imsaid'] . "', 'DELETE', 'tblproducts', '$eid')");
      echo '<script>alert("Product has been deleted.")</script>';
      echo "<script>window.location.href='manage-product.php'</script>";
      exit;
    } else {
      echo '<script>alert("Something Went Wrong. Please try again.")</script>';
    }
  }


  if (isset($_POST['submit'])) {
    $pname = $_POST['pname'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $costprice = $_POST['costprice'];
    $sellingprice = $_POST['sellingprice'];
    $status = $_POST['status'];
    $barcode = $_POST['barcode'];


    // attempt insertion of new records, ignore if duplictae detected based on ProductName (case-insensitive)
    $query = "insert ignore into tblproducts(ProductName,CategoryID,Stock,CostPrice,SellingPrice,Status,Barcode) value('$pname','$category','$stock','$costprice','$sellingprice','$status','$barcode')";

    mysqli_query($con, $query);

    // Check if any row was actually added
    if (mysqli_affected_rows($con) > 0) {
      
      $last_id = mysqli_insert_id($con);
      mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, Details, RecordID) VALUES ('" . $_SESSION['imsaid'] . "', 'CREATE', 'tblproducts', '$last_id')");

      echo '<script>alert("New product has been created.")</script>';
    } else {
      echo '<script>alert("Product already exists or no new data found.")</script>';
    }
  }
?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <title>Inventory Management System || Add Products</title>
    <?php include_once('includes/cs.php'); ?>
    <script>
      /*   function getSubCat(val) {
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
} */
    </script>
  </head>

  <body>

    <!--Header-part-->
    <?php include_once('includes/header.php'); ?>
    <?php include_once('includes/sidebar.php'); ?>


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


                      <!-- <select class="span11" name="category" id="category" onChange="getSubCat(this.value)" required="true"> -->

                      <select class="span11" name="category" id="category" required="true">
                        <option value="">Select Category</option>
                        <?php
                        $query = mysqli_query($con, "select * from tblcategory where CategoryCode='1' order by CategoryName ASC");
                        while ($row = mysqli_fetch_array($query)) { ?>
                          <option value="<?php echo $row['ID']; ?>"><?php echo $row['CategoryName']; ?></option>
                        <?php } ?>
                      </select>
                    </div>

                    <!-- <div class="control-group">
  <label class="control-label">Sub Category Name:</label>
  <div class="controls">
    <select class="span11" name="subcategory" id="subcategory" required="true">
      <option value="">Select Sub Category</option>
    </select>
  </div>
</div> -->

                    <div class="control-group">
                      <label class="control-label">Stock :</label>
                      <div class="controls">
                        <input type="text" class="span11" name="stock" id="stock" value="" required="true" placeholder="Enter Stock" />
                      </div>
                    </div>
                    <div class="control-group">
                      <label class="control-label">Cost Price :</label>
                      <div class="controls">
                        <input type="text" class="span11" name="costprice" id="price" value="" required="true" placeholder="Enter Cost Price" />
                      </div>
                    </div>
                    <div class="control-group">
                      <label class="control-label">Selling Price :</label>
                      <div class="controls">
                        <input type="text" class="span11" name="sellingprice" id="price" value="" required="true" placeholder="Enter Selling Price" />
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
                        <input type="checkbox" name="status" id="status" value="1" required="true" />
                      </div>
                    </div>

                    <div class="form-actions">
                      <button type="submit" class="btn btn-success span3" name="submit">Add</button>
                      <a href="dashboard.php" id="cancel" name="cancel" class="btn btn-info span3">Cancel</a>
                    </div>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <?php include_once('includes/footer.php'); ?>
    <?php include_once('includes/js.php'); ?>
  </body>

  </html>
<?php } ?>