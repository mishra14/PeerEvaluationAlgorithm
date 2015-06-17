<!DOCTYPE HTML> 
<html>
<head>
</head>
<body> 

<?php
	session_start();
	$username=$_SESSION["username"];
	print '<h2>CrowdFluttr Peer Evaluation Demo - '.$username.' Home</h2>';
	$conn =new mysqli("localhost","root","mishra2014","CrowdFluttr");
	// Check connection
	if ($conn->connect_error) 
	{
		$result=array("DBConnection"=>0);
		die("Connection failed: " . $conn->connect_error);
	} 
	else
	{
		$result=array("DBConnection"=>1);
	}
	
	
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $data = $_POST['submit'];
	$id=key($data);
	$action=$data[$id];
	echo $id;
	echo $action;
	if($action=="Join")
	{
		$sql="insert into relations(projID, username, cred, contribution) values(".$id.",'".$username."',1.0,0.0)";
		if($conn->query($sql)==true)
		{
			$result=$result+array("ProjectJoin"=>1);
			$sql="ALTER TABLE ".$id."_PE add ".$username."_contribution float default 0.0";
			if($conn->query($sql)==true)
			{
				$result=$result+array("PEJoin"=>1);
			}
			else
			{
				$result=$result+array("PEJoin"=>0);
			}
		}
		else
		{
			$result=$result+array("ProjectJoin"=>0);
			$result=$result+array("SQLError"=>$conn->error." in ".$sql);
		}
	}
	if($action=="View")
	{
		//open PE for project 
		$_SESSION['username']=$username;
		$_SESSION['id']=$id;
		header("Location: http://mishra14.ddns.net/peerEvaluationCF.php");
	}
	$result=$result+array("ProjectID"=>$id);
}

function test_input($data) 
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
	
	
	
	
	$sql="(SELECT projects.projname, projects.id, relations.username
                 FROM projects
                 JOIN relations ON relations.projID = projects.id
                 WHERE relations.username='".$username."')
              UNION ALL
                (SELECT projects.projname, projects.id , projects.projname
                 FROM projects
                 WHERE projects.id NOT IN
                     (SELECT projects.id
                      FROM projects
                      JOIN relations ON relations.projID = projects.id
                      WHERE relations.username='".$username."'))";
	$queryResult=$conn->query($sql);
	if($queryResult->num_rows > 0)
	{
		print '<form method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">';
		print '<table border="1">';
		print '<tr><td>Project Name</td><td>Project ID</td><td>Status</td></tr>';
		while ($row = $queryResult->fetch_assoc())
		{
			print '<tr><td>'.$row['projname'].'</td><td>'.$row['id'].'</td>';
			if($row['username']==$username)
			{
				print '<td><input type="submit" name="submit['.$row['id'].']" value="View"> </td></tr>';
			}
			else
			{
				print '<td><input type="submit" name="submit['.$row['id'].']" value="Join"> </td></tr>';
			}
			//print '<input type=”hidden” name=”ProjectID” value=”'.$row['id'].'”>';
		}
		print '</table>';
		print '</form>';
		$result=$result+array("NoProject"=>0);
	}
	else
	{
		$result=$result+array("NoProject"=>1);
	}
	$queryResult->close();
	$conn->close();
	print json_encode($result);
?>
</body>
</html>