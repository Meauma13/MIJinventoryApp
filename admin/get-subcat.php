<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['imsaid']==0)) {
  header('location:logout.php');
  } else{

if(!empty($_POST["catid"])) {
    $catid = $_POST["catid"];
    // Always use prepared statements or at least escape your data
    $query = mysqli_query($con, "SELECT * FROM tblsubcategory WHERE CategoryID = '$catid'");
    
    echo '<option value="">Select Sub Category</option>';
    while($row = mysqli_fetch_array($query)) {
        echo '<option value="'.$row['ID'].'">'.$row['Subcategoryname'].'</option>';
    }
}} ?>