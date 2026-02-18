<?php
session_start();
error_reporting(E_ALL);
include('includes/dbconnection.php');

if (strlen($_SESSION['imsaid']==0)) {
  header('location:logout.php');
  exit();
  } else{


// 1. DELETE PRODUCT FROM CART (Security: Use Prepared Statements)
    if (isset($_GET['delid'])) {
        $rid = intval($_GET['delid']);
        $stmt = $con->prepare("DELETE FROM tblcart WHERE ID = ?");
        $stmt->bind_param("i", $rid);
        if ($stmt->execute()) {
            echo "<script>alert('Data deleted'); window.location.href = 'cart.php'</script>";
        }
        $stmt->close();
    }

    // 2. CHECKOUT PROCESS (Security: Transactions + Prepared Statements)
    if (isset($_POST['submit'])) {
        $custname = mysqli_real_escape_string($con, $_POST['customername']);
        $custmobilenum = mysqli_real_escape_string($con, $_POST['mobilenumber']);
        $modepayment = mysqli_real_escape_string($con, $_POST['modepayment']);
        $billingnum = mt_rand(100000000, 999999999);

        // START TRANSACTION
        mysqli_begin_transaction($con);

        try {
            // A. Fetch current cart items to verify stock (FOR UPDATE locks the rows)
            $cart_res = mysqli_query($con, "SELECT tblcart.ProductId, tblcart.ProductQty, tblproducts.ProductName, tblproducts.Stock 
                                            FROM tblcart 
                                            JOIN tblproducts ON tblcart.ProductId = tblproducts.ID 
                                            WHERE tblcart.IsCheckOut = 0 FOR UPDATE");

            if (mysqli_num_rows($cart_res) == 0) {
                throw new Exception("Cart is empty.");
            }

            while ($item = mysqli_fetch_array($cart_res)) {
                $pid = $item['ProductId'];
                $qty = $item['ProductQty'];
                $pname = $item['ProductName'];
                $current_stock = $item['Stock'];

                // B. Check if sufficient stock exists
                if ($current_stock < $qty) {
                  throw new Exception("Insufficient stock for $pname. Available: $current_stock");
                }

                // C. Update Product Stock
                mysqli_query($con, "UPDATE tblproducts SET Stock = Stock - $qty WHERE ID = '$pid'");
            }

            // D. Mark Cart as Checked Out
            mysqli_query($con, "UPDATE tblcart SET BillingId='$billingnum', IsCheckOut=1 WHERE IsCheckOut=0");

            // E. Insert Customer Record
            mysqli_query($con, "INSERT INTO tblcustomer(BillingNumber, CustomerName, MobileNumber, ModeofPayment) 
                                VALUES('$billingnum', '$custname', '$custmobilenum', '$modepayment')");

            // COMMIT ALL CHANGES
            mysqli_commit($con);
            
            $_SESSION['invoiceid'] = $billingnum;
            echo "<script>alert('Invoice created. Billing No: $billingnum'); window.location.href='invoice.php';</script>";

        } catch (Exception $e) {
            // ROLLBACK if any step fails
            mysqli_rollback($con);
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Inventory Management System || Cart</title>
    <?php include_once('includes/cs.php'); ?>
</head>
<body>
<?php include_once('includes/header.php'); ?>
<?php include_once('includes/sidebar.php'); ?>

<div id="content">
    <div id="content-header">
      <div id="breadcrumb"> <a href="dashboard.php" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="cart.php" class="current">Cart</a> </div>
        <h1>Products Cart</h1>
    </div>
    <div class="container-fluid">
        <hr>
        <div class="row-fluid">
            <div class="span12">
                <!-- Customer Information Form -->
                <form method="post" class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label">Customer Name :</label>
                        <div class="controls"><input type="text" class="span11" name="customername" required /></div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Mobile Number :</label>
                        <div class="controls"><input type="text" class="span11" name="mobilenumber" required maxlength="10" pattern="[0-9]+" /></div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Payment Mode :</label>
                        <div class="controls">
                          <input type="radio" name="modepayment" value="Cash" checked> Cash
                            <input type="radio" name="modepayment" value="Card"> Card
                        </div>
                    </div>
                    <div class="text-center"><button class="btn btn-primary" type="submit" name="submit">Checkout</button></div>
                </form>

                <!-- Cart Display Table -->
                <div class="widget-box">
                    <div class="widget-title"><span class="icon"><i class="icon-th"></i></span><h5>Cart Items</h5></div>
                    <div class="widget-content nopadding">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Selling Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            <tbody>
                                <?php
                                $ret = mysqli_query($con, "SELECT c.ID as cid, p.ProductName, p.SellingPrice, c.ProductQty 
                                                           FROM tblcart c JOIN tblproducts p ON p.ID = c.ProductId 
                                                           WHERE c.IsCheckOut='0'");
                                $cnt = 1;
                                $gtotal = 0; // Initialize to avoid PHP notices
                                while ($row = mysqli_fetch_array($ret)) {
                                    $subtotal = $row['ProductQty'] * $row['SellingPrice'];
                                    $gtotal += $subtotal;
                                ?>
                                <tr>
                                    <td><?php echo $cnt++; ?></td>
                                    <td><?php echo $row['ProductName']; ?></td>
                                    <td><?php echo $row['ProductQty']; ?></td>
                                    <td><?php echo number_format($row['SellingPrice'], 2); ?></td>
                                    <td><?php echo number_format($subtotal, 2); ?></td>
                                    <td><a href="cart.php?delid=<?php echo $row['cid']; ?>" onclick="return confirm('Remove item?');"><i class="icon-trash"></i></a></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                  <th colspan="4" style="text-align:right">Grand Total:</th>
                                    <th colspan="2" style="color:red"><?php echo number_format($gtotal, 2); ?></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Footer-part-->
<?php include_once('includes/footer.php');?>
<!--end-Footer-part-->

<script src="js/jquery.min.js"></script> 
<script src="js/jquery.ui.custom.js"></script> 
<script src="js/bootstrap.min.js"></script> 
<script src="js/jquery.uniform.js"></script> 
<script src="js/select2.min.js"></script> 
<script src="js/jquery.dataTables.min.js"></script> 
<script src="js/matrix.js"></script> 
<script src="js/matrix.tables.js"></script>
</body>
</html>
<?php } ?>