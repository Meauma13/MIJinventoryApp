<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid']==0)) {
  header('location:logout.php');
  } else{

if(isset($_POST['submit']))
{
  $fromdate = $_POST['fromdate'];
  $todate = $_POST['todate'];
  $user = $_POST['user'];
  $action = $_POST['action'];
  $table = $_POST['table'];
  $recordid = $_POST['recordid'];

  $where = "1=1";
  if($fromdate) $where .= " AND DATE(a.Timestamp) >= '$fromdate'";
  if($todate) $where .= " AND DATE(a.Timestamp) <= '$todate'";
  if($user != 'all') $where .= " AND a.UserID = '$user'";
  if($action != 'all') $where .= " AND a.Action = '$action'";
  if($table != 'all') $where .= " AND a.TableName = '$table'";
  if($recordid) $where .= " AND a.RecordID = '$recordid'";

  $query = mysqli_query($con, "SELECT a.*, ad.AdminName, 
    CASE 
      WHEN a.TableName = 'tblproducts' THEN (SELECT ProductName FROM tblproducts WHERE ID = a.RecordID)
      WHEN a.TableName = 'tblcategory' THEN (SELECT CategoryName FROM tblcategory WHERE ID = a.RecordID)
      WHEN a.TableName = 'tblsubcategory' THEN (SELECT SubCategoryname FROM tblsubcategory WHERE ID = a.RecordID)
      WHEN a.TableName = 'tblbrand' THEN (SELECT BrandName FROM tblbrand WHERE ID = a.RecordID)
      ELSE 'N/A'
    END AS RecordName
    FROM tblauditlog a JOIN tbladmin ad ON a.UserID = ad.ID WHERE $where ORDER BY a.Timestamp DESC");
  $results = mysqli_fetch_all($query, MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Inventory Management System || Audit Report</title>
<?php include_once('includes/cs.php');?>
</head>
<body>

<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>

<div id="content">
<div id="content-header">
  <div id="breadcrumb"> <a href="dashboard.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#" class="current">Audit Report</a></div>
  <h1>Audit Report</h1>
</div>
<div class="container-fluid">
  <hr>
  <div class="row-fluid">
    <div class="span12">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
          <h5>Audit Report Filters</h5>
        </div>
        <div class="widget-content nopadding">
          <form method="post" class="form-horizontal">
            <div class="control-group">
              <label class="control-label">From Date :</label>
              <div class="controls">
                <input type="date" class="span11" name="fromdate" value="<?php echo isset($_POST['fromdate']) ? $_POST['fromdate'] : ''; ?>" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">To Date :</label>
              <div class="controls">
                <input type="date" class="span11" name="todate" value="<?php echo isset($_POST['todate']) ? $_POST['todate'] : ''; ?>" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">User :</label>
              <div class="controls">
                <select name="user" class="span11">
                  <option value="all">All Users</option>
                  <?php
                  $userquery = mysqli_query($con, "SELECT * FROM tbladmin");
                  while($row = mysqli_fetch_array($userquery)) {
                    $selected = (isset($_POST['user']) && $_POST['user'] == $row['ID']) ? 'selected' : '';
                    echo "<option value='".$row['ID']."' $selected>".$row['AdminName']."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Action :</label>
              <div class="controls">
                <select name="action" class="span11">
                  <option value="all" <?php echo (isset($_POST['action']) && $_POST['action']=='all') ? 'selected' : ''; ?>>All Actions</option>
                  <option value="CREATE" <?php echo (isset($_POST['action']) && $_POST['action']=='CREATE') ? 'selected' : ''; ?>>CREATE</option>
                  <option value="UPDATE" <?php echo (isset($_POST['action']) && $_POST['action']=='UPDATE') ? 'selected' : ''; ?>>UPDATE</option>
                  <option value="DELETE" <?php echo (isset($_POST['action']) && $_POST['action']=='DELETE') ? 'selected' : ''; ?>>DELETE</option>
                </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Table :</label>
              <div class="controls">
                <select name="table" class="span11">
                  <option value="all" <?php echo (isset($_POST['table']) && $_POST['table']=='all') ? 'selected' : ''; ?>>All Tables</option>
                  <option value="tblproducts" <?php echo (isset($_POST['table']) && $_POST['table']=='tblproducts') ? 'selected' : ''; ?>>Products</option>
                  <option value="tblcategory" <?php echo (isset($_POST['table']) && $_POST['table']=='tblcategory') ? 'selected' : ''; ?>>Categories</option>
                  <option value="tblsubcategory" <?php echo (isset($_POST['table']) && $_POST['table']=='tblsubcategory') ? 'selected' : ''; ?>>Subcategories</option>
                  <option value="tblbrand" <?php echo (isset($_POST['table']) && $_POST['table']=='tblbrand') ? 'selected' : ''; ?>>Brands</option>
                </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Record ID (optional) :</label>
              <div class="controls">
                <input type="number" class="span11" name="recordid" value="<?php echo isset($_POST['recordid']) ? $_POST['recordid'] : ''; ?>" />
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-success" name="submit">Generate Report</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php if(isset($results) && count($results) > 0) { ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="widget-box">
        <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
          <h5>Audit Log Results</h5>
        </div>
        <div class="widget-content nopadding">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>User</th>
                <th>Action</th>
                <th>Table</th>
                <th>Record ID</th>
                <th>Record Name</th>
                <th>Timestamp</th>
                <th>Details</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($results as $row) { ?>
              <tr>
                <td><?php echo $row['AdminName']; ?></td>
                <td><?php echo $row['Action']; ?></td>
                <td><?php echo $row['TableName']; ?></td>
                <td><?php echo $row['RecordID']; ?></td>
                <td><?php echo $row['RecordName']; ?></td>
                <td><?php echo $row['Timestamp']; ?></td>
                <td><?php echo $row['Details'] ? $row['Details'] : 'N/A'; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php } elseif(isset($_POST['submit'])) { ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="alert alert-info">No records found matching the criteria.</div>
    </div>
  </div>
  <?php } ?>
</div>
</div>

<?php include_once('includes/footer.php');?>
<?php include_once('includes/js.php');?>
</body>
</html>
<?php } ?>