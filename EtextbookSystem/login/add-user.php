<?php
	$thisPage='dashboard';
		
	//Check if file exist before include
	if (is_file('../include/lang.php')) {
		include '../include/lang.php';
	}
	else{
		// the file does not exist
		echo 'Language file not found, FATAL ERROR!';
		exit();
	}
	
	if ( !isset($_SESSION['Username']) ) {
		exit('
			<div class="ui section divider"></div>
			<div class="ui middle aligned center aligned grid">
				<div class="column">
					<h2 class="ui red header">'.NoSessionMessage.', <u><a href="index.php">'.NoSessionLink.'</a></u></h2>
				</div>
			</div>');
	}
	
	if(isset($_POST['submit'])){
		//Check if file exist before include
		if (is_file('../include/config.php')) {
			include '../include/config.php';
		}
		else{
			// the file does not exist
			echo 'Database configuration file not found, FATAL ERROR!';
			exit();
		}		

		$userName = $_POST['user_name'];
		$password = $_POST['password'];

		$hashedPassword = password_hash($password,PASSWORD_DEFAULT);
		
		$queryAddUser = 'INSERT INTO user_admin (username, hashed_password) VALUES (?,?)';		
		$queryHandle = $databaseHandle->prepare($queryAddUser);
		$queryHandle->bindParam(1, $userName);
		$queryHandle->bindParam(2, $hashedPassword);
		$queryHandle->execute();
		echo "Success";
	}	
?>
	<html>
		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="description" content="online interactive library of school textbooks">
			<meta name="author" content="Chandan Regmi, email: info@textbookslibrary.com">
			<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
			<link rel="stylesheet" type="text/css" href="../vendor/semantic.min.css">
			<title><?=Title?></title>
			<style type="text/css">
				body {
					background-color: #DADADA;
				}
			</style>		
		</head>
	<body>
<h1>Registration Form</h1>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
	<input type="text" name="user_name" value="" placeholder="User Name" required>
	<input type="password" name="password" value="" placeholder="Password" required>
	<button type="submit" name="submit">Submit</button>
</form>

</body>
</html>
