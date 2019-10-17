<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Declare the credentials to the database

$dbconnecterror = FALSE;
$dbh = NULL;

require_once 'credentials.php';

try{
	
	$conn_string = "mysql:host=".$dbserver.";dbname=".$db;
	
	$dbh= new PDO($conn_string, $dbusername, $dbpassword);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
}catch(Exception $e){
	//$dbconnecterror = TRUE;
	http_response_code(504);
	echo "database issues were encountered";
	exit();
}

//view tasks
if ($_SERVER['REQUEST_METHOD'] == "GET") {
	
	if (!$dbconnecterror) {
		try {
			$sql = "SELECT * FROM doList";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll();
			http_response_code(200);
			
		} catch (PDOException $e) {
			http_response_code(504);
			echo "bad sql in get";
			exit();
			
		}	
	} else {
		http_response_code(504);
		echo "bad database in GET";
		exit();
	} 
	
	foreach($result as $item){
	$data[] = array('listID' => $item["listID"],'completed'=>$item["complete"],'taskName'=>$item["listItem"],'taskDate'=>$item['finishDate']);
	
	}
	
	$data_json = json_encode($data); 
	
	echo $data_json;
}else {
	http_response_code(405);
	echo "request is not a GET request";
	exit();
}

