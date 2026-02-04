<?php
session_start();
//error_reporting(0);
error_reporting(E_ALL);
include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid']==0)) {
  header('location:logout.php');
  } else{
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Inventory Management System || Manage Sub Category</title>
<?php include_once('includes/cs.php');?>
</head>
<body>

<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>


<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="dashboard.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="manage-subcategory.php" class="current">Manage Sub Category</a> </div>
    <h1>Manage Sub Category</h1>
  </div>
  <div class="container-fluid">
    <hr>
    <div class="row-fluid">
      <div class="span12">
        
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
            <h5>Manage Category</h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>S.NO</th>
                  <th>Category Name</th>
                  <th>Sub Category Name</th>
                  <th>Status</th>
                  <th>Creation Date</th>
                  <th>Action</th>
                  
                </tr>
              </thead>
              <tbody>
                <?php
$ret=mysqli_query($con,"select tblcategory.CategoryName,tblsubcategory.ID as sid,tblsubcategory.SubCategoryname,tblsubcategory.SubcategoryCode,tblsubcategory.CreationDate from tblsubcategory join tblcategory on tblcategory.ID=tblsubcategory.CategoryId");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {

?>
                <tr class="gradeX">
                  <td style="text-align: center;"><?php echo $cnt;?></td>
                  <td style="text-align: center;"><?php  echo $row['CategoryName'];?></td>
                  <td style="text-align: center;"><?php  echo $row['SubCategoryname'];?></td>
                  <?php if($row['SubcategoryCode']=="1"){ ?>

                     <td style="text-align: center;"><?php echo "Active"; ?></td>
<?php } else { ?>    <td style="text-align: center;"><?php echo "Inactive"; ?>
                  </td>
                  <?php } ?>
                  <td style="text-align: center;"><?php  echo $row['CreationDate'];?></td>
                  <td class="center" style="text-align: center;"><a href="editsubcategory.php?scid=<?php echo $row['sid'];?>"><i class=" icon-edit"></i></a></td>
                </tr>
                <?php 
$cnt=$cnt+1;
}?> 
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