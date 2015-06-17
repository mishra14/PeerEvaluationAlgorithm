<!DOCTYPE HTML> 
<html>
<head>
</head>
<body> 

<?php
// define variables and set to empty values
$username = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
   $username = test_input($_POST["name"]);
   $password = test_input($_POST["password"]);
   $conn =new mysqli("localhost","root","mishra2014","CrowdFluttr");
	// Check connection
	if ($conn->connect_error) 
	{
		$result=array("DBConnection"=>0);
		print json_encode($result);
		//die("Connection failed: " . $conn->connect_error);
	}
	else
	{
		$result=array("DBConnection"=>1);
	}
	$sql="select username from user where username = '$username' and pass = '$password'";
	$queryResult=$conn->query($sql);
	//echo '<br>'.$queryResult->num_rows;
	if($queryResult->num_rows > 0)
	{
		$result=$result+array("validate"=>1);
	}
	else
	{
		$result=$result+array("validate"=>0);
	}
	print json_encode($result);
	$queryResult->close();
	$conn->close();
	if($result["validate"]==1)
	{
		session_start();
		$_SESSION['username']=$username;
		header("Location: http://mishra14.ddns.net/userHomeCF.php");
	}
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<h2>CrowdFluttr Peer Evaluation Demo - Login</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   Name: <input type="text" name="name">
   <br><br>
   Password: <input type="password" name="password">
   <br><br>
   <input type="submit" name="submit" value="Submit"> 
</form>
</body>
</html>