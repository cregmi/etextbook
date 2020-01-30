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
	
	if(isset($_POST['createBook'])){
		//Check if file exist before include
		if (is_file('../include/config.php')) {
			include '../include/config.php';
		}
		else{
			// the file does not exist
			echo 'Database configuration file not found, FATAL ERROR!';
			exit();
		}		

		require_once '../vendor/autoload.php';
		$bookName = $_POST['bookName'];
		$gradeSubject = $_POST['gradeSubject'];
		$bookContent = $_POST['bookContent'];
		
		// create book as PDF file, save the file and required XML file in filesystem, insert entry into databse		
		
		//creating instance for pdf 

		$mpdf = new \Mpdf\Mpdf();

		//create  pdf

		$textbookData = '';
		$textbookData = $textbookData.'<h1>'.$bookName.'</h1>';
		$textbookData = $textbookData.'<strong>Grade and Subject</strong>'.$gradeSubject.'<br/>';
		$textbookData = $textbookData.'<p>'.$bookContent.'</p>';
		//Writing PDF

		$mpdf->WriteHTML($textbookData);

		// output to browser

		$mpdf->Output($bookName.'.pdf', 'D');

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
<h1>Create New Textbook</h1>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
	Textbook Name:<input type="text" name="bookName" value="" placeholder="" required></br>
	Grade and Subject, separated by comma:<input type="Text" name="gradeSubject" value="" placeholder="" required></br>
	Number of chapters:<input type="number" id="chapterNum" name="numberOfChapters" value="" placeholder="" required></br>
	
	<div id='bookContent'>
			<textarea rows="20" cols="100" type="Textarea" name ="bookContent" value =""></textarea>
	</div>

	<button type="submit" name="createBook">Create Textbook</button>
</form>
<!--<script>
	const chapterCount = document.getElementById('chapNum');
	const chapterInput = document.getElementById('bookContent');

	chapterCount.addEventListener('change', createTextArea);

	function createTextArea(){
		chapterInput.innerHTML = '<textarea rows="20" cols="100" type="Textarea" name ="bookContent" value =""></textarea>';
	}
</script>-->
</body>
</html>
