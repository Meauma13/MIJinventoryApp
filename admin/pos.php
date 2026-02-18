<?php
session_start();
error_reporting(E_ALL);
include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid']==0)) {
  header('location:logout.php');
  } else{

// Handle barcode scan
if(isset($_POST['add_product'])){
  $barcode = $_POST['barcode'];
  $product_query = mysqli_query($con, "SELECT * FROM tblproducts WHERE Barcode='$barcode' AND Status='1'");
  echo '<script>console.log("Scanned Barcode: '.$barcode.'");</script>';
  if(mysqli_num_rows($product_query) > 0){
    $product = mysqli_fetch_array($product_query);
    // Add to session cart
    if(!isset($_SESSION['pos_cart'])){
      $_SESSION['pos_cart'] = array();
    }
    $pid = $product['ID'];
    if(isset($_SESSION['pos_cart'][$pid])){
      $_SESSION['pos_cart'][$pid]['qty'] += 1;
    } else {
      $_SESSION['pos_cart'][$pid] = array(
        'name' => $product['ProductName'],
        'price' => $product['Price'],
        'qty' => 1
      );
    }
    echo '<script>alert("Product added to cart.")</script>';
  } else {
    echo '<script>alert("Product not found.")</script>';
  }
}

// Handle manual add
if(isset($_POST['manual_add'])){
  $pid = (int)$_POST['product_id'];
  $qty = (int)$_POST['qty'];
  $product_query = mysqli_query($con, "SELECT * FROM tblproducts WHERE ID='$pid' AND Status='1'");
  $product = mysqli_fetch_array($product_query);
  if(!isset($_SESSION['pos_cart'])){
    $_SESSION['pos_cart'] = array();
  }
  if(isset($_SESSION['pos_cart'][$pid])){
    $_SESSION['pos_cart'][$pid]['qty'] += $qty;
  } else {
    $_SESSION['pos_cart'][$pid] = array(
      'name' => $product['ProductName'],
      'price' => $product['Price'],
      'qty' => $qty
    );
  }
}

// Remove from cart
if(isset($_GET['remove'])){
  $pid = $_GET['remove'];
  unset($_SESSION['pos_cart'][$pid]);
  header('location:pos.php');
}


  // Optimized Checkout with Transaction Support
if(isset($_POST['checkout'])){
  $billiningnum = mt_rand(100000000, 999999999);
  $customername = mysqli_real_escape_string($con, $_POST['customername'] ?: 'Walk-in');
  $mobilenumber = mysqli_real_escape_string($con, $_POST['mobilenumber'] ?: '');
  $modepayment = mysqli_real_escape_string($con, $_POST['modepayment']);
  
  // 1. Start Transaction
  mysqli_begin_transaction($con);

  try {
    foreach($_SESSION['pos_cart'] as $pid => $item){
      $qty = $item['qty'];

      // 2. Efficient Stock Check: Ensure we don't sell what we don't have
      $stock_check = mysqli_query($con, "SELECT Stock FROM tblproducts WHERE ID='$pid' FOR UPDATE");
      $current_stock = mysqli_fetch_array($stock_check)['Stock'];

      if($current_stock < $qty) {
        throw new Exception("Insufficient stock for: " . $item['name']);
      }

      // 3. Insert into tblcart
      mysqli_query($con, "INSERT INTO tblcart (ProductId, ProductQty, BillingId, IsCheckOut, CartDate) 
                          VALUES ('$pid', '$qty', '$billiningnum', '1', NOW())");

      // 4. Update Stock
      mysqli_query($con, "UPDATE tblproducts SET Stock = Stock - $qty WHERE ID='$pid'");
    }

    // 5. Insert Customer Record
    mysqli_query($con, "INSERT INTO tblcustomer (BillingNumber, CustomerName, MobileNumber, ModeofPayment) 
                        VALUES ('$billiningnum', '$customername', '$mobilenumber', '$modepayment')");

    // 6. If everything reached here without error, Commit to Database
    mysqli_commit($con);

    $_SESSION['invoiceid'] = $billiningnum;
    unset($_SESSION['pos_cart']);
    echo "<script>alert('Sale Completed Successfully!'); window.location.href='invoice.php';</script>";

  } catch (Exception $e) {
    // 7. If ANY error occurred, undo everything
    mysqli_rollback($con);
    $error_msg = $e->getMessage();
    echo "<script>alert('Error: $error_msg');</script>";
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Inventory Management System || POS</title>
<?php include_once('includes/cs.php');?>
<script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
</head>
<body>

<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>

<div id="content">
<div id="content-header">
  <div id="breadcrumb"> <a href="dashboard.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="pos.php" class="current">POS</a></div>
  <h1>Point of Sale</h1>
</div>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="span6">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-barcode"></i> </span>
          <h5>Barcode Scanner</h5>
        </div>
        <div class="widget-content nopadding">
          <div id="interactive" class="viewport"></div>
          <form method="post">
            <div class="control-group">
              <label class="control-label">Scanned Barcode:</label>
              <div class="controls">
                <input type="text" id="barcode_input" name="barcode" class="span11" readonly required/>
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-success" name="add_product">Add Product</button>
            </div>
          </form>
        </div>
      </div>
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-plus"></i> </span>
          <h5>Manual Add</h5>
        </div>
        <div class="widget-content nopadding">
          <form method="post">
            <div class="control-group">
              <label class="control-label">Product:</label>
              <div class="controls">
                <select name="product_id" class="span11" required>
                  <option value="">Select Product</option>
                  <?php
                  $prod_query = mysqli_query($con, "SELECT ID, ProductName FROM tblproducts WHERE Status='1' ORDER BY ProductName ASC");
                  while($prod = mysqli_fetch_array($prod_query)) {
                    echo "<option value='".$prod['ID']."'>".$prod['ProductName']."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Quantity:</label>
              <div class="controls">
                <input type="number" name="qty" class="span11" min="1" value="1" required />
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary" name="manual_add">Add to Cart</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="span6">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-shopping-cart"></i> </span>
          <h5>Cart</h5>
        </div>
        <div class="widget-content nopadding">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $total = 0;
              if(isset($_SESSION['pos_cart']) && count($_SESSION['pos_cart']) > 0){
                foreach($_SESSION['pos_cart'] as $pid => $item){
                  $subtotal = $item['qty'] * $item['price'];
                  $total += $subtotal;
                  echo "<tr><td>".$item['name']."</td><td>".$item['qty']."</td><td>".$item['price']."</td><td>$subtotal</td><td><a href='?remove=$pid' class='btn btn-mini btn-danger'>Remove</a></td></tr>";
                }
              } else {
                echo "<tr><td colspan='5'>Cart is empty</td></tr>";
              }
              ?>
            </tbody>
          </table>
          <div class="form-actions">
            <strong>Total: <?php echo $total; ?></strong>
          </div>
        </div>
      </div>
      <?php if(isset($_SESSION['pos_cart']) && count($_SESSION['pos_cart']) > 0){ ?>
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-check"></i> </span>
          <h5>Checkout</h5>
        </div>
        <div class="widget-content nopadding">
          <form method="post">
            <div class="control-group">
              <label class="control-label">Customer Name (optional):</label>
              <div class="controls">
                <input type="text" name="customername" class="span11" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Mobile Number (optional):</label>
              <div class="controls">
                <input type="text" name="mobilenumber" class="span11" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Mode of Payment:</label>
              <div class="controls">
                <select name="modepayment" class="span11" required>
                  <option value="Cash">Cash</option>
                  <option value="Card">Card</option>
                  <option value="Online">Online</option>
                </select>
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-success" name="checkout">Checkout</button>
            </div>
          </form>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
</div>

<?php include_once('includes/footer.php');?>
<?php include_once('includes/js.php');?>

<script>
Quagga.init({
  inputStream : {
    name : "Live",
    type : "LiveStream",
    target: document.querySelector('#interactive'),
    constraints: {
      width: 640,
      height: 480,
      facingMode: "environment"
    }
  },
  decoder : {
    readers : ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "upc_reader"]
  }
}, function(err) {
  if (err) {
    console.log(err);
    return;
  }
  Quagga.start();
});


var isScanning = false;
Quagga.onDetected(function(result) {
  if (isScanning) return; // Prevent multiple scans at once
  
  isScanning = true;
  var code = result.codeResult.code;
  document.getElementById('barcode_input').value = code;
  
  // Auto-click the Add button
  document.querySelector("button[name='add_product']").click();

  // Wait 2 seconds before allowing the next scan
  setTimeout(function() { isScanning = false; }, 2000);
});

</script>

</body>
</html>
<?php } ?>