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

//update a task
if ($_SERVER['REQUEST_METHOD'] == "PUT") {

	if(array_key_exists('listID',$_GET)){	
		$listID = $_GET['listID'];
	} else {
		http_response_code(400);
		echo "no listID in put";
		exit();
	}
	//decoding the json body from request
	$json = file_get_contents('php://input');
	$task = json_decode($json, true);
	//echo $json;
	//var_dump($task);
	//exit();
	
	//data validation
	if (array_key_exists('completed', $task)) {
		$completed = $task["completed"];
	} else {
		http_response_code(400);
		echo "missing boolean in put";
		exit();
	}
	if (array_key_exists('taskName', $task)) {
		$taskName = $task["taskName"];
	} else {
		http_response_code(400);
		echo "invalid task name in put";
		exit();
	}
	if (array_key_exists('taskDate', $task)) {
		$taskDate = $task["taskDate"];
	} else {
		http_response_code(400);
		echo "invalid date in put";
		exit();
	}
	
	
	
	if (!$dbconnecterror) {
		try {
			$sql = "UPDATE doList SET complete=:complete, listItem=:listItem, finishDate=:finishDate WHERE listID=:listID";
			$stmt = $dbh->prepare($sql);			
			$stmt->bindParam(":complete", $completed);
			$stmt->bindParam(":listItem", $taskName);
			$stmt->bindParam(":finishDate", $taskDate);
			$stmt->bindParam(":listID", $listID);
			$response = $stmt->execute();
			http_response_code(204);
			
		} catch (PDOException $e) {
			http_response_code(504);
			echo "bad sql in put";
			exit();
			
		}	
	} else {
		http_response_code(504);
		echo "bad database in PUT";
		exit();
	} 
	
//create a task
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

	//decoding the json body from request
	$json = file_get_contents('php://input');
	$task = json_decode($json, true);
	//echo $json;
	//var_dump($task);
	//exit();
	if (array_key_exists('completed', $task) && $task['completed'] == 1) {
		$completed = 1;
	} else {
		$completed = 0;
	}
	if (empty($task['taskDate'])) {
		$taskDate = null;
	} else {
		$taskDate = $task['taskDate'];
	}
	if (!$dbconnecterror) {
		try {
			$sql = "INSERT INTO doList (complete, listItem, finishDate) VALUES (:complete, :listItem, :finishDate)";
			$stmt = $dbh->prepare($sql);			
			$stmt->bindParam(":complete", $completed);
			$stmt->bindParam(":listItem", $task['taskName']);
			$stmt->bindParam(":finishDate", $taskDate);
			$response = $stmt->execute();	
			http_response_code(201);
			echo "created new taske with the folowing values: taskName = " . $task['taskName'] . " taskDate = " . $taskDate . " Completed boolean = " . $completed;
			
		} catch (PDOException $e) {
			http_response_code(504);
			echo "bad sql in post";
			exit();
		}	
	} else {
		http_response_code(504);
		echo "bad database in delete";
		exit();
	}
	
//delete a task
} else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
	if (!$dbconnecterror) {
		try {
			$sql = "DELETE FROM doList where listID = :listID";
			$stmt = $dbh->prepare($sql);			
			$stmt->bindParam(":listID", $_GET['listID']);
		
			$response = $stmt->execute();
				http_response_code(204);
			
		} catch (PDOException $e) {
			http_response_code(504);
			echo "bad sql in delete";
			exit();
		}	
	} else {
		http_response_code(504);
		echo "bad database in delete";
		exit();
	}
//view a task
}else if ($_SERVER['REQUEST_METHOD'] == "GET"){
	if(array_key_exists('listID',$_GET)){	
		$listID = $_GET['listID'];
	} else {
		http_response_code(400);
		echo "no listID in GET";
		exit();
	}

	if (!$dbconnecterror) {
	
	try {
		$sql = "SELECT * FROM doList WHERE listID = :listID";
		$stmt = $dbh->prepare($sql);
		$stmt->bindParam(":listID", $listID);
		$stmt->execute();
		$result = $stmt->fetch();
		
		//validates whether the task exists in the database or not. if it does exists, outputs the information to the page.
		if (!empty($result['listID'])){
			http_response_code(200);
			echo "listID = " . $result['listID'] . " taskName = " . $result['listItem'] . " taskDate = " . $result['finishDate'] . " Completed boolean = " . $result['complete'];
		}else{
			http_response_code(404);
			echo "page not found";
		}
		
	} catch (PDOException $e) {
		http_response_code(504);
		echo "bad sql in get";
		exit();
	}
	}else {
		http_response_code(504);
		echo "bad database in get";
		exit();
	}
	
}else {
	http_response_code(405);
	echo "request is not a GET, PUT, POST, or a DELETE request";
	exit();
}

