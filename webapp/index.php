<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == "GET") {

	//build url for api
	$url="http://3.229.178.148/api/tasks.php";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	//curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch); //body of the response
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); //gets the status code
	curl_close($ch);
	
	//if(status code = 204)	
	if($httpcode == 200){
		$json = file_get_contents('http://3.229.178.148/api/tasks.php');
		$json_array = json_decode($json, true);
		//foreach ($json_array as $task){
		//	echo $task['listID']. "<br>";
		//}
		
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="description" content="doIT">
		<meta name="author" content="Russell Thackston">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>doIT</title>

		<link rel="stylesheet" href="style/style.css">
		
		<link href="https://fonts.googleapis.com/css?family=Chilanka%7COrbitron&display=swap" rel="stylesheet">		

	</head>


	<body>
		<a href="index.php"><h1 id="siteName">doIT</h1></a>
		<hr>

			<?php if(isset($json_array)) { ?>
				<?php foreach($json_array as $item){ ?>
					<div class="list">
						<form method="POST" action="edit.php" style="display: inline-block">
							<input type="hidden" 	name="listID" value="<?php echo $item["listID"];?>" >
							<input type="checkbox"	name="fin" <?php if($item["completed"]=='1'){echo "checked='checked'";} ?> >
							<input type="text" 	name="listItem" size="50" value="<?php echo $item["taskName"];?>" maxlength="100" >
							<span>by:</span>
							<input type="date" 	name="finBy" value="<?php if($item['taskDate']=='0000-00-00'){echo '';} else {echo $item['taskDate'];} ?>" >
							<input type="submit" 	name="submitEdit" value="&check;" >
						</form>
						<form method="POST" action="delete.php" style="display: inline-block">
							<input type="hidden" name="listID" value="<?php echo $item["listID"];?>" >
							<input type="submit" name="submitDelete" value="&times;" >
						</form>
					</div>
					<?php } ?>
			<?php } ?>

			<div class="list">
				<form  method="POST" action="add.php">
					<input type="checkbox" name="fin" value="done">
					<input type="text" name="listItem" size="50">
					<span>by:</span>
					<input type="date" id="finDate" name="finBy">
					<input type="submit" value="&#43;">
				</form>
			</div>
			
		<!--	<?php //if ($dbconnecterror) { ?>
			<div class="error">
				Uh oh! There was an error connecting to the database. Please check your connection settings and try again.
			</div>
			<?php// } ?>
			<?php //if ($dbReadError) { ?>
			<div class="error">
				Uh oh! There was an error reading the to do list from the database. Please check your connection settings and try again.
			</div>
			<?php //} ?> -->
			<?php if (array_key_exists('error', $_GET)) { ?>
				<?php if ($_GET['error'] == 'add') { ?>
				<div class="error">
					Uh oh! There was an error adding your to do item. Please try again later.
				</div>
				<?php } ?>
				<?php if ($_GET['error'] == 'delete') { ?>
				<div class="error">
					Uh oh! There was an error deleting your to do item. Please try again later.
				</div>
				<?php } ?>
				<?php if ($_GET['error'] == 'edit') { ?>
				<div class="error">
					Uh oh! There was an error updating your to do item. Please try again later.
				</div>
				<?php } ?>
			<?php } ?>
	</body>
</html>
<?php
}else{
	//api errors (not http 204)
	//header("Location: index.php?error");
	echo "error";
}
}
?>