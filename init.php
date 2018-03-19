<?php
include('CURL.php');
$con = new mysqli('127.0.0.1','root','','test');
if (mysqli_connect_errno()){
	die('Unable to connect!'). mysqli_connect_error();
}
mysqli_set_charset($con, "utf8");
$curl=new CUrl();