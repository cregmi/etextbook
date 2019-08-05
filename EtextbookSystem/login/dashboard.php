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
		
	//Check if file exist before include
	if (is_file('../include/config.php')) {
		include '../include/config.php';
	}
	else{
		// the file does not exist
		echo 'Database configuration file not found, FATAL ERROR!';
		exit();
	}
	
	$bookTitleOptions = array('Science','Mathematics','English','Nepali','Social Science');
	$bookTagOptions = array('Class 01','Class 02','Class 03','Class 04','Class 05','Class 06','Class 07','Class 08','Class 09','Class 10','Class 11','Class 12');
	$bookLanguageOptions = array('Nepali','English');
		
	$queryBookTitles = $databaseHandle->query('SELECT DISTINCT name FROM book');
	$queryBookTags = $databaseHandle->query('SELECT DISTINCT description_tag FROM book');
		
	$bookTitleOptionValues = array();
	while ($valueFromOneRow = $queryBookTitles->fetch()){
		$bookTitleOptionValues[] = $valueFromOneRow;
    }
		
	$bookTagOptionValues = array();
	while ($valueFromOneRow = $queryBookTags->fetch()){
		$bookTagOptionValues[] = $valueFromOneRow;
    }

	if(isset($_POST['TrackAddFile'])){
		$postedBookName = isset($_POST['bookname']) ? $_POST['bookname'] : FALSE;
		$postedBookTag = isset($_POST['booktag']) ? $_POST['booktag'] : FALSE;
		$postedBookLanguage = isset($_POST['booklanguage']) ? $_POST['booklanguage'] : FALSE;
		$postedFileName = isset($_FILES['pdffile']['name']) ? $_FILES['pdffile']['name'] : FALSE;
		$postedManifestFileName = isset($_FILES['xmlfile']['name']) ? $_FILES['xmlfile']['name'] : FALSE;
		
		if ($postedBookName && $postedBookTag && $postedBookLanguage && $postedFileName && $_FILES['pdffile']['error'] == 0){
			$duplicationQueryString = 'SELECT * FROM book WHERE name=? AND description_tag=? AND language_tag=?';
			$queryDuplication = $databaseHandle->prepare($duplicationQueryString);
			$queryDuplication->bindParam(1,$postedBookName);
			$queryDuplication->bindParam(2,$postedBookTag);
			$queryDuplication->bindParam(3,$postedBookLanguage);
			$queryDuplication->execute();
			
			if($queryDuplication->fetch()){
				$duplicationError = true;
			} else{
				$queryMaxBookId = $databaseHandle->query('SELECT MAX(id) AS max_id FROM book FOR UPDATE');
				$maxBookId = $queryMaxBookId->fetchObject();
				$newBookId = $maxBookId->max_id + 1;
				$folderUploadPdf = $_SERVER['DOCUMENT_ROOT'].'/upload/book/pdf/';
				$folderUploadImage = $_SERVER['DOCUMENT_ROOT'].'/upload/book/image/';
				$defaultImage = $_SERVER['DOCUMENT_ROOT'].'/upload/book/image/default.png';
					
				$newFileNamePdf = 'PdfForBookID-'.$newBookId.'.pdf';
				$NewFileNameImage = 'ImageForBookID-'.$newBookId.'.png';
					
				$filePathPdf = $folderUploadPdf.$newFileNamePdf;
				$filePathImage = $folderUploadImage.$NewFileNameImage;
				
				if ((move_uploaded_file($_FILES['pdffile']['tmp_name'], $filePathPdf))) {	
					$uploadSuccess = true;
										
					//Requires imagick extension to convert first page of pdf to image
					if (!extension_loaded('imagick')){
						copy($defaultImage, $filePathImage); //use default image if imagick not available
					}else {					
						$firstPage = $filePathPdf.'[0]';
						try {
							$im = new Imagick();
							$im->setResolution(200,200);
							$im->readimage($firstPage); 
							$im->setImageFormat('jpeg');    
							$im->writeImage($filePathImage); 
							$im->clear(); 
							$im->destroy();
							$imageCreationSuccess = true;
						} catch (ImagickException $e) {
							//var_dump($e);
							copy($defaultImage, $filePathImage); //use default image if imagick not working
						}					
					}
					
					$entryDateTime = date('Y-m-d H:i:s');
					$queryAddFile = 'INSERT INTO book (name, description_tag, language_tag, entry_date, pdf_file_name, image_file_name) VALUES (?, ?, ?, ?, ?, ?)';
					$queryHandle = $databaseHandle->prepare($queryAddFile);	
					$queryHandle->bindParam(1, $postedBookName);
					$queryHandle->bindParam(2, $postedBookTag);
					$queryHandle->bindParam(3, $postedBookLanguage);
					$queryHandle->bindParam(4, $entryDateTime);
					$queryHandle->bindParam(5, $newFileNamePdf);
					$queryHandle->bindParam(6, $NewFileNameImage);	
					$queryHandle->execute();
					$addedBookId = $databaseHandle->lastInsertId();
				} else {			
					$uploadError = true; 
				}
			}
		} else{	
			$uploadError = true;
		}
		
		if($postedManifestFileName!=FALSE && $_FILES['xmlfile']['error'] == 0){
			$folderUploadManifest = $_SERVER['DOCUMENT_ROOT'].'/upload/book/manifest/';
			$NewFileNameManifest = 'ManifestForBookID-'.$addedBookId.'.xml';
			$filePathManifest = $folderUploadManifest.$NewFileNameManifest;
			if ((move_uploaded_file($_FILES['xmlfile']['tmp_name'], $filePathManifest))){
				$uploadManifestSuccess = true;
				if (is_file($filePathManifest) && is_readable($filePathManifest)) {
					$xml=simplexml_load_file($filePathManifest) or die("Error: Cannot create object");
					foreach($xml->chapter as $chapter){					
						$bookId = $addedBookId;
						$chapterId = intval($chapter->id);
						$chapterStartPage = intval($chapter->startpage);
						$chapterEndPage = intval($chapter->endpage);
						$queryAddBookMap = 'INSERT INTO book_map (book_id, chapter_id, chapter_start_page, chapter_end_page) VALUES (?, ?, ?, ?)';
						$queryHandle = $databaseHandle->prepare($queryAddBookMap);
						$queryHandle->bindParam(1, $bookId);
						$queryHandle->bindParam(2, $chapterId);
						$queryHandle->bindParam(3, $chapterStartPage);
						$queryHandle->bindParam(4, $chapterEndPage);
						$queryHandle->execute();
					}					
				}
			}
		}
	}
	
	$rows = array();
	if (isset($_POST['TrackSearchFile'])){	
		$bookNameSearch = $_POST['selectname'];
		$bookTagSearch = $_POST['selecttag']; 
		if($bookNameSearch=='all' && $bookTagSearch=='all'){
			$querySearchFile = 'SELECT * FROM book';
		}else if($bookTagSearch=='all'){
			$querySearchFile = "SELECT * FROM book WHERE name='$bookNameSearch'";
		}else if($bookNameSearch=='all'){
			$querySearchFile = "SELECT * FROM book WHERE description_tag='$bookTagSearch'";
		} else {
			$querySearchFile = "SELECT * FROM book WHERE name='$bookNameSearch' AND description_tag='$bookTagSearch'";
		}
		
		$querySearchResults = $databaseHandle->query($querySearchFile);
		
		while ($valueFromOneRow = $querySearchResults->fetch())
		{
			$rows[] = $valueFromOneRow;
        }
		$searchCount = count($rows);
		if ($_COOKIE['language'] == 'ne'){
			$searchCount = numberEnNe($searchCount);
		}
	}
	
	if (isset($_POST['trackdelete'])){
		$bookid = $_POST['trackdelete'];
		$bookname = $_POST['trackbookname'];
		$booktag = $_POST['trackbooktag'];
		
		$deleteMapQuery = "DELETE from book_map WHERE book_id='$bookid'";
		$databaseHandle->exec($deleteMapQuery);
		
		$bookpdffile = $_SERVER['DOCUMENT_ROOT'].'/upload/book/pdf/'.$_POST['trackpdffile'];
		$bookimagefile = $_SERVER['DOCUMENT_ROOT'].'/upload/book/image/'.$_POST['trackimagefile'];
		$bookManifestFile = $_SERVER['DOCUMENT_ROOT'].'/upload/book/manifest/ManifestForBookID-'.$bookid.'.xml';;
		$delete_query = "DELETE from book WHERE id='$bookid'";
		$delete_count = $databaseHandle->exec($delete_query);
	
		if ($delete_count==1){
			if(file_exists($bookpdffile)){
				unlink($bookpdffile);
				$pdfDeleted = true;
			}
			if(file_exists($bookimagefile)){
				unlink($bookimagefile);
				$imageDeleted = true;		
			}
			if(file_exists($bookManifestFile)){
				unlink($bookManifestFile);
				$manifestDeleted = true;		
			}
			$databaseEntryDeleted = true;
		} else{
			$deleteError = true;
		}
	}
	
	function numberEnNe($numberEn){ // function to convert numbers from English to Nepali 
		$digitsEn = str_split($numberEn);
		$digitsArrayNe = array('०','१','२','३','४','५','६','७','८','९');
		$digitsNe = array();
		$numberNe = NULL;
		foreach ($digitsEn as $x){
			$digitsNe[] = $digitsArrayNe[$x];
		}
		foreach ($digitsNe as $y){
			$numberNe = $numberNe.$y; 
		}
		return $numberNe;
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
		<div class="ui section divider"></div>
		<div class="ui container"><h1 class="ui center aligned header" ><?=Name?></h1></div>
		<div class="ui four item menu">
		  <p class="item"><?=Message?> <?=$_SESSION['Username']?></p>
		  <a class="item" href = "index.php?action=logout"><?=Menu1?></a>
		  <a class="item" href = "../index.php" target="_blank" rel="noopener"><?=Menu2?></a>
		  <div class="ui simple dropdown item">
				<?=Menu3?>&nbsp;
				<i class="translate icon"></i>
				<div class="menu">
					<a href="<?=$_SERVER['PHP_SELF'].'?language=en'?>" class="item"><i class="us flag"></i><?=Language1?></a>
					<a href="<?=$_SERVER['PHP_SELF'].'?language=ne'?>" class="item"><i class="nepal flag"></i><?=Language2?></a>
				</div>
			</div>
		</div>
		<div class="ui section divider"></div>	
		<div class="ui center aligned grid">
			<div class="seven wide olive column">
				<form class = "ui form" action="" method="post" enctype="multipart/form-data">
					<h4 class="ui dividing header"><?=AddFormHeader?></h4>
					<div class="two fields">
						<div class="field">
							<label for="pdffile"><?=AddLabel04?></label>
							<input type="file" name="pdffile" id="pfile" accept=".pdf" required>
						</div>
						<div class="field">
							<label for="xmlfile"><?=AddLabel05?></label>
							<input type="file" name="xmlfile" id="xfile" accept=".xml" required>
						</div>
					</div>
					<div class = "three fields">
						<div class="field">	
							<label for="booktag"><?=AddLabel01?></label>
							<select class="ui fluid dropdown" name="booktag" id="btag" required>
								<?php 
									foreach($bookTagOptions as $bookTagOption){
											echo '<option value="'.str_replace(' ','_',$bookTagOption).'">'.$bookTagOption.'</option>';
									}
								?>
							</select>
						</div>					
						<div class="field">
							<label for="bookname"><?=AddLabel02?></label>
							<select class="ui fluid dropdown" name="bookname" id="bname" required>
								<?php 
									foreach($bookTitleOptions as $bookTitleOption){
											echo '<option value="'.str_replace(' ','_',$bookTitleOption).'">'.$bookTitleOption.'</option>';
									}
								?>		
							</select>
						</div>				
						<div class="field">	
							<label for="booktag"><?=AddLabel03?></label>
							<select class="ui fluid dropdown" name="booklanguage" id="blang" required>
								<?php 
									foreach($bookLanguageOptions as $bookLanguageOption){
											echo '<option value="'.$bookLanguageOption.'">'.$bookLanguageOption.'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<input type = "hidden" name="TrackAddFile" value="true">
					<div class="field">
						<input class="ui fluid submit button" type="submit" name="addbook" id="add" value="<?=AddButton?>" />			
					</div>
					
					
					<?php if ( isset( $uploadSuccess ) ) { ?>
					<div class="ui green dividing header">
						<div class="content">
							<i class="checkmark icon"></i><?=UploadSuccessMessage?>
							<?php 
								if ( isset( $imageCreationSuccess ) ) { 
									echo ', '.ImageCreationSuccessMessage;
								} else {
									echo ', '.ImageCreationErrorMessage;
								}
							?>
							<?php 
								if ( isset( $uploadManifestSuccess ) ) { 
									echo ', '.UploadManifestSuccessMessage;
								} else {
									echo ', '.UploadManifestErrorMessage;
								}
							?>
						</div>
					</div>
					<?php } ?>
					<?php if ( isset( $uploadError) ) { ?>
					<div class="ui red dividing header">
						<div class="content">
							<i class="attention icon"></i><?=UploadErrorMessage?>
						</div>
					</div>
					<?php } ?>
					<?php if ( isset( $duplicationError) ) { ?>
					<div class="ui red dividing header">
						<div class="content">
							<i class="attention icon"></i><?=DuplicationErrorMessage?>
						</div>
					</div>
					<?php } ?>

				</form>
			</div>
			
			<div class="ui five wide brown column">
				<form class="ui form" action="" method="post" enctype="application/x-www-form-urlencoded">
					<h4 class="ui dividing header"><?=SearchFormHeader?></h4>
					<div class="two fields">
						<div class="field">
							<label for="selectname"><?=SearchLabel01?></label>
							<select class="ui fluid dropdown" name="selectname" id="sname" >
								<option value="all">ALL</option>
								<?php foreach($bookTitleOptionValues as $bookTitleOptionValue){ ?>
								<option value="<?=$bookTitleOptionValue['name']?>"><?=$bookTitleOptionValue['name']?></option>
								<?php } ?>
							</select>	
						</div>
						<div class="field">
							<label for="selecttag"><?=SearchLabel02?></label>
							<select class="ui fluid dropdown" name="selecttag" id="stag" >
								<option value="all">ALL</option>
								<?php foreach($bookTagOptionValues as $bookTagOptionValue){ ?>
								<option value="<?=$bookTagOptionValue['description_tag']?>"><?=$bookTagOptionValue['description_tag']?></option>
								<?php } ?>
							</select>	
						</div>
					</div>
					<input type = "hidden" name="TrackSearchFile" value="true">
					<div class="field">
						<input class="ui fluid submit button" type="submit" name="searchbook" id="search" value="<?=SearchButton?>" />
					</div>			
						<?php if ( isset( $searchCount ) ) { ?>
						<div><p><?=$searchCount?> <?=SearchMessage?><p></div>
						<?php } ?>				
				
				</form>			
			</div>
		</div>
		<div class="ui section divider"></div>		
		<?php if ( count($rows)>0 ) { ?>
		<div class = "ui center aligned middle aligned grid">
			<div class="eight column blue row">
				<div class="column"><?=SearchResultColumn01?></div>
				<div class="column"><?=SearchResultColumn02?></div>
				<div class="column"><?=SearchResultColumn03?></div>
				<div class="column"><?=SearchResultColumn04?></div>
				<div class="column"><?=SearchResultColumn05?></div>
				<div class="column"><?=SearchResultColumn06?></div>
				<div class="column"><?=Menu3?></div>
				<div class="column"></div>
			</div>
		
			<?php foreach($rows as $row){ ?>
			<div class="eight column row">
				<div class="column"><?=$row['id']?></div>
				<div class="column"><a href="../read.php?id=<?=$row['id']?>" target="_blank"><?=$row['name']?></a></div>
				<div class="column"><a href="./link-content.php?id=<?=$row['id']?>" target="_blank"><?=$row['pdf_file_name']?></a></div>
				<div class="column"><?=$row['description_tag']?></div>
				<div class="column"><?=$row['entry_date']?></div>
				<div class="column"><a href="../upload/book/image/<?=$row['image_file_name']?>" target="_blank">Open</a></div>
				<div class="column"><?=$row['language_tag']?></div>
				<div class="column">
					<form action="" method="post" enctype="application/x-www-form-urlencoded">
						<input class="ui fluid submit button" type="submit" name="deletebook" id="search" value="Delete"/>
						<input type="hidden" name="trackdelete" value="<?=$row['id']?>"/>
						<input type="hidden" name="trackbookname" value="<?=$row['name']?>"/>
						<input type="hidden" name="trackbooktag" value="<?=$row['description_tag']?>"/>
						<input type="hidden" name="trackpdffile" value="<?=$row['pdf_file_name']?>"/>
						<input type="hidden" name="trackimagefile" value="<?=$row['image_file_name']?>"/>
					</form>	
				</div>
				
			</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<div class="ui container">
			<?php if ( isset( $deleteError) ) { ?>
			<div class="ui red center aligned dividing header">
				<div class="content">
					<i class="attention icon"></i><?=DeleteErrorMessage?>
				</div>
			</div>
			<?php } ?>
			<?php if ( isset( $pdfDeleted) || isset($imageDeleted) || isset($manifestDeleted) ) { ?>
				<div class="ui green center aligned dividing header">
					<div class="content">
						<i class="checkmark icon"></i><?=DeleteSuccessMessage?>
					</div>
				</div>
			<?php } ?>
		</div>
		
		<div class="ui section divider"></div>
		
		<a href="add-lesson.php" target="_blank">Add Lesson</a> |
		<a href="add-exercise.php" target="_blank">Add Exercise</a> |
		<a href="add-user.php" target="_blank">Add User</a>
		
		<h5 class="ui right aligned header" id="siteby">
			&copy;&nbsp;<a href="mailto:info@textbookslibrary.com" target="mail"><?=CopyrightText?></a>
		</h5>
	</body>
</html>


