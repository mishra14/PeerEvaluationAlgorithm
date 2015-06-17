<?php
	ob_start();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	session_start();
	//header('location : http://mishra14.ddns.net/homeCF.php');
	echo 'CrowdFluttr Facebook Login Test<br>';
	require_once 'autoload.php';

	use Facebook\FacebookSession;
	use Facebook\FacebookRedirectLoginHelper;
	use Facebook\FacebookRequest;
	use Facebook\FacebookResponse;
	use Facebook\FacebookSDKException;
	use Facebook\FacebookRequestException;
	use Facebook\FacebookAuthorizationException;
	use Facebook\GraphObject;
	use Facebook\Entities\AccessToken;
	use Facebook\HttpClients\FacebookCurlHttpClient;
	use Facebook\HttpClients\FacebookHttpable;

	FacebookSession::setDefaultApplication('327791730743747','c3e683e63343843d9d9ba1d453707d3d');

	$helper=new FacebookRedirectLoginHelper('http://mishra14.ddns.net/facebookLoginDemoCF.php');

	try
	{
		$session=$helper->getSessionFromRedirect();
	}
	catch (FacebookRequestException $e)
	{
		echo $e;
	}
	catch (Exception $e)
	{
		echo $e;
	}
	if(isset($session))
	{
		$request= new FacebookRequest($session,'GET','/me');
		$response=$request->execute();
		$graphObject=$response->getGraphObject();
		$fbID=$graphObject->getProperty('id');
		$fbFullName=$graphObject->getProperty('name');
		$email=$graphObject->getProperty('mail');
		$_SESSION['fbID']=$fbID;
		$_SESSION['fbFullName']=$fbFullName;
		$_SESSION['email']=$email;
		echo '<br>';
		echo ($fbID);
		echo '<br>';
		echo ($fbFullName);
		echo '<br>';
		echo ($email);

	}
	else
	{
		echo '<a href="' . $helper->getLoginUrl() . '">Login</a>';
	}
	ob_end_flush();
?>