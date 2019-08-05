<?php
	$thisPage = 'dashboard';

	// Check if file exist before include

	if (is_file('../include/lang.php')) {
		include '../include/lang.php';

	}
	else {

		// the file does not exist

		echo 'Language file not found, FATAL ERROR!';
		exit();
	}

	if (!isset($_SESSION['Username'])) {
		exit('
			<div class="ui section divider"></div>
			<div class="ui middle aligned center aligned grid">
				<div class="column">
					<h2 class="ui red header">' . NoSessionMessage . ', <u><a href="index.php">' . NoSessionLink . '</a></u></h2>
				</div>
			</div>');
	}	
	
	// Check if file exist before include
	if (is_file('../include/config.php')) {
		include '../include/config.php';

	}
	else {
		// the file does not exist
		echo 'Database configuration file not found, FATAL ERROR!';
		exit();
	}
	
	$queryLesson = $databaseHandle->query('SELECT * FROM lesson');
	$lesson = array();
	while($row = $queryLesson->fetch()){
		$lesson[] = $row;
	}

	if (isset($_POST['submit'])) {
		$queryMaxLessonId = $databaseHandle->query('SELECT MAX(id) AS max_id FROM lesson FOR UPDATE');
		$maxLessonId = $queryMaxLessonId->fetchObject();
		$newLessonId = $maxLessonId->max_id + 1;
		$entryDate = date('Y-m-d H:i:s');
		$contentFilename = 'Lesson_Id-' . $newLessonId;
		$contentLanguage = $_POST['contentLang'];
		$contentDescriptionTag = $_POST['contentTag'];
		$queryAddLesson = 'INSERT INTO lesson (entry_date, content_file_name, content_language, content_description_tag) VALUES (?,?,?,?)';
		$queryHandle = $databaseHandle->prepare($queryAddLesson);
		$queryHandle->bindParam(1, $entryDate);
		$queryHandle->bindParam(2, $contentFilename);
		$queryHandle->bindParam(3, $contentLanguage);
		$queryHandle->bindParam(4, $contentDescriptionTag);
		$queryHandle->execute();
		
		$lessonFolderPath = $_SERVER['DOCUMENT_ROOT'] . '/upload/lesson/';
		$lessonFolder = $contentFilename;
		$filePath = $lessonFolderPath . $lessonFolder;
		if (!file_exists($filePath)) {
			mkdir($filePath, 0777, true);
		}

		$uploadedFiles = 0;
		$total = count($_FILES['upload']['name']);
		for ($i = 0; $i < $total; $i++) {
			$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
			if ($tmpFilePath != "") {
				if (move_uploaded_file($tmpFilePath, $filePath . '/' . $_FILES['upload']['name'][$i])) {
					$uploadedFiles++;
					echo $_FILES['upload']['name'][$i] . ' uploaded</br>';
				}
				else {
					echo $_FILES['upload']['name'][$i] . ' NOT uploaded</br>';
				}
			}
		}

		echo $uploadedFiles . ' files uploaded, ' . '<b>Record added to database.</b></br>';
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
<h1 class = "ui center aligned header">Add Lesson Files</h1>
<div class="ui container">
	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
		<input type="text" name="contentLang" value="" placeholder="Content Language" required>
		<input type="text" name="contentTag" value="" placeholder="Content tag" required>
		<label for="upload">Add files</label>
		<input type="file" name="upload[]" id="expFiles" multiple required>
		<button type="submit" name="submit">Submit</button>
	</form>
</div>
<div class="ui section divider"></div>
<?php if(count($lesson)>0) {?>
		<h1 class = "ui center aligned header">Available lessons</h1>
		<div class = "ui center aligned middle aligned grid">
			<div class="five column blue row">
				<div class="column">ID</div>
				<div class="column">Entry date</div>
				<div class="column">File path</div>
				<div class="column">Language</div>
				<div class="column">Description</div>
			</div>
		
			<?php foreach($lesson as $l){ ?>
			<div class="five column row">
				<div class="column"><?=$l['id']?></div>
				<div class="column"><?=$l['entry_date']?></div>
				<div class="column"><a href="../upload/lesson/<?=$l['content_file_name']?>" target="_blank"><?=$l['content_file_name']?></a></div>
				<div class="column"><?=$l['content_language']?></div>
				<div class="column"><?=$l['content_description_tag']?></div>
			</div>
			<?php } ?>
		</div>
<?php }?>
</body>
</html>


