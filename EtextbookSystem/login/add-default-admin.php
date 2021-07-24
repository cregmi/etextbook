<?php
		require_once('../include/config.php');
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
			
			// Creating necessary upload folders ...
			$lessonFolder = $_SERVER['DOCUMENT_ROOT'] . '/upload/lesson/';
			$exerciseFolder = $_SERVER['DOCUMENT_ROOT'] . '/upload/exercise/';
			$pdfFolder = $_SERVER['DOCUMENT_ROOT'] . '/upload/book/pdf/';
			$manifestFolder = $_SERVER['DOCUMENT_ROOT'] . '/upload/book/manifest/';
			mkdir($lessonFolder);
			mkdir($exerciseFolder);
			mkdir($pdfFolder);
			mkdir($manifestFolder);
			echo "</br>Necessary upload folders created - ../upload/lesson/, ../upload/exercise/, ../upload/book/pdf/, ../upload/book/manifest/"
				
			echo "</br><a href='./index.php'>Go to Login Page</a>";
		}
		else
		{
			exit("System already has valid admin/s to login, <a href='./index.php'>Go to Login Page</a>");
		}

?>
