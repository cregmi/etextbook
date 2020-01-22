<?php
		include '../include/config.php';
		$userEntries = $databaseHandle->query('SELECT COUNT(*) AS num_rows FROM user_admin'); 
		$rowCount = $userEntries->fetchColumn();		
	
		if($rowCount == 0){
			$userName = "admin";
			$password = "password";
			$hashedPassword = password_hash($password,PASSWORD_DEFAULT);
			$queryAddUser = 'INSERT INTO user_admin (username, hashed_password) VALUES (?,?)';		
			$queryHandle = $databaseHandle->prepare($queryAddUser);
			$queryHandle->bindParam(1, $userName);
			$queryHandle->bindParam(2, $hashedPassword);
			$queryHandle->execute();
			echo "Default administrator account created with username: ".$userName." / password: ".$password;
			echo "</br><a href='./index.php'>Go to Login Page</a>";
		}
		else
		{
			exit("System already has valid admin/s to login, <a href='./index.php'>Go to Login Page</a>");
		}

?>
