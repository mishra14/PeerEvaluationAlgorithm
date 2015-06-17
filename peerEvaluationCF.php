<!DOCTYPE HTML> 
<html>
<head>
</head>
<body> 
<?php 
error_reporting(E_ALL); 
ini_set("display_errors", 1); 
?>
<?php
	///echo phpinfo();
	session_start();
	$username=$_SESSION['username'];
	$id=$_SESSION['id'];
	print '<h2>CrowdFluttr Peer Evaluation Demo - '.$username.'/'.$id.' Home</h2>';
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
    $data = $_POST['contribution'];
	var_dump($data);
	$credListPost=$_SESSION['credList'];
	echo '<br> ';
	var_dump($credListPost);
	$sum=0;
	$sqlFirst="";
	$sqlSecond="";
	foreach($data as $key => $value)
	{
		$sum=$sum+$value;
		$sqlFirst=$sqlFirst.",".$key."_contribution";
		$sqlSecond=$sqlSecond.",".$value;
	}
	if($sum!=100)
	{
		echo '<br>The sum of all contributions total should be equal to 100 %';
	}
	else
	{
		$sql="insert into ".$id."_PE(username".$sqlFirst.")values ('$username'".$sqlSecond.")";
		if($conn->query($sql)==true)
		{
			$result=$result+array("UpdatePE"=>1);
			$sql="select ".$id."_PE.* from ".$id."_PE join (select username, max(ts) as ts from ".$id."_PE group by username)latestEntry on ".$id."_PE.ts=latestEntry.ts";
			$queryResult=$conn->query($sql);
			if($queryResult->num_rows > 0)
			{
				$sumPE=array();
				$listPE=array();
				$rowPE=array();
				$rowCount=0;
				while($row=$queryResult->fetch_assoc())
				{
					//echo '<br>';
					//var_dump($row);	
					$keyList=array_keys($row);
					echo '<br> '.$row['username'].' : '.$credListPost[$row['username']];
					for($i=3;$i<count($keyList);$i++)
					{
						$keySplit=explode("_", $keyList[$i]);
						$rowPE[$keySplit[0]]=$row[$keyList[$i]];
						
						if($rowCount>0)
						{	
							$sumPE[$keySplit[0]]=$sumPE[$keySplit[0]]+$row[$keyList[$i]]*$credListPost[$row['username']];
							//$sumPE[$keySplit[0]]=($sumPE[$keySplit[0]]*$rowCount + $row[$keyList[$i]])/($rowCount+1);				//old avg folrula
							echo '<br>'.$keySplit[0].' '.$sumPE[$keySplit[0]];
						}
						else
						{
							$sumPE[$keySplit[0]]=$row[$keyList[$i]]*$credListPost[$row['username']];
							//$sumPE[$keySplit[0]]=($row[$keyList[$i]]);												//old avg formula
							echo '<br> '.$keySplit[0].' '.$sumPE[$keySplit[0]];
						}
					}
					$listPE[$row['username']]=$rowPE;
					$rowCount++;
				}

			}
			//$avgPE=$sumPE/count($sumPE);
			echo '<br> ';
			var_dump($sumPE);
			echo '<br> ';
			var_dump($listPE);
			echo '<br>';
			//store the new values
			$result=$result+array("UpdateRelations"=>1);
			$sumDiff=array();
			foreach($sumPE as $evaluator => $value)
			{
				$cred=$credListPost[$evaluator];
				$diff=0;
				echo '<br>'.$evaluator;				
				foreach ($listPE[$evaluator] as $name => $contribution)
				{
					echo '<br>'.$name.' '.$sumPE[$name].' - '.$listPE[$evaluator][$name].' = '.abs($sumPE[$name]-$listPE[$evaluator][$name]);
					$diff+=abs($sumPE[$name]-$listPE[$evaluator][$name]);
				}
				$sumDiff+=array($evaluator=>$diff);
			}
			echo '<br>';
			var_dump($sumDiff);
			$sumDiffTotal=array_sum($sumDiff);
			$totalCred=0;
			foreach($sumPE as $evaluator => $value)
			{
				$totalCred=$totalCred+($sumDiffTotal/$sumDiff[$evaluator]);
			}
			foreach($sumPE as $evaluator => $value)
			{
				//$cred=$credListPost[$evaluator];

				$cred=(($sumDiffTotal/$sumDiff[$evaluator])/$totalCred);
				$cred=($cred<0)?0:$cred;
				//$cred=($cred>1)?1:$cred;
				$sql="update relations set cred=$cred, contribution=".$value." where username='$evaluator' AND projID='$id'";
				if($conn->query($sql)==false)
				{
					$result['UpdateRelations']=0;
				}
			}
		}
		else
		{
			$result=$result+array("UpdatePE"=>0);
		}
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
	$sql="select relations.username,relations.cred,relations.contribution, projects.projname from relations join projects on (relations.projID=projects.id) where relations.projID=".$id;
	$queryResult=$conn->query($sql);
	if($queryResult->num_rows > 0)
	{
		print '<form method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">';
		print '<table border="1">';
		print '<tr><td>Contributor</td><td>Current Credibility</td><td>Current Contribution</td><td>Update Contribution</td></tr>';
		$credList=array();
		while ($row = $queryResult->fetch_assoc())
		{	
			
			$credList=$credList+array($row['username']=>$row['cred']);
			print '<tr><td>'.$row['username'].'</td><td>'.$row['cred'].'</td><td>'.$row['contribution'].'</td>';
			print '<td><input type="number" min="0" max="100" name="contribution['.$row['username'].']" value=""> </td></tr>';
		}
		print '</table>';
		print '<input type="submit" name="submit" value="Submit">';
		print '</form>';
		$_SESSION['credList']=$credList;
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