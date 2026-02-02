<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid']==0) || $_SESSION['role'] != 'ceo') {
  header('location:logout.php');
  } else{

// Get Admin Name
$admin_query = mysqli_query($con, "SELECT AdminName FROM tbladmin WHERE ID='".$_SESSION['imsaid']."'");
$admin = mysqli_fetch_array($admin_query);
$admin_name = $admin['AdminName'];

// Total Stock Value
$stock_value_query = mysqli_query($con, "SELECT SUM(Stock * Price) as total_value FROM tblproducts WHERE Status='1'");
$stock_value = mysqli_fetch_array($stock_value_query)['total_value'];

// Warehouse Stock Value
$wh_value_query = mysqli_query($con, "SELECT SUM(tblwarehouse.WarehouseStock * tblproducts.Price) as wh_value FROM tblwarehouse JOIN tblproducts ON tblwarehouse.ProductID = tblproducts.ID");
$wh_value = mysqli_fetch_array($wh_value_query)['wh_value'];

// Today's Sales
$today_sales_query = mysqli_query($con, "SELECT SUM(tblcart.ProductQty * tblproducts.Price) as today_sales FROM tblcart JOIN tblproducts ON tblcart.ProductId = tblproducts.ID WHERE DATE(CartDate) = CURDATE() AND IsCheckOut='1'");
$today_sales = mysqli_fetch_array($today_sales_query)['today_sales'] ?: 0;

// This Week Sales
$week_sales_query = mysqli_query($con, "SELECT SUM(tblcart.ProductQty * tblproducts.Price) as week_sales FROM tblcart JOIN tblproducts ON tblcart.ProductId = tblproducts.ID WHERE YEARWEEK(CartDate) = YEARWEEK(CURDATE()) AND IsCheckOut='1'");
$week_sales = mysqli_fetch_array($week_sales_query)['week_sales'] ?: 0;

// This Month Sales
$month_sales_query = mysqli_query($con, "SELECT SUM(tblcart.ProductQty * tblproducts.Price) as month_sales FROM tblcart JOIN tblproducts ON tblcart.ProductId = tblproducts.ID WHERE MONTH(CartDate) = MONTH(CURDATE()) AND YEAR(CartDate) = YEAR(CURDATE()) AND IsCheckOut='1'");
$month_sales = mysqli_fetch_array($month_sales_query)['month_sales'] ?: 0;

// Total Products
$total_products = mysqli_num_rows(mysqli_query($con, "SELECT * FROM tblproducts WHERE Status='1'"));

// Low Stock Products
$low_stock = mysqli_num_rows(mysqli_query($con, "SELECT * FROM tblproducts WHERE Stock < 10 AND Status='1'"));

// Pending Shipments
$pending_shipments = mysqli_num_rows(mysqli_query($con, "SELECT * FROM tblshipments WHERE Status='pending'"));

// Recent Sales
$recent_sales = mysqli_query($con, "SELECT tblcustomer.CustomerName, tblcustomer.BillingNumber, SUM(tblcart.ProductQty * tblproducts.Price) as total, tblcustomer.ModeofPayment FROM tblcart JOIN tblproducts ON tblcart.ProductId = tblproducts.ID JOIN tblcustomer ON tblcart.BillingId = tblcustomer.BillingNumber WHERE tblcart.IsCheckOut='1' GROUP BY tblcart.BillingId ORDER BY tblcart.ID DESC LIMIT 5");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Inventory Management System || CEO Dashboard</title>
<?php include_once('includes/cs.php');?>
</head>
<body>

<div id="content">
<div id="content-header">
  <div id="breadcrumb"> <a href="logout.php" class="tip-bottom"><i class="icon-off"></i> Logout</a></div>
  <h1>Welcome <?php echo $admin_name; ?> - CEO Dashboard</h1>
</div>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="span12">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-dashboard"></i> </span>
          <h5>Business Overview</h5>
        </div>
        <div class="widget-content">
          <div class="row-fluid">
            <div class="span3">
              <div class="stats-box">
                <h3>Total Stock Value</h3>
                <p>₦<?php echo number_format($stock_value, 2); ?></p>
              </div>
            </div>
            <div class="span3">
              <div class="stats-box">
                <h3>Warehouse Value</h3>
                <p>₦<?php echo number_format($wh_value, 2); ?></p>
              </div>
            </div>
            <div class="span3">
              <div class="stats-box">
                <h3>Today's Sales</h3>
                <p>₦<?php echo number_format($today_sales, 2); ?></p>
              </div>
            </div>
            <div class="span3">
              <div class="stats-box">
                <h3>This Month Sales</h3>
                <p>₦<?php echo number_format($month_sales, 2); ?></p>
              </div>
            </div>
          </div>
          <div class="row-fluid">
            <div class="span3">
              <div class="stats-box">
                <h3>Total Products</h3>
                <p><?php echo $total_products; ?></p>
              </div>
            </div>
            <div class="span3">
              <div class="stats-box">
                <h3>Low Stock Items</h3>
                <p><?php echo $low_stock; ?></p>
              </div>
            </div>
            <div class="span3">
              <div class="stats-box">
                <h3>Pending Shipments</h3>
                <p><?php echo $pending_shipments; ?></p>
              </div>
            </div>
            <div class="span3">
              <div class="stats-box">
                <h3>This Week Sales</h3>
                <p>₦<?php echo number_format($week_sales, 2); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row-fluid">
    <div class="span6">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-shopping-cart"></i> </span>
          <h5>Recent Sales</h5>
        </div>
        <div class="widget-content nopadding">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Customer</th>
                <th>Invoice</th>
                <th>Total</th>
                <th>Payment</th>
              </tr>
            </thead>
            <tbody>
              <?php while($sale = mysqli_fetch_array($recent_sales)) { ?>
              <tr>
                <td><?php echo $sale['CustomerName'] ?: 'Walk-in'; ?></td>
                <td><?php echo $sale['BillingNumber']; ?></td>
                <td>₦<?php echo number_format($sale['total'], 2); ?></td>
                <td><?php echo $sale['ModeofPayment']; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="span6">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-warning-sign"></i> </span>
          <h5>Low Stock Alerts</h5>
        </div>
        <div class="widget-content nopadding">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Product</th>
                <th>Current Stock</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $low_stock_items = mysqli_query($con, "SELECT ProductName, Stock FROM tblproducts WHERE Stock < 10 AND Status='1' LIMIT 10");
              while($item = mysqli_fetch_array($low_stock_items)) {
                echo "<tr><td>".$item['ProductName']."</td><td>".$item['Stock']."</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>


<?php include_once('includes/js.php');?>
</body>
</html>
<?php } ?>