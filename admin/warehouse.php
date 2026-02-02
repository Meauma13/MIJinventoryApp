<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid']==0)) {
  header('location:logout.php');
  } else{

if(isset($_POST['transfer']))
{
  $productid = $_POST['product'];
  $quantity = $_POST['quantity'];

  // Check warehouse stock
  $wh_query = mysqli_query($con, "SELECT WarehouseStock FROM tblwarehouse WHERE ProductID='$productid'");
  $wh_row = mysqli_fetch_array($wh_query);
  if($wh_row['WarehouseStock'] >= $quantity) {
    // Update warehouse
    mysqli_query($con, "UPDATE tblwarehouse SET WarehouseStock = WarehouseStock - $quantity WHERE ProductID='$productid'");
    // Update main stock
    mysqli_query($con, "UPDATE tblproducts SET Stock = Stock + $quantity WHERE ID='$productid'");
    // Audit
    mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, TableName, RecordID, Details) VALUES ('".$_SESSION['imsaid']."', 'TRANSFER', 'tblproducts', '$productid', 'Transferred $quantity from warehouse')");
    echo '<script>alert("Transfer successful.")</script>';
  } else {
    echo '<script>alert("Insufficient stock in warehouse.")</script>';
  }
}

if(isset($_POST['update_stock']))
{
  $productid = $_POST['stock_product'];
  $new_stock = $_POST['new_stock'];
  mysqli_query($con, "UPDATE tblwarehouse SET WarehouseStock = $new_stock WHERE ProductID='$productid'");
  mysqli_query($con, "INSERT INTO tblauditlog (UserID, Action, TableName, RecordID, Details) VALUES ('".$_SESSION['imsaid']."', 'UPDATE', 'tblwarehouse', '$productid', 'Warehouse stock updated to $new_stock')");
  echo '<script>alert("Warehouse stock updated.")</script>';
}

if(isset($_POST['create_shipment']))
{
  $name = $_POST['shipment_name'];
  $date = $_POST['arrival_date'];
  mysqli_query($con, "INSERT INTO tblshipments (ShipmentName, ArrivalDate) VALUES ('$name', '$date')");
  echo '<script>alert("Shipment created.")</script>';
}

if(isset($_POST['add_item']))
{
  $sid = $_POST['sid'];
  $pid = $_POST['item_product'];
  $qty = $_POST['item_qty'];
  mysqli_query($con, "INSERT INTO tblshipmentitems (ShipmentID, ProductID, ExpectedQty) VALUES ('$sid', '$pid', '$qty')");
  echo '<script>alert("Item added to shipment.")</script>';
  echo "<script>window.location.href='warehouse.php'</script>";
}

if(isset($_GET['delete_item']) && $_GET['delete_item'] == 'yes')
{
  $item_id = $_GET['item_id'];
  mysqli_query($con, "DELETE FROM tblshipmentitems WHERE ID='$item_id'");
  echo '<script>alert("Item deleted.")</script>';
  echo "<script>window.location.href='warehouse.php?manage=yes&sid=".$_GET['sid']."'</script>";
}

if(isset($_POST['edit_item']))
{
  $item_id = $_POST['item_id'];
  $qty = $_POST['edit_qty'];
  mysqli_query($con, "UPDATE tblshipmentitems SET ExpectedQty='$qty' WHERE ID='$item_id'");
  echo '<script>alert("Item updated.")</script>';
  echo "<script>window.location.href='warehouse.php?manage=yes&sid=".$_POST['sid']."'</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Inventory Management System || Warehouse Transfer</title>
<?php include_once('includes/cs.php');?>
</head>
<body>

<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>

<div id="content">
<div id="content-header">
  <div id="breadcrumb"> <a href="dashboard.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="warehouse.php" class="current">Warehouse Transfer</a></div>
  <h1>Warehouse Transfer</h1>
</div>
<div class="container-fluid">
  <hr>
  <div class="row-fluid">
    <div class="span4">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-share"></i> </span>
          <h5>Transfer Products from Warehouse</h5>
        </div>
        <div class="widget-content nopadding">
          <form method="post" class="form-horizontal">
            <div class="control-group">
              <label class="control-label">Product :</label>
              <div class="controls">
                <select name="product" class="span11" required>
                  <option value="">Select Product</option>
                  <?php
                  $prod_query = mysqli_query($con, "SELECT tblproducts.ID, tblproducts.ProductName, tblwarehouse.WarehouseStock FROM tblproducts JOIN tblwarehouse ON tblproducts.ID = tblwarehouse.ProductID");
                  while($prod = mysqli_fetch_array($prod_query)) {
                    echo "<option value='".$prod['ID']."'>".$prod['ProductName']." (Warehouse: ".$prod['WarehouseStock'].")</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Quantity :</label>
              <div class="controls">
                <input type="number" class="span11" name="quantity" min="1" required />
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-success" name="transfer">Transfer to Main Office</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="span4">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-edit"></i> </span>
          <h5>Update Warehouse Stock</h5>
        </div>
        <div class="widget-content nopadding">
          <form method="post" class="form-horizontal">
            <div class="control-group">
              <label class="control-label">Product :</label>
              <div class="controls">
                <select name="stock_product" class="span11" required>
                  <option value="">Select Product</option>
                  <?php
                  $prod_query = mysqli_query($con, "SELECT tblproducts.ID, tblproducts.ProductName, tblwarehouse.WarehouseStock FROM tblproducts JOIN tblwarehouse ON tblproducts.ID = tblwarehouse.ProductID");
                  while($prod = mysqli_fetch_array($prod_query)) {
                    echo "<option value='".$prod['ID']."'>".$prod['ProductName']." (Current: ".$prod['WarehouseStock'].")</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">New Stock :</label>
              <div class="controls">
                <input type="number" class="span11" name="new_stock" min="0" required />
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary" name="update_stock">Update Stock</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="span4">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-plus"></i> </span>
          <h5>Create New Shipment</h5>
        </div>
        <div class="widget-content nopadding">
          <form method="post" class="form-horizontal">
            <div class="control-group">
              <label class="control-label">Shipment Name :</label>
              <div class="controls">
                <input type="text" class="span11" name="shipment_name" required />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Arrival Date :</label>
              <div class="controls">
                <input type="date" class="span11" name="arrival_date" required />
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-info" name="create_shipment">Create Shipment</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span6">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
          <h5>Warehouse Inventory</h5>
        </div>
        <div class="widget-content nopadding">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Product</th>
                <th>Warehouse Stock</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $wh_inv = mysqli_query($con, "SELECT tblproducts.ProductName, tblwarehouse.WarehouseStock FROM tblwarehouse JOIN tblproducts ON tblwarehouse.ProductID = tblproducts.ID");
              while($inv = mysqli_fetch_array($wh_inv)) {
                echo "<tr><td>".$inv['ProductName']."</td><td>".$inv['WarehouseStock']."</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="span6">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-truck"></i> </span>
          <h5>Shipments</h5>
        </div>
        <div class="widget-content nopadding">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Shipment Name</th>
                <th>Arrival Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $ship_query = mysqli_query($con, "SELECT * FROM tblshipments ORDER BY ArrivalDate");
              while($ship = mysqli_fetch_array($ship_query)) {
                echo "<tr><td>".$ship['ShipmentName']."</td><td>".$ship['ArrivalDate']."</td><td>".$ship['Status']."</td><td>";
                if($ship['Status'] == 'pending') {
                  echo "<a href='?manage=yes&sid=".$ship['ID']."' class='btn btn-mini btn-primary'>Manage Items</a> ";
                  echo "<a href='?receive=yes&sid=".$ship['ID']."' class='btn btn-mini btn-success'>Receive</a>";
                } else {
                  echo "Arrived";
                }
                echo "</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if(isset($_GET['manage']) && $_GET['manage'] == 'yes') { 
  $sid = $_GET['sid'];
  $ship_query = mysqli_query($con, "SELECT * FROM tblshipments WHERE ID='$sid'");
  $ship = mysqli_fetch_array($ship_query);
?>
<div class="row-fluid">
  <div class="span12">
    <div class="widget-box">
      <div class="widget-title"> <span class="icon"> <i class="icon-list"></i> </span>
        <h5>Manage Items for Shipment: <?php echo $ship['ShipmentName']; ?></h5>
      </div>
      <div class="widget-content nopadding">
        <form method="post" class="form-horizontal">
          <input type="hidden" name="sid" value="<?php echo $sid; ?>" />
          <div class="control-group">
            <label class="control-label">Product :</label>
            <div class="controls">
              <select name="item_product" class="span11" required>
                <option value="">Select Product</option>
                <?php
                $prod_query = mysqli_query($con, "SELECT ID, ProductName FROM tblproducts");
                while($prod = mysqli_fetch_array($prod_query)) {
                  echo "<option value='".$prod['ID']."'>".$prod['ProductName']."</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label">Expected Qty :</label>
            <div class="controls">
              <input type="number" class="span11" name="item_qty" min="1" required />
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-success" name="add_item">Add Item</button>
            <a href="warehouse.php" class="btn btn-default">Back</a>
          </div>
        </form>
        <?php if(isset($_GET['edit_item']) && $_GET['edit_item'] == 'yes') { 
          $item_id = $_GET['item_id'];
          $edit_query = mysqli_query($con, "SELECT tblshipmentitems.*, tblproducts.ProductName FROM tblshipmentitems JOIN tblproducts ON tblshipmentitems.ProductID = tblproducts.ID WHERE tblshipmentitems.ID='$item_id'");
          $edit_item = mysqli_fetch_array($edit_query);
        ?>
        <hr>
        <h5>Edit Item: <?php echo $edit_item['ProductName']; ?></h5>
        <form method="post" class="form-horizontal">
          <input type="hidden" name="item_id" value="<?php echo $item_id; ?>" />
          <input type="hidden" name="sid" value="<?php echo $sid; ?>" />
          <div class="control-group">
            <label class="control-label">Expected Qty :</label>
            <div class="controls">
              <input type="number" class="span11" name="edit_qty" value="<?php echo $edit_item['ExpectedQty']; ?>" min="1" required />
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-warning" name="edit_item">Update Item</button>
            <a href="?manage=yes&sid=<?php echo $sid; ?>" class="btn btn-default">Cancel</a>
          </div>
        </form>
        <?php } ?>
        <hr>
        <h5>Current Items</h5>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Product</th>
              <th>Expected Qty</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $item_query = mysqli_query($con, "SELECT tblshipmentitems.*, tblproducts.ProductName FROM tblshipmentitems JOIN tblproducts ON tblshipmentitems.ProductID = tblproducts.ID WHERE ShipmentID='$sid'");
            while($item = mysqli_fetch_array($item_query)) {
              echo "<tr><td>".$item['ProductName']."</td><td>".$item['ExpectedQty']."</td><td>";
              echo "<a href='?edit_item=yes&item_id=".$item['ID']."&sid=$sid' class='btn btn-mini btn-warning'>Edit</a> ";
              echo "<a href='?delete_item=yes&item_id=".$item['ID']."&sid=$sid' onclick='return confirm(\"Are you sure?\")' class='btn btn-mini btn-danger'>Delete</a>";
              echo "</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php if(isset($_GET['receive']) && $_GET['receive'] == 'yes') { 
  $sid = $_GET['sid'];
  $ship_query = mysqli_query($con, "SELECT * FROM tblshipments WHERE ID='$sid'");
  $ship = mysqli_fetch_array($ship_query);
?>
<div class="row-fluid">
  <div class="span12">
    <div class="widget-box">
      <div class="widget-title"> <span class="icon"> <i class="icon-check"></i> </span>
        <h5>Receive Shipment: <?php echo $ship['ShipmentName']; ?></h5>
      </div>
      <div class="widget-content nopadding">
        <form method="post" class="form-horizontal">
          <input type="hidden" name="sid" value="<?php echo $sid; ?>" />
          <?php
          $item_query = mysqli_query($con, "SELECT tblshipmentitems.*, tblproducts.ProductName FROM tblshipmentitems JOIN tblproducts ON tblshipmentitems.ProductID = tblproducts.ID WHERE ShipmentID='$sid'");
          while($item = mysqli_fetch_array($item_query)) {
            echo '<div class="control-group">
              <label class="control-label">'.$item['ProductName'].' (Expected: '.$item['ExpectedQty'].') :</label>
              <div class="controls">
                <input type="number" class="span11" name="received['.$item['ID'].']" min="0" required />
              </div>
            </div>';
          }
          ?>
          <div class="form-actions">
            <button type="submit" class="btn btn-success" name="receive_items">Confirm Receipt</button>
            <a href="warehouse.php" class="btn btn-default">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php if(isset($_GET['receive']) && $_GET['receive'] == 'yes') { 
  $inc_id = $_GET['id'];
  $inc_query = mysqli_query($con, "SELECT tblincoming.*, tblproducts.ProductName FROM tblincoming JOIN tblproducts ON tblincoming.ProductID = tblproducts.ID WHERE tblincoming.ID='$inc_id'");
  $inc = mysqli_fetch_array($inc_query);
?>
<div class="row-fluid">
  <div class="span12">
    <div class="widget-box">
      <div class="widget-title"> <span class="icon"> <i class="icon-check"></i> </span>
        <h5>Receive Shipment: <?php echo $inc['ProductName']; ?> (Expected: <?php echo $inc['ExpectedQty']; ?>)</h5>
      </div>
      <div class="widget-content nopadding">
        <form method="post" class="form-horizontal">
          <input type="hidden" name="inc_id" value="<?php echo $inc_id; ?>" />
          <div class="control-group">
            <label class="control-label">Actual Received Quantity :</label>
            <div class="controls">
              <input type="number" class="span11" name="received_qty" min="0" required />
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-success" name="receive">Confirm Receipt</button>
            <a href="warehouse.php" class="btn btn-default">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php include_once('includes/footer.php');?>
<?php include_once('includes/js.php');?>
</body>
</html>
<?php } ?>
