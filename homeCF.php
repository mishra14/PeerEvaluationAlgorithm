<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	session_start();
	echo 'CrowdFluttr HomePage - Testing FB Login';
	if(isset($_SESSION['fbID']))
	{
		echo '<br>';
		echo 'Facebook Login successful';
		echo '<br>';
		echo $_SESSION['fbID'];
		echo '<br>';
		echo $_SESSION['fbFullName'];
		echo '<br>';
		echo $_SESSION['email'];
		echo '<br>';
	}
	else
	{
		echo 'Facebook Login Not successful';
	}
?>
