<?php
	$thisPage = 'loginIndex';
	//session starts from lang.php

	//Check if file exist before include
	if (is_file('../include/lang.php')) {
		include '../include/lang.php';
	}
	else{
		// the file does not exist
		echo 'Language file not found, FATAL ERROR!';
		exit();
	}
		
	$getActionParameter = isset( $_GET['action'] ) ? $_GET['action'] : '';
	$getSessionUsername = isset( $_SESSION['Username'] ) ? $_SESSION['Username'] : '';
	
	if ( $getActionParameter != 'login' && $getActionParameter != 'logout' && !$getSessionUsername) {
		LoginSession();
		exit();			
	}
		
	switch ( $getActionParameter ) {
		case 'login':
			LoginSession();
			break;
		case 'logout':
			unset( $_SESSION['Username'] );
			header( 'Location: index.php' );
		default:
			echo '
				<div class="ui section divider"></div>
				<div class="ui center aligned grid">
					<div class="row">
						<h2 class="ui red header"><b>'.$_SESSION['Username'].'</b>, '.YouLoggedIn.'</h2>
					</div>
				</div>
				<div class="ui center aligned grid">
					<div class="four wide column">
						<a href="dashboard.php">'.Link1.'</a>
					</div>
					<div class="four wide column">
						<a href="index.php?action=logout">'.Link2.'</a>
					</div>
				</div>
				
				';
	}
	
	function LoginSession() {
		
		global $thisPage;
		if ( isset( $_POST['Login'] ) ) {
			
			//Check if file exist before include
			if (is_file('../include/config.php')) {
				include '../include/config.php';
			}
			else{
				// the file does not exist
				echo 'Database configuration file not found, FATAL ERROR!';
				exit();
			}
			
			$getSessionUsername = $_POST['Username'];
			$password = $_POST['Password'];
			
			$userQueryString = 'SELECT username, hashed_password FROM user_admin WHERE username=BINARY ?';
			$queryHandle = $databaseHandle->prepare($userQueryString);
			$queryHandle->bindParam(1,$getSessionUsername);
			$queryHandle->execute();
			$row = $queryHandle->fetch();
			
			if(password_verify($password,$row['hashed_password'])){
				$_SESSION['Username'] = $_POST['Username'];
				header( 'Location: dashboard.php' );
			} else {
				$loginError = true;
				//Check if file exist before include
				if (is_file('./templates/loginform.php')) {
					include './templates/loginform.php';
				}
				else{
					// the file does not exist
					echo 'Login form template file not found, FATAL ERROR!';
					exit();
				}
			}
		} else {
			//Check if file exist before include
			if (is_file('./templates/loginform.php')) {
				include './templates/loginform.php';
			}
			else{
				// the file does not exist
				echo 'Login form template file not found, FATAL ERROR!';
				exit();
			}
		}
	}
	
?>


