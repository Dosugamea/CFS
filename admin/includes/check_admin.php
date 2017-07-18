<?php
session_start();
if(!isset($_SESSION['admin'])){
	header("HTTP/1.1 302 Found");
	header("Location: /admin/login.php");
	die();
}
?>