<?php
	// the variable $thisPage should be set in the file from where this script is included
	if(!isset($thisPage)){
		exit('Direct access to language file not allowed!!');
	} else {
		if($thisPage == 'loginIndex' || $thisPage == 'dashboard'){
			session_start(); //start session for required page from here 
		}
		$callingPage = $thisPage;
	}

	if ( !empty($_GET['language']) ) {
		setcookie('language', $_GET['language'], time()+720000, '/');
		$lang = $_GET['language'];
	} else if (!empty($_COOKIE['language'])) {
			$lang=htmlspecialchars($_COOKIE['language']);
	} else {
			setcookie('language', 'en', time()+720000, '/');
			$lang = 'en';	
	}	
	
	//set absolute path for $dataFile, filename for the CSV files holding translation string 	
	$dataFile = $_SERVER['DOCUMENT_ROOT'].'/l10n/'.$callingPage.'data_'.$lang.'.csv';

	//following code defines a set of constant for every page 
	//for example: define( "Title", "My page Title")
	if (file_exists($dataFile) &&($handle = fopen($dataFile, 'r')) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
			define( $data[0],$data[1] );
		}
		fclose($handle);
	} else{
		echo 'Language contents file not found, FATAL ERROR!!';
		exit();
	}

?>