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
	
	if(!isset($_GET['id']) || $_GET['id']==null){
		exit("Identifier Missing");
	}
	
	//Check if file exist before include
	if (is_file('../include/config.php')) {
		include '../include/config.php';
	}
	else{
		// the file does not exist
		echo 'Database configuration file not found, FATAL ERROR!';
		exit();
	}
	
	$bookId = intval($_GET['id']);
	
	$bookQueryString = "SELECT * FROM book WHERE id=?";
	$bookQuery = $databaseHandle->prepare($bookQueryString);
	$bookQuery->bindParam(1, $bookId, PDO::PARAM_INT);
	$bookQuery->execute();
	$bookRecordAll = $bookQuery->fetch();
	if(!$bookRecordAll){
		exit('No book exist for given ID');
	}
	
	$bookMapQueryString = "SELECT * FROM book_map WHERE book_id=?";
	$bookMapQuery = $databaseHandle->prepare($bookMapQueryString);
	$bookMapQuery->bindParam(1, $bookId, PDO::PARAM_INT);
	$bookMapQuery->execute();
	$bookMap = array();
	while($row = $bookMapQuery->fetch()){
		$bookMap[] = $row;
	}
	
	$lessonQueryString = "SELECT * FROM lesson";
	$lessonQuery = $databaseHandle->prepare($lessonQueryString);
	$lessonQuery->execute();
	$lesson = array();
	while($row = $lessonQuery->fetch()){
		$lesson[] = $row;
	}
	
	$exerciseQueryString = "SELECT * FROM exercise";
	$exerciseQuery = $databaseHandle->prepare($exerciseQueryString);
	$exerciseQuery->execute();
	$exercise = array();
	while($row = $exerciseQuery->fetch()){
		$exercise[] = $row;
	}
	
	if (isset($_POST['updateMapLesson'])){
		$chapterId = intval($_POST['chapterId']);					
		$lessonId = intval($_POST['lessonId']);	
		if($lessonId=='NULL'){$lessonId=NULL;}			
		$queryUpdateMap = 'UPDATE book_map SET lesson_id = ? WHERE book_id = ? AND chapter_id = ?';
		$queryHandle = $databaseHandle->prepare($queryUpdateMap);
		$queryHandle->bindParam(1, $lessonId);
		$queryHandle->bindParam(2, $bookId);
		$queryHandle->bindParam(3, $chapterId);	
		$queryHandle->execute();
		header("Refresh:0");
	}
	
	if (isset($_POST['updateMapExercise'])){
		$chapterId = intval($_POST['chapterId']);
		$exerciseId = intval($_POST['exerciseId']);
		if($exerciseId=='NULL'){$exerciseId=NULL;}			
		$queryUpdateMap = 'UPDATE book_map SET exercise_id = ? WHERE book_id = ? AND chapter_id = ?';
		$queryHandle = $databaseHandle->prepare($queryUpdateMap);
		$queryHandle->bindParam(1, $exerciseId);
		$queryHandle->bindParam(2, $bookId);
		$queryHandle->bindParam(3, $chapterId);	
		$queryHandle->execute();
		header("Refresh:0");
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
	
<h1 class = "ui center aligned header">Book Map for Book ID <?=$bookId?></h1>
<div class="ui container">
	<div class="ui grid" style="background-color: #2185D0;color: #FFFFFF;">
		<div class="six wide column">
			<div class="ui five column grid">
				<div class="column">Chapter ID</div>
				<div class="column">Start Page</div>
				<div class="column">End Page</div>
				<div class="column">Page for lesson</div>
				<div class="column">Page for exercise</div>
			</div>
		</div>
		<div class="ten wide column">
			<div class="ui five column grid">
				<div class="column">lesson ID</div>
				<div class="column">Set new</div>
				<div class="column">exercise ID</div>
				<div class="column">Set new</div>
			</div>
		</div>
	</div>
		
	<?php foreach ($bookMap as $map){?>
	<div class="ui grid">
		<div class="six wide column">	
			<div class="ui five column grid">
				<div class="column"><?=$map['chapter_id']?></div>
				<div class="column"><?=$map['chapter_start_page']?></div>
				<div class="column"><?=$map['chapter_end_page']?></div>
				<div class="column"><?=$map['chapter_end_page']?></div>
				<div class="column"><?=$map['chapter_end_page']?></div>
			</div>
		</div>
		
		<div class="ten wide column">
			<form class="ui form" action="" method="post" enctype="application/x-www-form-urlencoded">
				<div class="five fields">
					<div class="field"><?php if($map['lesson_id']==NULL){echo 'Not set';}else{echo $map['lesson_id'];}?></div>
					<div class="field">
						<select name="lessonId">
							<option>NULL</option>
							<?php 
								foreach ($lesson as $l){
									echo '<option>'.$l['id'].'</option>';
								}
							 ?>
						</select>
						<div class="field"><input type="submit" name="updateMapLesson" value="Update"/></div>
						<input type="hidden" name="chapterId" value="<?=$map['chapter_id']?>"/>
					</div>
					<div class="field"><?php if($map['exercise_id']==NULL){echo 'Not set';}else{echo $map['exercise_id'];}?></div>
					<div class="field">
						<select name="exerciseId">
							<option>NULL</option>
							<?php 
								foreach ($exercise as $e){
									echo '<option>'.$e['id'].'</option>';
								}
							 ?>
						</select>
						<div class="field"><input type="submit" name="updateMapExercise" value="Update"/></div>
						<input type="hidden" name="chapterId" value="<?=$map['chapter_id']?>"/>
					</div>
				</div>
			</form>
		</div>
	</div>			
	<?php } ?>
</div>
</body>
</html>






