<?php
// Sets the display error variables to the value 1
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//if the request method is post, then creates a variable equal to the listID that was editted. then completes a series of if statements that checks if the 'fin' and 'finBy' fields in the forms from index.php exist and sets variables that correspond to them existing or not.
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$listID = $_POST['listID'];
	
	if (array_key_exists('fin', $_POST)) {
		$complete = 1;
	} else {
		$complete = 0;
	}
	if (empty($_POST['finBy'])) {
		$finBy = null;
	} else {
		$finBy = $_POST['finBy'];
	}
	$listItem = $_POST['listItem'];
	
	//build url for api
	$url="http://3.229.178.148/api/task.php?listID=$listID";
	
	//encode json
	$data = array('completed'=>$complete,'taskName'=>$listItem,'taskDate'=>$finBy);
	$data_json = json_encode($data); 
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch); //body of the response
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); //gets the status code
	curl_close($ch);
	
	//if(status code = 204)	
	if($httpcode == 204){
		header("Location: index.php");		
	}else{
		//api errors (not http 204)
		header("Location: index.php?error=edit");
	}
}
?>
