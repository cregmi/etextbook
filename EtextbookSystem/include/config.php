<?php
	ini_set( 'display_errors', true );

	date_default_timezone_set( 'Europe/Helsinki' );

	define( 'DATABASE_DETAILS', 'mysql:host=localhost;dbname=library' );
	define( 'DATABASE_USERNAME', 'root' );
	define( 'DATABASE_PASSWORD', '' );

	function handleException( $exception ) 
	{
		echo 'DATABASE ERROR!!';
		var_dump($exception->getMessage());
		error_log( $exception->getMessage() );
	}

	set_exception_handler( 'handleException' );

	$databaseHandle = new PDO( DATABASE_DETAILS, DATABASE_USERNAME, DATABASE_PASSWORD );
	$databaseHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
?>