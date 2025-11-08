<?php 
if(!isset($_SESSION["user_id"])){
    header("Location: /moviebooking/auth/login.php");
 }
?>