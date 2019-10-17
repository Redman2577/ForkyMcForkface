<?php
// Sets the error variables to the value 1
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//if the request method is post then it will check to see if there are no issues connecting to the database. If there aren't any, a command will run that deletes an object from the table based on the given parameter. If the command fails or connection to the database fails, a delete variable will be set to "TRUE".
if ($_SERVER['REQUEST_METHOD'] == "POST") {

	$listID = $_POST['listID'];
	
	//build url for api
	$url="http://3.229.178.148/api/task.php?listID=$listID";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	//curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch); //body of the response
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); //gets the status code
	curl_close($ch);
	
	//if(status code = 204)	
	if($httpcode == 204){
		header("Location: index.php");		
	}else{
		//api errors (not http 204)
		header("Location: index.php?error=delete");
	}
}


?>