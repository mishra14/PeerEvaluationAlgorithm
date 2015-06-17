<!DOCTYPE HTML> 
<html>
<head>
</head>
<body> 

<?php
// define variables and set to empty values
$projectName ="";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
   $projectName = test_input($_POST["name"]);
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
	$sql = " INSERT INTO projects (projname) VALUES ('$projectName')";
	if($conn->query($sql)==true)
	{
		$result=$result+array("ProjectCreation"=>1);
		$sql = "SELECT id from projects ORDER BY id DESC LIMIT 1";
		$queryResult=$conn->query($sql);
		if($queryResult->num_rows != 1)
		{
			echo '<br>More/Less than 1 rows';
			var_dump($queryResult);
		}
		else
		{
			$row=$queryResult->fetch_assoc();
			$sql = " CREATE TABLE ".$row['id']."_PE ( id int auto_increment, ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP, username varchar(30) NOT NULL, PRIMARY key(id) )";
			if($conn->query($sql)==true)
			{
				$result=$result+array("ProjectPECreation"=>1);
			}
		}
	}
	else
	{
		$result=$result+array("ProjectCreation"=>0);
		$result=$result+array("SQLError"=>$conn->error." in ".$sql);
	}
	print json_encode($result);
	$conn->close();
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<h2>CrowdFluttr Peer Evaluation Demo - Create Project</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   Project Name: <input type="text" name="name">
   <br><br>
   <input type="submit" name="submit" value="Submit"> 
</form>
</body>
</html>