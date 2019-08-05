<?php
		include '../include/config.php';		
		$userName = "admin";
		$password = "password";
		$hashedPassword = password_hash($password,PASSWORD_DEFAULT);
		$queryAddUser = 'INSERT INTO user_admin (username, hashed_password) VALUES (?,?)';		
		$queryHandle = $databaseHandle->prepare($queryAddUser);
		$queryHandle->bindParam(1, $userName);
		$queryHandle->bindParam(2, $hashedPassword);
		$queryHandle->execute();
		echo "Default administrator account created with username: ".$userName." / password: ".$password;
?>